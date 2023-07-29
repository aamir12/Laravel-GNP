<?php

namespace App\Listeners;

use App\Classes\EmailConfigManager;
use App\Events\UserCreated;
use App\Mail\InvitedToEarnie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitation
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
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        if (EmailConfigManager::isEmailEnabled('invitation') && $user->email) {
            Mail::to($user)->send(new InvitedToEarnie($user));
        }
    }
}
