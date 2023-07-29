<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    use HasDates;

    protected $guarded = [];

    public static function getDataForToken($token)
    {
        return VerifyUser::firstWhere('token', $token);
    }

    /*** Relationship ***/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
