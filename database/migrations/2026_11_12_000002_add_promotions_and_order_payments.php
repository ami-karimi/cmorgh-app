<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('code', 64)->nullable()->unique();
            $t->string('kind', 20); $t->string('audience', 20)->default('all');
            $t->string('value_type', 10); $t->unsignedInteger('value');
            $t->unsignedInteger('min_amount')->default(0); $t->unsignedInteger('max_discount')->nullable();
            $t->json('group_ids')->nullable(); $t->json('user_ids')->nullable();
            $t->timestamp('starts_at')->nullable(); $t->timestamp('ends_at')->nullable();
            $t->unsignedInteger('usage_limit')->nullable(); $t->unsignedInteger('per_user_limit')->nullable();
            $t->boolean('is_active')->default(true); $t->unsignedBigInteger('created_by')->nullable(); $t->timestamps();
        });
        Schema::create('promotion_redemptions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('promotion_id'); $t->unsignedBigInteger('user_id');
            $t->string('context', 20); $t->unsignedBigInteger('reference_id'); $t->unsignedInteger('amount');
            $t->timestamp('reserved_until')->nullable(); $t->timestamp('redeemed_at')->nullable(); $t->timestamps();
            $t->unique(['promotion_id', 'context', 'reference_id']);
            $t->index(['promotion_id', 'user_id']);
        });
        Schema::table('store_orders', function (Blueprint $t) {
            $t->string('status', 30)->default('pending')->change();
            $t->string('phone', 20)->nullable()->change();
            $t->string('email', 255)->nullable()->change();
            $t->string('payment_method', 20)->default('receipt');
            $t->unsignedInteger('original_price')->nullable(); $t->unsignedInteger('discount_amount')->default(0);
            $t->unsignedBigInteger('promotion_id')->nullable(); $t->timestamp('paid_at')->nullable();
            $t->json('agent_costs')->nullable();
            $t->string('fulfillment_state', 30)->default('waiting'); $t->text('fulfillment_error')->nullable();
            $t->timestamp('fulfillment_started_at')->nullable();
            $t->string('checkout_key', 64)->nullable()->unique();
        });
        Schema::table('topup_requests', function (Blueprint $t) {
            $t->string('purpose', 20)->default('wallet'); $t->unsignedBigInteger('store_order_id')->nullable()->unique();
            $t->unsignedInteger('bonus_amount')->default(0); $t->unsignedBigInteger('bonus_promotion_id')->nullable();
            $t->string('expected_account_number', 50)->nullable();
        });
        Schema::table('financial', function (Blueprint $t) {
            $t->unsignedBigInteger('store_order_id')->nullable(); $t->string('entry_kind', 30)->nullable();
            $t->unique(['store_order_id', 'for', 'entry_kind'], 'financial_order_entry_unique');
            $t->unsignedBigInteger('bonus_topup_id')->nullable()->unique();
        });
        Schema::table('agent_bank_accounts', fn (Blueprint $t) => $t->string('account_number', 50)->nullable());
    }

    public function down(): void
    {
        Schema::table('agent_bank_accounts', fn (Blueprint $t) => $t->dropColumn('account_number'));
        Schema::table('financial', function (Blueprint $t) {
            $t->dropUnique('financial_order_entry_unique'); $t->dropUnique(['bonus_topup_id']);
            $t->dropColumn(['store_order_id', 'entry_kind', 'bonus_topup_id']);
        });
        Schema::table('topup_requests', function (Blueprint $t) {
            $t->dropUnique(['store_order_id']);
            $t->dropColumn(['purpose', 'store_order_id', 'bonus_amount', 'bonus_promotion_id', 'expected_account_number']);
        });
        Schema::table('store_orders', function (Blueprint $t) {
            $t->dropUnique(['checkout_key']);
            $t->dropColumn(['payment_method','original_price','discount_amount','promotion_id','paid_at','fulfillment_state','fulfillment_error','fulfillment_started_at','checkout_key','agent_costs']);
        });
        Schema::dropIfExists('promotion_redemptions'); Schema::dropIfExists('promotions');
    }
};
