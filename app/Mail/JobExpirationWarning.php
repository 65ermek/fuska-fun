<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobExpirationWarning extends Mailable
{
    use Queueable, SerializesModels;

    public Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function build()
    {
        return $this->subject('Váš inzerát bude brzy smazán')
            ->markdown('emails.job_expiration_warning')
            ->with([
                'job' => $this->job,
            ]);
    }
}
