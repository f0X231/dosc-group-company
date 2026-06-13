<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimonialSetting extends Model
{
    protected $fillable = [
        'label', 'heading', 'bg_color',
        'featured_image_path', 'featured_image_url',
        'overlay_text', 'cta_text', 'cta_url',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'label'    => 'TESTIMONIALS',
            'heading'  => 'กำลังใจสำคัญของเรา',
            'bg_color' => '#7c1d1d',
            'cta_text' => 'ดูเพิ่มเติม',
        ]);
    }
}
