<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingItems extends Model
{
    use HasFactory;
    protected $table = 'marketing_items';
    protected $fillable = [
        'category_id',
        'title',
        'file_url',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(MarketingCat::class, 'category_id');
    }
}
