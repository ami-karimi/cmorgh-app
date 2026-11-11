<?php

namespace App\Console\Commands;

use App\Services\BankPaymentService;
use Illuminate\Console\Command;

class ProgressBankMesage extends Command
{
    protected $signature = 'bank:sync-payment';
    protected $description = 'Match bank deposits and credit wallets exactly once';

    public function handle(BankPaymentService $payments): int
    {
        $result = $payments->processPending();
        $this->info("Processed: {$result['processed']}; matched: {$result['matched']}");
        return self::SUCCESS;
    }
}
