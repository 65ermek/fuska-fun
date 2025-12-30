<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobUpdatedNotification extends Mailable
{
    use Queueable, SerializesModels;
    public Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function build(): self
    {
        return $this->subject("Váš inzerát byl upraven")
            ->view('emails.job_updated')
            ->with(['job' => $this->job]);
    }
}
