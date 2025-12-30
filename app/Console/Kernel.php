<?php

namespace App\Console;

use App\Mail\JobExpirationWarning;
use App\Models\Job;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $jobs = Job::whereNull('warning_sent_at')
            ->where('created_at', '<=', now()->subMinutes(59))
            ->get();

        foreach ($jobs as $job) {
            Mail::to($job->email)->send(new JobExpirationWarning($job));
        }

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
