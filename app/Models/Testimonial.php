<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'customer_name', 'customer_title',
        'avatar_path', 'avatar_url', 'avatar_color',
        'review_text', 'rating', 'source', 'source_url',
        'status', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'rating' => 'integer',
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
