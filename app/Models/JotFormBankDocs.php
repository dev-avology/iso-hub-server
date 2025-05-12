<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JotFormBankDocs extends Model
{
    use HasFactory;
    protected $table = 'jot_form_bank_docs';
    protected $fillable = [
        'jot_form_id ',
        'name',
        'path'
    ];
    
}
