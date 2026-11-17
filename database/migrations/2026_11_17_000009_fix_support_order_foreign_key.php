<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('support_tickets') || !Schema::hasTable('store_orders') || !Schema::hasColumn('support_tickets','order_id')) return;
        $foreignExists = DB::selectOne("select count(*) as aggregate from information_schema.REFERENTIAL_CONSTRAINTS where CONSTRAINT_SCHEMA = database() and TABLE_NAME = 'support_tickets' and CONSTRAINT_NAME = 'support_tickets_order_id_foreign'");
        if ((int) ($foreignExists->aggregate ?? 0) === 0) {
            Schema::table('support_tickets', function (Blueprint $table) { $table->foreign('order_id', 'support_tickets_order_id_foreign')->references('id')->on('store_orders')->nullOnDelete(); });
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('support_tickets')) {
            Schema::table('support_tickets', function (Blueprint $table) { try { $table->dropForeign('support_tickets_order_id_foreign'); } catch (Throwable) {} });
        }
    }
};
