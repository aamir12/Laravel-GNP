<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EmailConfig;
use Faker\Generator as Faker;

$factory->define(EmailConfig::class, function (Faker $faker) {
    return [
        'email_verification' => rand(0,1),
        'invitation' => rand(0,1),
        'invitation_reminder' => rand(0,1),
        'account_changes' => rand(0,1),
        'password_reset' => rand(0,1),
        'competition_result' => rand(0,1),
        'league_invites' => rand(0,1),
        'invitation_reminder_time_delay' => $faker->randomFloat(3, 0, 1000),
    ];
});
