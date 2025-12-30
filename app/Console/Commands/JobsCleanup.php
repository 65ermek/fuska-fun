<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Mail\JobExpirationWarning;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class JobsCleanup extends Command
{
    protected $signature = 'jobs:cleanup';
    protected $description = 'Send expiration warnings and delete old jobs';

    public function handle()
    {
        // 1. Отправка письма за 30 дней до удаления (тест — 59 минута)
        $jobsToWarn = Job::whereNull('warning_sent_at')
            ->where('created_at', '<=', now()->subMinutes(59))
            ->get();

        foreach ($jobsToWarn as $job) {
            if ($job->email) {
                Mail::to($job->email)->send(new JobExpirationWarning($job));
                $job->update(['warning_sent_at' => now()]);
                $this->info("📧 Warning sent to job ID {$job->id}");
            }
        }

        // 2. Удаление через 2 минуты после письма (тест — 2 минуты)
        $jobsToDelete = Job::whereNotNull('warning_sent_at')
            ->where('warning_sent_at', '<=', now()->subMinutes(2))
            ->get();

        foreach ($jobsToDelete as $job) {
            $job->delete(); // мягкое удаление
            $this->info("🗑️ Job ID {$job->id} deleted");
        }

        return 0;
    }
}
