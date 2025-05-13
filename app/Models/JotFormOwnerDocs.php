<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JotFormOwnerDocs extends Model
{
    use HasFactory;
    protected $table = 'jot_form_owner_docs';
    protected $fillable = [
        'jot_form_id',
        'name',
        'path',
        'ownership_first_name',
        'ownership_last_name',
        'ownership_percent',
        'ownership_phone_number',
        'ownership_city',
        'ownership_state',
        'ownership_zip',
        'ownership_email',
        'ownership_dob',
        'ownership_social_security_number',
        'ownership_residential_street_address',
        'ownership_driver_licence_number',
        'ownership_address',
        'owner_street_address',
        'owner_street_address2',
        'ownership_title',
    ];

    protected $hidden = [
        "name",
        "path",
        'ownership_address',
        'ownership_residential_street_address'
    ];
}
