<?php

namespace App\Providers;

use App\Events\CompetitionEnded;
use App\Events\CompetitionStarted;
use App\Events\UserCreated;
use App\Events\UserRegistered;
use App\Events\UserSaving;
use App\Listeners\AddUserToDefaultGroup;
use App\Listeners\EnterUserInCompetitions;
use App\Listeners\GenerateUsername;
use App\Listeners\SendCompetitionEndedNotification;
use App\Listeners\SendCompetitionStartedNotification;
use App\Listeners\SendInvitation;
use App\Listeners\SendWelcomeNotification;
use App\Listeners\SetUserFullName;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CompetitionStarted::class => [
            SendCompetitionStartedNotification::class,
        ],
        CompetitionEnded::class => [
            SendCompetitionEndedNotification::class,
        ],
        UserCreated::class => [
            AddUserToDefaultGroup::class,
            SendInvitation::class,
        ],
        UserRegistered::class => [
            SendWelcomeNotification::class,
            GenerateUsername::class,
            EnterUserInCompetitions::class,
        ],
        UserSaving::class => [
            SetUserFullName::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            'Mahfuz\LoginActivity\Listeners\LogSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'Mahfuz\LoginActivity\Listeners\LogFailedLogin',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'Mahfuz\LoginActivity\Listeners\LogSuccessfulLogout',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
