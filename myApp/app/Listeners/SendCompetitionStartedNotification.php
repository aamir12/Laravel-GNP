<?php

namespace App\Listeners;

use App\Classes\EmailConfigManager;
use App\Events\CompetitionStarted;
use App\Mail\CompetitionStarted as CompetitionStartedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendCompetitionStartedNotification
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
     * @param  CompetitionStarted  $event
     * @return void
     */
    public function handle(CompetitionStarted $event)
    {
        if (EmailConfigManager::isEmailEnabled('competition_started')) {
            foreach($event->competition->getUsersEligibleToEnter() as $user) {
                Mail::to($user)->send(new CompetitionStartedMail($user, $event->competition));
            }
        }
    }
}
