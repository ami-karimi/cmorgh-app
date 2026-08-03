<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // جدول اطلاعیه‌ها
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            // تعیین هدف: همه، فقط نمایندگان، فقط مشتریان
            $table->enum('target', ['all', 'agents', 'customers'])->default('all');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        // جدول وضعیت سرویس‌ها و آخرین تغییرات
        Schema::create('service_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // مثلا: سرورهای ایران، تانل وایرگارد و...
            $table->enum('status', ['operational', 'degraded', 'outage'])->default('operational');
            $table->string('last_change_log')->nullable(); // آخرین تغییرات
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('service_statuses');
    }
};
