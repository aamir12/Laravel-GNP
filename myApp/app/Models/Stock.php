<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Stock extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use RecordsUserstamps;

    protected $fillable = [
        'name',
        'image',
        'type',
        'amount',
        'currency',
        'image_type'
    ];

    /*** Relations ***/
    public function prize()
    {
        return $this->hasOne(Prize::class);
    }
}
