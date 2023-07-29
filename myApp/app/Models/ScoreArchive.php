<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreArchive extends Model
{
    use HasDates;
    use HasFactory;
    use RecordsUserstamps;

    protected $fillable = [
        'user_id',
        'value',
        'weight',
        'timestamp',
    ];
}
