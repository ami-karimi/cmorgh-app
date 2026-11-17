<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_backup_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('device_id')->unique();

            $table->boolean('enabled')->default(true);

            // every_6_hours | every_12_hours | daily | weekly | monthly
            $table->string('frequency')->default('daily');

            $table->string('run_at', 5)->default('03:00');

            // 0=یکشنبه ... 6=شنبه (مطابق Carbon)
            $table->unsignedTinyInteger('weekday')->nullable();
            $table->unsignedTinyInteger('day_of_month')->nullable();

            // null یعنی از تنظیمات سراسری ارث میبرد
            $table->unsignedInteger('retention_days')->nullable();
            $table->boolean('encrypt')->nullable();

            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_status')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamps();

            $table->index(['enabled', 'next_run_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_backup_settings');
    }
};
