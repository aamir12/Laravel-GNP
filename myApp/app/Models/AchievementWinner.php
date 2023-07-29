<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class AchievementWinner extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'achievement_id',
        'user_id',
        'deliverable_id',
        'is_claimed'
    ];


    public static function create($data)
    {
        $model = new AchievementWinner;
        is_array($data) ? $model->fill($data) : $model->fill((array) $data);
        return $model->save() ? $model : false;
    }

    /** Relations **/
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'id', 'achievement_id');
    }

    public function deliverable()
    {
        return $this->hasOne(Deliverable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class, 'achievement_id', 'id');
    }
    /** Relations **/
}
