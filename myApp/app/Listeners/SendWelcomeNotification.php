<?php

namespace App\Listeners;

use App\Classes\EmailConfigManager;
use App\Events\UserRegistered;
use App\Mail\VerifyEmail;
use App\Mail\Welcome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        if (EmailConfigManager::isEmailEnabled('email_verification')) {
            Mail::to($event->user)->send(new VerifyEmail($event->user));
        } else if (EmailConfigManager::isEmailEnabled('welcome')) {
            Mail::to($event->user)->send(new Welcome($event->user));
        }
    }
}
