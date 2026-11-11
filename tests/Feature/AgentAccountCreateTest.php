<?php

namespace Tests\Feature;

use App\Livewire\Agent\AccountCreate;
use App\Models\Accounts;
use App\Models\Group;
use App\Models\User;
use App\Services\AccountProvisioningService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class AgentAccountCreateTest extends TestCase
{
    private User $agent;
    private Group $group;

    protected function setUp(): void
    {
        parent::setUp();
        // Never run migrations, provision VPN peers, or charge the application's real wallet.
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            foreach (['name', 'username', 'email', 'phone', 'password', 'role'] as $field) {
                $t->string($field)->nullable();
            }
            $t->integer('creator')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('groups', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->boolean('is_enabled')->default(true);
            $t->integer('price_reseler')->default(100);
            $t->integer('multi_login')->nullable();
            $t->timestamps();
        });
        Schema::create('accounts', function (Blueprint $t) {
            $t->id();
            $t->string('username');
        });
        Schema::create('agent_hidden_groups', function (Blueprint $t) {
            $t->integer('agent_id');
            $t->integer('group_id');
        });
        Schema::create('nas', function (Blueprint $t) {
            $t->id();
            $t->boolean('is_enabled');
            $t->text('server_type');
        });
        Schema::create('financial', function (Blueprint $t) {
            $t->id();
            $t->integer('for');
            $t->boolean('approved');
            $t->string('type');
            $t->integer('price');
        });
        $this->agent = User::create(['name' => 'نماینده تست', 'role' => 'agent']);
        $this->group = Group::create(['name' => 'L2TP Test', 'multi_login' => 3]);
        DB::table('financial')->insert(['for' => $this->agent->id, 'approved' => 1, 'type' => 'plus', 'price' => 100000]);
        $this->actingAs($this->agent);
    }

    private function form()
    {
        return Livewire::test(AccountCreate::class)
            ->call('setServiceGroup', 'l2tp_cisco')
            ->set('group_id', $this->group->id)
            ->set('username', 'test_user')
            ->set('password', '123456');
    }

    private function fakeProvisioning(int $count = 1)
    {
        // Real preparation; fake final provisioning/payment only.
        $service = Mockery::mock(AccountProvisioningService::class)->makePartial();
        $service->shouldReceive('createFullAccount')->times($count)->andReturn(
            (new Accounts)->forceFill(['id' => 42, 'username' => 'test_user'])
        );
        $this->app->instance(AccountProvisioningService::class, $service);
        return $service;
    }

    public function test_preparation_preserves_plan_simultaneous_login_limit(): void
    {
        $data = (new AccountProvisioningService)->prepareAccountData(
            $this->group, $this->agent, null, ['service_group' => 'l2tp_cisco']
        );
        $this->assertSame(3, $data['configData']['multi_login']);
        $this->group->multi_login = null;
        $data = (new AccountProvisioningService)->prepareAccountData(
            $this->group, $this->agent, null, ['service_group' => 'l2tp_cisco']
        );
        $this->assertSame(1, $data['configData']['multi_login']);
    }

    public function test_optional_email_does_not_block_single_issuance_and_result_has_account_link(): void
    {
        $this->fakeProvisioning();
        $this->form()->set('newCustomerName', 'مشتری تست')->call('createAccounts')
            ->assertHasNoErrors()
            ->assertSet('isSuccessModalOpen', true)
            ->assertSet('createdAccountsList.0.id', 42)
            ->assertSeeHtml(route('reseller.accounts.show', 42));
    }

    public function test_invalid_email_is_visible_in_the_form(): void
    {
        $this->form()->set('newCustomerName', 'مشتری تست')->set('newCustomerEmail', 'invalid')
            ->call('createAccounts')
            ->assertHasErrors(['newCustomerEmail' => 'email'])
            ->assertSee('فرمت ایمیل صحیح نیست.')
            ->assertSee('صدور انجام نشد');
    }

    public function test_bulk_does_not_validate_hidden_customer_fields(): void
    {
        $this->fakeProvisioning(2);
        $this->form()->call('setCreationType', 'bulk')->set('bulkCount', 2)
            ->call('createAccounts')->assertHasNoErrors()->assertSet('isSuccessModalOpen', true);
        $this->assertTrue(User::where('creator', $this->agent->id)
            ->where('email', 'archive_'.$this->agent->id.'@local.system')->exists());
    }

    public function test_runtime_errors_show_failure_and_rollback_archive_creation(): void
    {
        $service = Mockery::mock(AccountProvisioningService::class)->makePartial();
        $service->shouldReceive('createFullAccount')->once()->andThrow(new \Error('Test provisioning failure'));
        $this->app->instance(AccountProvisioningService::class, $service);
        $this->form()->set('customer_id', 'me')->call('createAccounts')
            ->assertSet('isErrorModalOpen', true)
            ->assertSet('isSuccessModalOpen', false)
            ->assertSee('Test provisioning failure');
        $this->assertSame(1, User::count());
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_insufficient_balance_is_visible_without_provisioning(): void
    {
        DB::table('financial')->update(['price' => 0]);
        $this->form()->set('customer_id', 'me')->call('createAccounts')
            ->assertSet('isErrorModalOpen', true)->assertSee('موجودی کیف پول شما');
    }

    public function test_plan_search_matches_substrings(): void
    {
        $this->form()->set('searchPlan', 'Test')->assertSee('L2TP Test');
    }
}
