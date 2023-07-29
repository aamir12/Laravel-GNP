<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    use HasDates;

    protected $table = 'role_user';
    protected $fillable = ['role_id', 'user_id'];
}
