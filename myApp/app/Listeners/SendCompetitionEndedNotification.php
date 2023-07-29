<?php

namespace App\Listeners;

use App\Classes\EmailConfigManager;
use App\Events\CompetitionEnded;
use App\Mail\CompetitionEnded as CompetitionEndedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendCompetitionEndedNotification
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
     * @param  CompetitionEnded  $event
     * @return void
     */
    public function handle(CompetitionEnded $event)
    {
        if (EmailConfigManager::isEmailEnabled('competition_ended')) {
            foreach($event->competition->entrants as $user) {
                Mail::to($user)->send(new CompetitionEndedMail($user, $event->competition));
            }
        }
    }
}
