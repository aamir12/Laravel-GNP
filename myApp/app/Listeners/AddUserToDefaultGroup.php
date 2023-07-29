<?php

namespace App\Listeners;

use App\Models\Group;

class AddUserToDefaultGroup
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        $group = Group::firstWhere('is_default_group', 1);

        if ($group) {
            $group->users()->attach($user);
        }
    }
}
