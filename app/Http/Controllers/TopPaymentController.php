<?php

namespace App\Http\Controllers;

use App\Models\TopPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopPaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'pocetpoukazek' => 'required|integer|min:1|max:21',
            'idad' => 'required|integer',
        ]);

        return DB::transaction(function () use ($request) {

            $count  = $request->pocetpoukazek;
            $amount = $count * 29;

            $lastNumber = TopPayment::lockForUpdate()->max('vs_number');

            $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

            $payment = TopPayment::create([
                'job_id'    => $request->idad,
                'count'     => $count,
                'amount'    => $amount,
                'vs_number' => $nextNumber,
                'status'    => 'pending',
            ]);

            return redirect("/platba-topovani/{$payment->id}");
        });
    }

    public function show($id)
    {
        $payment = TopPayment::findOrFail($id);
        return view('jobs.platba-topovani', compact('payment'));
    }
    public function markAsPaid(TopPayment $payment)
    {
        // защита от повторных кликов
        if ($payment->status !== 'pending') {
            return back();
        }

        $payment->update([
            'status' => 'waiting',
        ]);

        // тут позже можно:
        // - отправить email админу
        // - логировать событие

        return back()->with('success', 'Platba byla označena jako zaplacená.');
    }
}
