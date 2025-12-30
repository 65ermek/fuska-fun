@extends('layouts.bazos')

@section('title', 'Platba topování')

@section('content')
    <div class="maincontent">
        <div class="oranzovy">

            @php
                $vs = '2026' . str_pad($payment->vs_number, 6, '0', STR_PAD_LEFT);
            @endphp

            <h3>Platba topování inzerátu</h3>

            <p>
                Počet topování: <b>{{ $payment->count }}</b><br>
                Částka: <b>{{ $payment->amount }} Kč</b><br>
                Variabilní symbol: <b>{{ $vs }}</b>
            </p>

            <hr>

            <p>
                Naskenujte QR kód ve vaší bankovní aplikaci:
            </p>

            @php
                $vs = '2026' . str_pad($payment->vs_number, 6, '0', STR_PAD_LEFT);
                $date = now()->format('Ymd');

                $qrString = "SPD*1.0"
                    . "*ACC:CZ7820100000002002895425"
                    . "*AM:" . number_format($payment->amount, 2, '.', '')
                    . "*CC:CZK"
                    . "*DT:" . $date
                    . "*X-KS:0308"
                    . "*X-VS:" . $vs;
            @endphp

            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data={{ urlencode($qrString) }}"
                alt="QR Platba">

            <hr>

            <p>
                <b>Příjemce:</b> NewIdea, spol. s r.o.<br>
                <b>Účet:</b> 2002895425 / 2010<br>
                <b>Variabilní symbol:</b> {{ $vs }}
            </p>

            <p class="text-muted">
                Platba bude automaticky spárována podle variabilního symbolu.
                Po přijetí platby bude inzerát ihned topován.
            </p>
            @if($payment->status === 'pending')
                <form method="POST" action="{{ route('top-payment.paid', $payment->id) }}">
                    @csrf
                    <button type="submit" class="zaplatil-btn">
                        Zaplatil jsem
                    </button>
                </form>
            @elseif($payment->status === 'waiting')
                <p style="color: #856404;">
                    ⏳ Platba čeká na kontrolu administrátorem.
                </p>
            @elseif($payment->status === 'paid')
                <p style="color: green;">
                    ✅ Platba byla potvrzena.
                </p>
            @endif

        </div>
    </div>

    <style>
        .oranzovy {
            background: #fff4e5;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 6px;
            max-width: 600px;
        }
        .zaplatil-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .zaplatil-btn:hover {
            background: #218838;
        }

    </style>
@endsection
