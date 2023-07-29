<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CompetitionParticipant extends Pivot implements Auditable
{
    use AuditableTrait;
    use HasDates;

    protected $table = 'competition_participants';

    protected $fillable = [
        'competition_id',
        'user_id',
    ];

    /*** Relations ***/
    public function competitions()
    {
        return $this->hasOne(Competition::class);
    }

    public function users()
    {
        return $this->hasOne(User::class);
    }
    /*** Relations ***/
}
