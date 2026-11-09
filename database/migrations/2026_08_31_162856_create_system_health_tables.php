<?php
// database/migrations/xxxx_xx_xx_create_system_health_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // جدول بررسی‌های سلامت سیستم
        Schema::create('system_health_checks', function (Blueprint $table) {
            $table->id();
            $table->string('service')->index(); // radius, mikrotik, wireguard, openvpn, v2ray
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->json('summary')->nullable(); // خلاصه نتایج
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // جدول مشکلات شناسایی شده
        Schema::create('system_health_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')->nullable()->constrained('system_health_checks')->nullOnDelete();
            $table->string('service'); // radius, mikrotik, wireguard, openvpn, v2ray
            $table->string('server_id')->nullable(); // شناسه سرور (اگر قابل تشخیص باشد)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username');
            $table->string('issue_type'); // orphan, missing, mismatch
            $table->string('severity')->default('warning'); // critical, warning, info
            $table->text('details')->nullable();
            $table->string('status')->default('open'); // open, ignored, resolved
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
        });

        // جدول لاگ‌های تعمیراتی
        Schema::create('system_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('action'); // delete_orphan, sync_user, cleanup_logs, etc.
            $table->string('service')->nullable();
            $table->string('target')->nullable(); // username, file path, etc.
            $table->string('status'); // success, failed, pending
            $table->text('message')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_maintenance_logs');
        Schema::dropIfExists('system_health_issues');
        Schema::dropIfExists('system_health_checks');
    }
};
