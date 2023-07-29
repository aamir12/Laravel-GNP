<?php

namespace App\Mail;

use App\Models\Branding;
use App\Models\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompetitionStarted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $competition;
    public $branding;
    public $emailConfig;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $competition)
    {
        $this->user = $user;
        $this->competition = $competition;
        $this->branding = Branding::first();
        $this->emailConfig = EmailConfig::findByType('competition_started');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailConfig->subject)
                    ->view('emails.competition_started');
    }

    public function getHtml()
    {
        return $this->view('emails.competition_started')->render();
    }
}
