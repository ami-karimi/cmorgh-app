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
        Schema::create('bank_messages', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 50)->nullable();
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->unsignedBigInteger('balance')->default(0);
            $table->timestamp('transaction_datetime')->nullable();
            $table->text('raw_message')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['account_number', 'deposit_amount']);
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_messages');
    }
};
