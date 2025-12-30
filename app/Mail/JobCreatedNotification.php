<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function build(): self
    {
        return $this->subject("Inzerát číslo: {$this->job->id} byl přidán")
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.job_created')  // ⚠️ HTML-письмо
            ->with(['job' => $this->job]);
    }

}
