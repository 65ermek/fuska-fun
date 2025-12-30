<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ChatOnlineStatus;
use Illuminate\Support\Facades\Log;

class ChatUpdateLastSeen
{
    public function handle($request, Closure $next)
    {
        $email = null;
        $userType = 'candidate';
        if (session()->has('customer')) {
            $customer = session('customer');

            if (is_array($customer)) {
                $email = $customer['email'] ?? null;
            } elseif (is_object($customer)) {
                $email = $customer->email ?? null;
            }

            $userType = 'customer';

            Log::info('ChatUpdateLastSeen: session customer detected', [
                'email' => $email
            ]);
        }
        elseif ($request->filled('email')) {
            $email = $request->input('email');
            $userType = $request->input('user_type') ?? 'candidate';
        }

        // updateOrCreate
        if ($email) {
            ChatOnlineStatus::updateOrCreate(
                ['email' => $email],
                [
                    'user_type' => $userType,
                    'last_seen' => now(),
                    'is_online' => true
                ]
            );
        }
        return $next($request);
    }
}
