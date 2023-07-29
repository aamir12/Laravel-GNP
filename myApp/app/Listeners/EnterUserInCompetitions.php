<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\Competition;

class EnterUserInCompetitions
{
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $competitions = Competition::where([
            'state' => 'started',
            'auto_enter_user' => '1'
        ])->get();

        foreach ($competitions as $competition) {
            $competition->entrants()->attach($event->user->id);
        }
    }
}
