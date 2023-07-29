<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Winner extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prize_id',
        'is_claimed',
        'is_revealed',
        'deliverable_id'
    ];

    /* Relations */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    public function deliverable()
    {
        return $this->belongsTo(Deliverable::class);
    }
    /* Relations */
}
