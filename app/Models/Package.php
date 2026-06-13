<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name', 'slug', 'tag_text', 'page_structure', 'price', 'original_price',
        'is_vat_excluded', 'description', 'cover_image', 'color_from', 'color_to',
        'badge_text', 'badge_color', 'cta_primary_text', 'cta_primary_url',
        'cta_secondary_text', 'cta_secondary_url', 'portfolio_url', 'detail_url',
        'meta_title', 'meta_description', 'status', 'sort_order',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_vat_excluded' => 'boolean',
    ];

    public function features()
    {
        return $this->hasMany(PackageFeature::class)->orderBy('sort_order');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
