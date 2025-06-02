<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorTemplates extends Model
{
    use HasFactory;
    protected $table = 'vendor_templates';
    protected $fillable = [
        'user_id',
        'vendor_type',
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'logo_url',
        'login_url',
        'rep_name',
        'rep_email',
        'rep_phone',
        'notes',
        'support_info',
        'description',
        'card_order',
    ];

    public function vendor_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
