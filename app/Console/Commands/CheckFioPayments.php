<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FioPaymentService;

class CheckFioPayments extends Command
{
    protected $signature = 'payments:check-fio';
    protected $description = 'Check Fio bank payments';

    public function handle(FioPaymentService $service)
    {
        $service->checkPayments();
        $this->info('Fio payments checked');
    }
}
