<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep historical statuses readable; new successful payments use completed.
        Schema::table('topup_requests', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });
        foreach (Schema::getIndexes('topup_requests') as $index) {
            if ($index['unique'] && $index['columns'] === ['payable_amount', 'status']) {
                // Terminal requests must not prevent reuse of an amount indefinitely.
                Schema::table('topup_requests', fn (Blueprint $table) => $table->dropUnique($index['name']));
            }
        }
        Schema::table('topup_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('topup_requests', 'matched_bank_message_id')) {
                $table->unsignedBigInteger('matched_bank_message_id')->nullable()->index();
            }
            $table->timestamp('matched_at')->nullable();
            $table->string('match_type', 20)->nullable();
            $table->timestamp('settled_at')->nullable()->index();
            $table->text('rejection_reason')->nullable();
            $table->index(['status', 'payable_amount']);
            // Unique only for active requests, not for historical terminal rows.
            $table->unsignedBigInteger('active_payable_amount')->nullable()
                ->virtualAs("CASE WHEN status IN ('pending', 'paid') THEN payable_amount ELSE NULL END")->unique();
        });
        Schema::table('financial', function (Blueprint $table) {
            $table->unsignedBigInteger('top_up_request_id')->nullable()->unique();
        });
        Schema::table('bank_messages', function (Blueprint $table) {
            $table->string('deduplication_key', 64)->nullable()->unique();
            $table->text('processing_error')->nullable();
        });
    }

    public function down(): void
    {
        // Do not restore the enum/amount uniqueness: that could invalidate historical payments.
        Schema::table('financial', function (Blueprint $table) {
            $table->dropUnique(['top_up_request_id']);
            $table->dropColumn('top_up_request_id');
        });
        Schema::table('bank_messages', function (Blueprint $table) {
            $table->dropUnique(['deduplication_key']);
            $table->dropColumn(['deduplication_key', 'processing_error']);
        });
        Schema::table('topup_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'payable_amount']);
            $table->dropIndex(['settled_at']);
            $table->dropUnique(['active_payable_amount']);
            $table->dropColumn('active_payable_amount');
            $table->dropColumn(['matched_at', 'match_type', 'settled_at', 'rejection_reason']);
        });
    }
};
