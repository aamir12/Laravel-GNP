<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Branding extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use HasMetadata;
    use RecordsUserstamps;

    protected $fillable = [
        'company_name',
        'logo',
        'primary_color',
        'company_address',
        'terms_url',
        'privacy_url',
        'image_type',
        'support_email'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['meta'];

}
