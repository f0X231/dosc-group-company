<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogMedia extends Model
{
    public $incrementing  = false;
    public $timestamps    = false;
    protected $keyType    = 'string';

    protected $fillable   = ['id', 'blog_id', 'blog_uuid', 'storage_path', 'url', 'alt_text', 'file_size', 'mime_type'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
