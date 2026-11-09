<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use App\View\Components\Ui\Announcements;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Tests\TestCase;

class UiPresentationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Small fixtures in PHPUnit's in-memory SQLite DB; no application migrations or real DB.
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('sqlite', config('database.default'));
        Cache::put('site_setting_SITE_TITLE', 'برند آزمایشی');
        Cache::put('site_setting_SITE_LOGO', '');
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('target');
            $table->boolean('is_active');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('custom_domain')->nullable();
            $table->string('domain_status')->nullable();
        });
        Schema::create('financial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('for');
            $table->boolean('approved');
            $table->string('type');
            $table->bigInteger('price');
        });
        Schema::create('store_orders', function (Blueprint $table) {
            $table->id();
            $table->string('status');
        });
    }

    private function signInAs(string $role): void
    {
        $user = new User;
        $user->forceFill(['id' => 1, 'name' => 'آزمایش', 'role' => $role, 'balance' => 120000]);
        $user->setRelation('parentAgent', null);
        $this->actingAs($user);
    }

    public function test_shared_shell_is_rtl_accessible_and_has_no_fake_status_links(): void
    {
        $html = view('layouts.partials.site-shell', ['brand_name' => 'آزمایش', 'slot' => new HtmlString('<h1>محتوا</h1>')])->render();
        $this->assertStringContainsString('lang="fa" dir="rtl"', $html);
        $this->assertStringContainsString('id="main-content"', $html);
        $this->assertStringContainsString('id="ui-toast-region"', $html);
        $this->assertStringContainsString('<h1>محتوا</h1>', $html);
        $this->assertStringNotContainsString('Vazirmatn-font-face', $html);
        $this->assertStringNotContainsString('/status', $html);
        $this->assertStringNotContainsString('href="#"', $html);
    }

    public function test_account_link_respects_all_existing_panel_roles(): void
    {
        foreach (['admin' => 'admin.dashboard', 'manager' => 'admin.dashboard', 'agent' => 'reseller.dashboard', 'sub_agent' => 'reseller.dashboard', 'customer' => 'customer.dashboard'] as $role => $route) {
            $this->signInAs($role);
            $html = view('layouts.partials.site-shell', ['slot' => new HtmlString('')])->render();
            $this->assertStringContainsString('href="'.route($route).'"', $html);
        }
    }

    public function test_existing_panel_layouts_render_with_the_shared_feedback_region(): void
    {
        foreach (['admin' => 'admin', 'agent' => 'agent', 'customer' => 'customer', 'app' => 'customer', 'store' => 'customer', 'public' => 'customer'] as $layout => $role) {
            $this->signInAs($role);
            $html = view('layouts.'.$layout, ['slot' => new HtmlString('<h1>آزمایش پنل</h1>')])->render();
            $this->assertStringContainsString('آزمایش پنل', $html, $layout);
            $this->assertSame(1, substr_count($html, 'id="ui-toast-region"'), $layout);
            $this->assertStringNotContainsString('user_102', $html, $layout);
            $this->assertStringNotContainsString('user-scalable', $html, $layout);
        }
    }

    public function test_agent_announcements_are_active_targeted_and_limited(): void
    {
        $this->signInAs('agent');
        foreach (range(1, 7) as $i) {
            Announcement::create(['title' => 'اطلاعیه '.$i, 'content' => 'متن', 'target' => 'agents', 'is_active' => true]);
        }
        Announcement::create(['title' => 'مخصوص مشتری', 'content' => 'متن', 'target' => 'customers', 'is_active' => true]);
        Announcement::create(['title' => 'غیرفعال', 'content' => 'متن', 'target' => 'all', 'is_active' => false]);
        $component = new Announcements;
        $this->assertCount(5, $component->announcements);
        $this->assertSame('اطلاعیه 7', $component->announcements->first()->title);
        $this->assertFalse($component->announcements->contains('title', 'مخصوص مشتری'));
        $this->assertFalse($component->announcements->contains('title', 'غیرفعال'));
    }

    public function test_customer_announcements_do_not_include_agent_messages_and_escape_content(): void
    {
        $this->signInAs('customer');
        Announcement::create(['title' => 'عمومی', 'content' => '<script>alert(1)</script>', 'target' => 'all', 'is_active' => true]);
        Announcement::create(['title' => 'مشتری', 'content' => 'متن', 'target' => 'customers', 'is_active' => true]);
        Announcement::create(['title' => 'نماینده', 'content' => 'متن', 'target' => 'agents', 'is_active' => true]);
        $component = new Announcements;
        $this->assertCount(2, $component->announcements);
        $html = Blade::render('<x-ui.announcements />');
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_empty_announcements_show_an_honest_empty_state(): void
    {
        $this->signInAs('sub_agent');
        $html = Blade::render('<x-ui.announcements />');
        $this->assertStringContainsString('اطلاعیه‌ای منتشر نشده است.', $html);
        $this->assertStringNotContainsString('جدید</span>', $html);
    }

    public function test_brand_component_escapes_the_name(): void
    {
        $html = Blade::render('<x-ui.brand :name="$name" />', ['name' => '<img onerror=alert(1)>']);
        $this->assertStringNotContainsString('<img onerror=', $html);
        $this->assertStringContainsString('&lt;img', $html);
    }
}
