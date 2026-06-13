<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUsItem extends Model
{
    protected $fillable = [
        'icon_name', 'title', 'description', 'sort_order', 'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
