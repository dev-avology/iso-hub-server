<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;
    protected $table = 'team_members';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];
}
