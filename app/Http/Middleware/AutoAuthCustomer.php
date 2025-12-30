<?php
// app/Http/Middleware/AutoAuthCustomer.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use App\Models\Customer;

class AutoAuthCustomer
{
    public function handle($request, Closure $next)
    {
        // Если автор авторизован — не трогаем
        if (session('author_logged_in') || session('block_customer_auto_identification')) {
            return $next($request);
        }

        // Уже есть customer в сессии
        if (session()->has('customer_id')) {
            return $next($request);
        }

        // Проверяем cookie токен
        $token = $request->cookie('fuska_customer_token');

        if ($token) {
            $customer = Customer::where('persistent_token', $token)->first();

            if ($customer) {
                // Устанавливаем сессию
                session([
                    'customer_id'    => $customer->id,
                    'customer_email' => $customer->email,
                    'customer_name'  => $customer->name,
                    'customer_source'=> $customer->source,
                ]);

                // Обновляем время последнего визита
                $customer->update(['last_seen_at' => now()]);
            }
        }

        return $next($request);
    }
}
