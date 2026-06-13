<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageAddon extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'status', 'sort_order',
        'created_by', 'updated_by',
    ];

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
