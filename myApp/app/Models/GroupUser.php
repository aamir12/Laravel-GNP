<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    use HasDates;

    protected $table = 'group_user';
    protected $fillable = [
        'group_id',
        'user_id',
    ];
}
