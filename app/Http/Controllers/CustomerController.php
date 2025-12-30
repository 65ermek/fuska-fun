<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * 🔥 АВТОРИЗАЦИЯ CUSTOMER ПО EMAIL
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        try {
            // Ищем customer по email
            $customer = Customer::where('email', $email)->first();

            if (!$customer) {
                // 🔥 ЕСЛИ CUSTOMER НЕ СУЩЕСТВУЕТ - СОЗДАЕМ ЕГО ИЗ ОБЪЯВЛЕНИЙ
                $customer = $this->createCustomerFromJobs($email);
            }

            if ($customer) {
                // Сохраняем в сессию
                session([
                    'customer_id' => $customer->id,
                    'customer_email' => $customer->email,
                    'customer_name' => $customer->name,
                    'customer_source' => $customer->source,
                    'customer_phone' => $customer->phone,
                ]);

                Log::info("👤 Customer authenticated via form", [
                    'customer_id' => $customer->id,
                    'email' => $customer->email
                ]);

                // 🔥 РЕДИРЕКТИМ НА STRANku S INZERÁTY
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'redirect' => route('jobs.my'),
                        'customer' => [
                            'id' => $customer->id,
                            'email' => $customer->email,
                            'name' => $customer->name,
                            'source' => $customer->source
                        ]
                    ]);
                } else {
                    return redirect()->route('jobs.my');
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Customer auth error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication failed'
            ], 500);
        }
    }

    /**
     * 🔥 СОЗДАЕМ CUSTOMER ИЗ СУЩЕСТВУЮЩИХ ОБЪЯВЛЕНИЙ
     */
    private function createCustomerFromJobs($email)
    {
        // Ищем объявления с этим email
        $jobs = Job::where('email', $email)->get();


        if ($jobs->isEmpty()) {
            return null;
        }

        $firstJob = $jobs->first();

        // Создаем нового customer
        $persistentToken = hash('sha256', Str::random(40));

        $customer = Customer::create([
            'email' => $email,
            'name' => $firstJob->contact_name,
            'phone' => $firstJob->phone,
            'source' => 'author',
            'persistent_token' => $persistentToken,
            'last_seen_at' => now(),
        ]);

        // 🔥 ОБНОВЛЯЕМ ВСЕ ОБЪЯВЛЕНИЯ С ЭТИМ EMAIL - СВЯЗЫВАЕМ С CUSTOMER
        Job::where('email', $email)->update(['customer_id' => $customer->id]);

        Log::info("👤 New customer created from jobs", [
            'customer_id' => $customer->id,
            'email' => $email,
            'jobs_count' => $jobs->count()
        ]);

        return $customer;
    }
}
