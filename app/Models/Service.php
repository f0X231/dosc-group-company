<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'description', 'icon_name',
        'image_path', 'image_url', 'cta_text', 'cta_url',
        'badge_text', 'badge_color', 'status', 'sort_order',
        'created_by', 'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
