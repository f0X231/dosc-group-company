<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'tagline',
        'logo_path', 'logo_url',
        'logo_dark_path', 'logo_dark_url',
        'favicon_path', 'favicon_url',
        'phone', 'phone_secondary',
        'email', 'email_secondary',
        'address', 'google_map_url', 'google_map_embed',
        'default_og_image_path', 'default_og_image_url',
        'meta_description',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], ['site_name' => 'DOSC Group']);
    }
}
