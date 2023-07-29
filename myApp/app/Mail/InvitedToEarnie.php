<?php

namespace App\Mail;

use App\Models\Branding;
use App\Models\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitedToEarnie extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $branding;
    public $emailConfig;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->branding = Branding::first();
        $this->emailConfig = EmailConfig::findByType('invitation');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailConfig->subject)->view('emails.invitation');
    }

    public function getHtml()
    {
        return $this->view('emails.invitation')->render();
    }
}
