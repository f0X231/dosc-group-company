<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model
{
    protected $fillable = [
        'badge_text', 'headline', 'headline_highlight', 'subtext',
        'cta_text', 'cta_url', 'stats',
        'media_type', 'media_path', 'media_url',
        'overlay_opacity', 'overlay_color',
        'image_position', 'image_size', 'image_align_y',
        'bg_type', 'bg_color_from', 'bg_color_to',
        'text_color', 'animation_style',
        'status', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
