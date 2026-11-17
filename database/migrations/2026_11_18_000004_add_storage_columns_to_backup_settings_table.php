<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->string('storage_driver')->default('sftp')->after('notify_on_success');
            $table->string('storage_host')->nullable()->after('storage_driver');
            $table->unsignedInteger('storage_port')->nullable()->after('storage_host');
            $table->string('storage_username')->nullable()->after('storage_port');
            $table->text('storage_password')->nullable()->after('storage_username');
            $table->text('storage_private_key')->nullable()->after('storage_password');
            $table->string('storage_root')->nullable()->after('storage_private_key');
            $table->string('storage_folder_root')->nullable()->after('storage_root');
            $table->unsignedInteger('storage_timeout')->default(120)->after('storage_folder_root');
        });
    }

    public function down(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->dropColumn([
                'storage_driver',
                'storage_host',
                'storage_port',
                'storage_username',
                'storage_password',
                'storage_private_key',
                'storage_root',
                'storage_folder_root',
                'storage_timeout',
            ]);
        });
    }
};
