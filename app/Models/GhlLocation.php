<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhlLocation extends Model
{
    use HasFactory;
    protected $table = 'tokens';
    protected $fillable = [
        'location_id',
        'access_token',
        'refresh_token',
        'expires_in'
    ];
}
