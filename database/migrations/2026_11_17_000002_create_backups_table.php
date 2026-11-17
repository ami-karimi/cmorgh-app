<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            // mikrotik_binary | mikrotik_export | database

            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('device_name')->nullable();

            $table->string('disk');
            $table->string('folder')->nullable();
            $table->string('filename');
            $table->string('path');

            $table->unsignedBigInteger('size')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->boolean('encrypted')->default(false);

            $table->string('status')->default('queued');
            // queued | running | success | failed | expired

            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['device_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
