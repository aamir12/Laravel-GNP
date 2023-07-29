<?php

namespace App\Mail;

use App\Models\Branding;
use App\Models\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationCode extends Mailable implements ShouldQueue
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
        $this->emailConfig = EmailConfig::findByType('activation_code');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailConfig->subject)->view('emails.activation_code');
    }
}
