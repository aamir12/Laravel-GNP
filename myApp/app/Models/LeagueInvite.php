<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class LeagueInvite extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasMetadata;
    use RecordsUserstamps;

    protected $fillable = [
        'league_owner_id',
        'invitee_id',
        'league_id',
        'accepted',
        'rejected',
    ];

    public static function updateData($league, $data)
    {
        if (is_object($league) && $model = $league) { //When league object
            is_array($data) ? $model->fill($data) : $model->fill((array)$data);
            return $model->save() ? $model : false;
        } else if ($model = LeagueInvite::find($league)) { //When league id
            is_array($data) ? $model->fill($data) : $model->fill((array)$data);
            return $model->save() ? $model : false;
        } else {
            return 'leagueinvitenotfound';
        }
    }
}
