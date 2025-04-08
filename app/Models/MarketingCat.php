<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCat extends Model
{
    use HasFactory;
    protected $table = 'marketing_categories';
    protected $fillable = [
        'name',
        'slug'
    ];
    public function items()
    {
        return $this->hasMany(MarketingItems::class, 'category_id');
    }
}
