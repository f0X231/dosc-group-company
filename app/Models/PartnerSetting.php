<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerSetting extends Model
{
    protected $fillable = [
        'heading', 'bg_color', 'logos_per_row', 'show_name', 'grayscale',
    ];

    protected $casts = [
        'show_name' => 'boolean',
        'grayscale' => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'heading'       => 'ลูกค้าที่ให้ความไว้วางใจกับเรา',
            'bg_color'      => '#7c1d1d',
            'logos_per_row' => 4,
            'show_name'     => false,
            'grayscale'     => true,
        ]);
    }
}
