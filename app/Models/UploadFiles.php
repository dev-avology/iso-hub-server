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
        'created_by_id',
        'form_id',
        'file_path',
        'prospect_name',
        'file_original_name',
        'email',
        'uploaded_at',
    ];
}
