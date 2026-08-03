<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان آموزش (مثلا: آموزش اتصال وایرگارد در ویندوز)
            $table->string('platform')->nullable(); // پلتفرم: Android, Windows, iOS, Mac
            $table->string('protocol')->nullable(); // پروتکل: WireGuard, Cisco, OpenVPN, L2TP
            $table->longText('content'); // محتوای HTML تولید شده توسط ویرایشگر
            $table->boolean('is_published')->default(true); // وضعیت انتشار
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorials');
    }
};
