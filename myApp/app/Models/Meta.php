<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasDates;

    protected $table = 'meta';

    protected $fillable = [
        'name', 'content', 'meta_id', 'meta_type', 'status'
    ];

    protected $hidden = [
        'meta_type', 'status', 'created_at', 'updated_at', 'id', 'meta_id'
    ];

    public static function extractMetadata($arr): array
    {
        if (isset($arr['metadata'])) {
            return json_decode($arr['metadata'], true);
        }
        return [];
    }

    public function meta()
    {
        return $this->morphTo();
    }
}
