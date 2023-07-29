<?php

namespace App\Mail;

use App\Models\Branding;
use App\Models\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $passwordResetToken;
    public $branding;
    public $emailConfig;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $passwordResetToken)
    {
        $this->user = $user;
        $this->passwordResetToken = $passwordResetToken;
        $this->branding = Branding::first();
        $this->emailConfig = EmailConfig::findByType('password_reset');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailConfig->subject)->view('emails.password_reset');
    }

    public function getHtml()
    {
        return $this->view('emails.league_invite')->render();
    }
}
