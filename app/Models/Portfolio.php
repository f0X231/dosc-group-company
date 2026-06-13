<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'title', 'slug', 'package_id', 'package_name', 'client_url',
        'video_path', 'video_url', 'thumbnail_path', 'thumbnail_url',
        'file_size', 'description', 'status', 'sort_order',
        'created_by', 'updated_by',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
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
