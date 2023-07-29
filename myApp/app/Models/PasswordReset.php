<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class PasswordReset extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    public $timestamps = false;
}
