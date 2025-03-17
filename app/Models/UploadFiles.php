<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFiles extends Model
{
    use HasFactory;
    protected $table = 'uploaded_files';
    protected $fillable = [
        'user_id',
        'file_path',
        'prospect_name',
        'uploaded_at'
    ];
}
