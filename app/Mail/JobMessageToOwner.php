<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobMessageToOwner extends Mailable
{
    use Queueable, SerializesModels;

    public $fromEmail;
    public $text;
    public $job;
    public $chatLink;
    public $name;
    public $phone;

    public function __construct($fromEmail, $text, $job, $chatLink = null, $name = null, $phone = null)
    {
        $this->fromEmail = $fromEmail;
        $this->text = $text;
        $this->job = $job;
        $this->chatLink = $chatLink;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function build()
    {
        return $this->subject("Fuska.fun - odpověď na inzerát {$this->job->id} - {$this->job->title}")
            ->view('emails.job_message')
            ->with([
                'fromEmail' => $this->fromEmail,
                'text' => $this->text,
                'job' => $this->job,
                'chat_link' => $this->chatLink,
                'name' => $this->name,
                'phone' => $this->phone,
            ]);
    }
}
