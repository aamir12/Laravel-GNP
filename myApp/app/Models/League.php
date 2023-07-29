<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class League extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use HasMetadata;
    use RecordsUserstamps;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'parent_id',
        'owner_id',
        'score_aggregation_period',
        'type',
        'group_id',
        'image_type'
    ];

    public static function updateData($league, $data)
    {
        if (is_object($league) && $model = $league) { //When league is object
            is_array($data) ? $model->fill($data) : $model->fill((array)$data);
            return $model->save() ? $model : false;
        } else if ($model = League::find($league)) { //When league is id
            is_array($data) ? $model->fill($data) : $model->fill((array)$data);
            return $model->save() ? $model : false;
        } else {
            return false;
        }
    }

    public function entrants()
    {
        return $this->belongsToMany(User::class, 'league_entrants');
    }

    public function group()
    {
        return $this->hasOne(Group::class);
    }

    public function children()
    {
        return $this->hasMany(League::class, 'parent_id')->with('entrants');
    }
    /*** Relations ***/
}
