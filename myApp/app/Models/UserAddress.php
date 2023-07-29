<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasDates;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_default',
        'name',
        'phone',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'town',
        'county',
        'postcode',
        'country',
        'delivery_instructions'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
