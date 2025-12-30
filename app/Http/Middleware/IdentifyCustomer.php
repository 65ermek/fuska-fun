<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class IdentifyCustomer
{
    public function handle(Request $request, Closure $next)
    {
        // 🔥 КРИТИЧЕСКИ ВАЖНО: ЕСЛИ ТОЛЬКО ЧТО АВТОРИЗОВАЛСЯ АВТОР - БЛОКИРУЕМ
        $justAuthorized = session('author_logged_in') &&
            session('user_email') &&
            empty(session('customer_id'));

        if ($justAuthorized) {
            Log::info("🚫 BLOCKING AUTO-IDENTIFICATION - AUTHOR JUST LOGGED IN", [
                'author_email' => session('user_email'),
                'session_just_created' => true
            ]);
            return $next($request);
        }

        // 🔥 ЕСЛИ АВТОР УЖЕ В СЕССИИ - ТОЖЕ БЛОКИРУЕМ
        if (session('author_logged_in') && session('user_email')) {
            Log::info("🚫 AUTHOR SESSION ACTIVE - SKIPPING CUSTOMER AUTO-IDENTIFICATION");
            return $next($request);
        }
        // Если customer уже в сессии - пропускаем
        if (session()->has('customer_id')) {
            return $next($request);
        }

        $customer = null;

        // 🔥 СПОСОБ 1: По cookie
        $customerToken = $request->cookie('fuska_customer_token');
        if ($customerToken) {
            $customer = Customer::where('persistent_token', $customerToken)->first();
        }

        // 🔥 СПОСОБ 2: По email из объявлений пользователя
        if (!$customer && auth()->check()) {
            // Если пользователь авторизован - ищем по email
            $customer = Customer::where('email', auth()->user()->email)->first();
        }

        // 🔥 СПОСОБ 3: По email из параметра или формы
        if (!$customer && $request->has('email')) {
            $customer = Customer::where('email', $request->email)->first();
        }

        // 🔥 СПОСОБ 4: При просмотре "Moje inzeráty" - ищем по email из объявлений
        if (!$customer && $request->routeIs('jobs.my')) {
            $customer = $this->findCustomerFromJobs($request);
        }

        // Сохраняем customer в сессию если нашли
        if ($customer) {
            session([
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'customer_name' => $customer->name,
                'customer_source' => $customer->source,
            ]);

            Log::info("👤 Customer auto-identified", [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'source' => 'middleware'
            ]);
        }

        return $next($request);
    }

    /**
     * 🔥 НАХОДИМ CUSTOMER ПО EMAIL ИЗ ОБЪЯВЛЕНИЙ ПОЛЬЗОВАТЕЛЯ
     */
    private function findCustomerFromJobs(Request $request)
    {
        // Получаем email из куки tokens или из параметров
        $tokens = json_decode($request->cookie('fuska_tokens', '[]'), true);

        if (!empty($tokens)) {
            // Ищем объявления по токенам
            $job = \App\Models\Job::whereIn('edit_token', $tokens)->first();
            if ($job && $job->email) {
                return Customer::where('email', $job->email)->first();
            }
        }

        // Если в URL есть email (при восстановлении)
        if ($request->has('email')) {
            return Customer::where('email', $request->email)->first();
        }

        return null;
    }
}
