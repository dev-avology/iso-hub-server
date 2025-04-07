<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rep extends Model
{
    use HasFactory;
    protected $table = 'reps';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'user_id'
    ];
}
