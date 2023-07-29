<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class GenerateUsername
{
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $user = $event->user;
        if (config('app.generate_usernames')) {
            $user->username = UsernameGenerator::generate();
        } else {
            $user->username = UsernameGenerator::usingName()->generate($user->name);
        }
        $user->save();
    }
}
