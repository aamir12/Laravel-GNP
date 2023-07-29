<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Prize extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use RecordsUserstamps;
    use SoftDeletes;

    protected $fillable = [
        'stock_id',
        'competition_id',
        'reference',
        'max_winners'
    ];

    /*** Relations ***/
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }
}
