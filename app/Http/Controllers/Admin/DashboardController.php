<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Job;
use App\Models\TopPayment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $totalJobs = Job::count();
        $activeJobs = Job::where('status', 'published')->count();
        $customers = Customer::count();
        $jobsToday = Job::whereDate('created_at', today())->count();
        $waitingTopPayments = TopPayment::where('status', 'waiting')->count();


        // --- Visits ---
        $countTodayVisits = Customer::whereBetween('last_seen_at', [
            now()->startOfDay(),
            now()
        ])->count();

        $days = collect();
        $visits = collect();

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $days->push($day->format('D'));

            $visits->push(
                Customer::whereDate('last_seen_at', $day->toDateString())->count()
            );
        }

        // -----------------------------------
        // 📌 Группировка объявлений по CITY
        // -----------------------------------
        $cityStats = Job::whereNotNull('city')
            ->selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->pluck('total', 'city')
            ->toArray();

        return view('admin.dashboard', [
            'totalJobs' => $totalJobs,
            'activeJobs' => $activeJobs,
            'customers' => $customers,
            'jobsToday' => $jobsToday,
            'countTodayVisits' => $countTodayVisits,
            'chartLabels' => $days->toArray(),
            'chartData' => $visits->toArray(),
            'cityStats' => $cityStats,
            'waitingTopPayments' => $waitingTopPayments,
        ]);
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
