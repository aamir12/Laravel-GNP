<?php

namespace App\Classes;

use App\Events\CompetitionEnded;
use App\Events\CompetitionStarted;
use App\Models\Competition;

class CompetitionScheduler
{
    public static function startCompetitions()
    {
        $competitions = Competition::with('groups.children')
            ->where('start_date', '<=', now()->format('Y-m-d H:i:59'))
            ->where('state', 'pending')
            ->where('status', 'live')
            ->get();

        foreach ($competitions as $competition) {
            $competition->start();
            CompetitionStarted::dispatch($competition);
        }
    }

    public static function endCompetitions()
    {
        $competitions = Competition::with(['entrants', 'prizes'])
            ->where('end_date', '<=', now()->format('Y-m-d H:i:59'))
            ->where('state', 'started')
            ->get();

        foreach ($competitions as $competition) {
            $competition->end();
            CompetitionEnded::dispatch($competition);
        }
    }
}
