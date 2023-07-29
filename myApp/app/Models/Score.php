<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Score extends Model implements AuditableContract
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use HasMetadata;
    use RecordsUserstamps;

    protected $fillable = [
        'user_id',
        'value',
        'weight',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
