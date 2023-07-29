<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LeagueEntrant extends Pivot
{
    use HasDates;
    protected $table = 'league_entrants';
    protected $fillable = [
        'league_id',
        'user_id'
    ];
}
