<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use HasDates;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'reference',
        'status',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
