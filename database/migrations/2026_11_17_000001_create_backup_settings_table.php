<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();

            $table->boolean('enabled')->default(true);
            $table->boolean('mikrotik_enabled')->default(true);
            $table->boolean('database_enabled')->default(true);

            $table->string('schedule')->default('daily');
            $table->string('run_at')->default('03:00');

            $table->unsignedInteger('retention_days')->default(30);
            $table->boolean('encrypt_files')->default(true);

            $table->boolean('notify_on_failure')->default(true);
            $table->boolean('notify_on_success')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
