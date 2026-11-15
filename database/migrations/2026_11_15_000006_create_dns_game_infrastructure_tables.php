<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('dns_plans', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique();
            $table->unsignedInteger('price')->default(0); $table->unsignedInteger('duration_days')->default(30);
            $table->unsignedTinyInteger('max_ips')->default(1); $table->unsignedTinyInteger('max_devices')->default(1);
            $table->text('description')->nullable(); $table->boolean('is_active')->default(true); $table->unsignedInteger('sort_order')->default(0); $table->timestamps();
        });
        Schema::create('dns_nodes', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('host'); $table->unsignedInteger('port')->default(53);
            $table->string('location')->nullable(); $table->string('provider')->nullable(); $table->string('status')->default('pending');
            $table->unsignedInteger('latency_ms')->nullable(); $table->timestamp('last_checked_at')->nullable();
            $table->boolean('is_active')->default(true); $table->text('notes')->nullable(); $table->timestamps();
            $table->index(['is_active','status']);
        });
        Schema::create('dns_game_domains', function (Blueprint $table) {
            $table->id(); $table->string('game_name'); $table->string('domain'); $table->string('record_type')->default('A');
            $table->string('record_value')->nullable(); $table->string('region')->nullable(); $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true); $table->timestamp('last_checked_at')->nullable(); $table->timestamps();
            $table->index(['game_name','is_active']); $table->index('domain');
        });
        Schema::create('dns_subscriptions', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('dns_plans')->restrictOnDelete(); $table->string('token')->unique();
            $table->string('status')->default('pending'); $table->timestamp('starts_at')->nullable(); $table->timestamp('expires_at')->nullable();
            $table->unsignedTinyInteger('max_ips')->default(1); $table->unsignedTinyInteger('max_devices')->default(1);
            $table->json('metadata')->nullable(); $table->timestamps(); $table->index(['user_id','status']);
        });
        Schema::create('dns_subscription_ips', function (Blueprint $table) {
            $table->id(); $table->foreignId('subscription_id')->constrained('dns_subscriptions')->cascadeOnDelete();
            $table->string('ip_address',45); $table->timestamp('last_seen_at')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps();
            $table->unique(['subscription_id','ip_address']);
        });
    }
    public function down(): void { Schema::dropIfExists('dns_subscription_ips'); Schema::dropIfExists('dns_subscriptions'); Schema::dropIfExists('dns_game_domains'); Schema::dropIfExists('dns_nodes'); Schema::dropIfExists('dns_plans'); }
};
