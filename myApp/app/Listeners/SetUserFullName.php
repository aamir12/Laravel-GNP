<?php

namespace App\Listeners;

use App\Events\UserSaving;

class SetUserFullName
{
    /**
     * Handle the event.
     *
     * @param  UserSaving  $event
     * @return void
     */
    public function handle(UserSaving $event)
    {
        $user = $event->user;

        $name = $user->first_name ?? '';
        if (isset($user->last_name)) {
            $name .= isset($user->first_name) ? ' ' . $user->last_name : $user->last_name;
        }
        $user->name = $name;
    }
}
