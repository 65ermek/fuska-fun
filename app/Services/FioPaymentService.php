<?php

namespace App\Services;

use App\Models\TopPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FioPaymentService
{
    public function checkPayments(): void
    {
        $token = config('services.fio.token');

        $from = now()->subDays(3)->format('Y-m-d');
        $to   = now()->format('Y-m-d');

        $url = "https://fioapi.fio.cz/v1/rest/last/{$token}/transactions.json";

        $response = Http::timeout(5)
            ->retry(2, 500)
            ->get($url);


        if (!$response->ok()) {
            Log::error('FIO API error', ['status' => $response->status()]);
            return;
        }

        $data = $response->json();

        $transactions = $data['accountStatement']['transactionList']['transaction'] ?? [];

        foreach ($transactions as $t) {

            $vs = $t['column5']['value'] ?? null; // variabilní symbol
            $amount = (float) ($t['column1']['value'] ?? 0); // amount

            if (!$vs || $amount <= 0) {
                continue;
            }

            // убираем префикс 2026
            if (!str_starts_with($vs, '2026')) {
                continue;
            }

            $vsNumber = (int) substr($vs, 4);

            $payment = TopPayment::where('vs_number', $vsNumber)
                ->where('status', 'pending')
                ->where('amount', $amount)
                ->first();

            if (!$payment) {
                continue;
            }

            $payment->update([
                'status'   => 'paid',
                'paid_at'  => now(),
            ]);

            Log::info('Payment matched', [
                'payment_id' => $payment->id,
                'vs' => $vs,
                'amount' => $amount,
            ]);

            // 👉 ТУТ позже подключим реальное топование объявления
        }
    }
}
