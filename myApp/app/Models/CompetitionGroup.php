<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;

class CompetitionGroup extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;

    protected $table = 'competition_group';
    protected $fillable = [
        'competition_id',
        'group_id',
    ];

    /*** Relations ***/
    // public function users(){
    //     return $this->hasOne('App\Models\User', 'id', 'user_id');
    // }
    /*** Relations ***/
}
