<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('support_categories', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique(); $t->string('audience')->default('all');
            $t->string('icon')->nullable(); $t->text('description')->nullable(); $t->boolean('is_active')->default(true); $t->unsignedInteger('sort_order')->default(0); $t->timestamps();
        });
        Schema::create('support_faqs', function (Blueprint $t) {
            $t->id(); $t->foreignId('category_id')->nullable()->constrained('support_categories')->nullOnDelete();
            $t->string('question'); $t->longText('answer'); $t->string('platform')->nullable(); $t->string('protocol')->nullable();
            $t->json('keywords')->nullable(); $t->string('action_url')->nullable(); $t->boolean('is_published')->default(true); $t->unsignedInteger('sort_order')->default(0); $t->unsignedInteger('views')->default(0); $t->timestamps();
            $t->index(['is_published','category_id']);
        });
        Schema::create('support_tickets', function (Blueprint $t) {
            $t->id();

            $t->string('number', 32)->unique();

            $t->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $t->foreignId('category_id')
                ->nullable()
                ->constrained('support_categories')
                ->nullOnDelete();

            $t->foreignId('account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // عمداً FK سفارش در این migration ساخته نمی‌شود؛ نوع جدول store_orders در نصب‌های legacy متفاوت است.
            $t->unsignedInteger('order_id')->nullable();


            $t->string('subject');
            $t->text('initial_message');

            $t->string('status')->default('new');
            $t->string('priority')->default('normal');

            $t->timestamp('last_reply_at')->nullable();
            $t->timestamp('closed_at')->nullable();

            $t->timestamps();

            $t->index(['user_id', 'status']);
            $t->index(['status', 'priority']);
        });
        Schema::create('support_messages', function (Blueprint $t) {
            $t->id(); $t->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->text('message');
            $t->boolean('is_internal')->default(false); $t->timestamps();
            $t->index(['ticket_id','created_at']);
        });
        Schema::create('support_attachments', function (Blueprint $t) {
            $t->id(); $t->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete(); $t->foreignId('message_id')->nullable()->constrained('support_messages')->cascadeOnDelete();
            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete(); $t->string('path'); $t->string('name'); $t->string('mime')->nullable(); $t->unsignedBigInteger('size')->default(0); $t->timestamps();
        });
    }
    public function down(): void { foreach(['support_attachments','support_messages','support_tickets','support_faqs','support_categories'] as $table) Schema::dropIfExists($table); }
};
