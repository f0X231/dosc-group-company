<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name', 'logo_path', 'logo_url', 'website_url',
        'status', 'sort_order', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
