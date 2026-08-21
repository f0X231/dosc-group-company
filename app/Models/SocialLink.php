<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SocialLink extends Model
{
    protected $fillable = [
        'platform', 'label', 'url',
        'icon_type', 'icon_value', 'icon_path', 'icon_url',
        'color', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        // ->where('is_active', true) binds as integer 1, which fails against a
        // native pgsql boolean column when PDO::ATTR_EMULATE_PREPARES is on
        // (required for Supabase's Transaction Pooler). Inline the literal instead.
        return $query->where('is_active', DB::raw('true'));
    }
}
