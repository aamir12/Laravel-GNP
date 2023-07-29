<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Model;

class PasswordSecurity extends Model
{
    use HasDates;
    protected $guarded = [];

    public static function deletePasswordSecurity($id)
    {
        $model = PasswordSecurity::find($id);
        return $model->delete();
    }

    /*** Relations ***/
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /*** Relations ***/
}
