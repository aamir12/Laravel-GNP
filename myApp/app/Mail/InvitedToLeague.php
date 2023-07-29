<?php

namespace App\Mail;

use App\Models\Branding;
use App\Models\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitedToLeague extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $recipient;
    public $leagueOwner;
    public $branding;
    public $emailConfig;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($recipient, $leagueOwner)
    {
        $this->recipient = $recipient;
        $this->leagueOwner = $leagueOwner;
    	$this->branding = Branding::first();
        $this->emailConfig = EmailConfig::findByType('league_invite');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailConfig->subject)->view('emails.league_invite');
    }

    public function getHtml()
    {
        return $this->view('emails.league_invite')->render();
    }
}
