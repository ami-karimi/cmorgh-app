<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {$table->string('custom_domain')->nullable()->unique()->comment('دامنه اختصاصی نماینده');
            $table->string('brand_name')->nullable()->comment('نام برند نماینده');
            $table->string('brand_logo')->nullable()->comment('لوگوی نماینده');
            $table->enum('domain_status', ['none', 'pending', 'approved', 'rejected'])->default('none');
        });

        // ۲. ساخت جدول حساب‌های بانکی نمایندگان
        Schema::create('agent_bank_accounts', function (Blueprint $table) {$table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');$table->string('bank_name');
            $table->string('account_name');$table->string('card_number', 20);
            $table->string('sheba_number', 50)->nullable();$table->timestamps();
        });

        // ۳. ساخت جدول مجزا برای تنظیمات فروشگاه اختصاصی نماینده
        Schema::create('agent_stores', function (Blueprint $table) {$table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');$table->boolean('is_active')->default(false); // وضعیت فعال بودن فروشگاه
            $table->string('title')->nullable(); // عنوان سئو فروشگاه
            $table->string('support_id')->nullable(); // آیدی پشتیبانی
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_stores');
        Schema::dropIfExists('agent_bank_accounts');
    }
};
