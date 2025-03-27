<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JotForm extends Model
{
    use HasFactory;
    protected $table = 'jot_forms';
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'description',
        'signature_date',
        'signature',
    ];
}
