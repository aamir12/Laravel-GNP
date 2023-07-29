<?php

namespace App\Mail;

use App\Models\Branding;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawToPaypalRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $branding;
    public $amount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->branding = Branding::first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'User Request to Withdraw Funds';
        return $this->subject($subject)->view('emails.withdraw_to_paypal_request');
    }
}
