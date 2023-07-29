<?php

namespace App\Models;

use App\Models\AchievementWinner;
use App\Models\Traits\HasDates;
use App\Models\Traits\RecordsUserstamps;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Deliverable extends Model implements Auditable
{
    use AuditableTrait;
    use RecordsUserstamps;
    use HasDates;
    use HasFactory;

    protected $fillable = [
        'is_shipped',
        'shipping_name',
        'shipping_number',
        'shipping_email',
        'shipping_addressline1',
        'shipping_addressline2',
        'shipping_addressline3',
        'shipping_postcode',
        'shipping_county',
        'shipping_country',
        'shipping_comment',
        'tracking_ref',
    ];

    /** Relationship **/
    public function achievement_winner()
    {
        return $this->hasOne(AchievementWinner::class);
    }

    public function winner()
    {
        return $this->hasOne(Winner::class);
    }
    /** Relationship **/
}
