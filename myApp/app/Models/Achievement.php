<?php

namespace App\Models;

use App\Models\AchievementWinner;
use App\Models\Traits\HasDates;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Achievement extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use RecordsUserstamps;
    use SoftDeletes;

    protected $fillable = [
        'stock_id',
        'name',
        'description',
        'image'
    ];


    /** Relationship **/
    public function achievementWinners()
    {
        return $this->hasMany(AchievementWinner::class, 'achievement_id');
    }
    /** Relationship **/
}
