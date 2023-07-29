<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use Illuminate\Database\Eloquent\Model;

class OauthAccessToken extends Model
{
    use HasDates;
}
