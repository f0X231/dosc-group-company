<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageSeo extends Model
{
    protected $table = 'page_seo';

    protected $fillable = [
        'page_key', 'page_label',
        'meta_title', 'meta_description',
        'og_title', 'og_description',
        'og_image_path', 'og_image_url',
        'robots', 'canonical_url', 'schema_json',
    ];

    public static function getCachedByKey(string $key): ?array
    {
        return Cache::remember("page_seo:{$key}", 300, function () use ($key) {
            $record = static::where('page_key', $key)->first();
            return $record?->toSeoArray();
        });
    }

    public static function clearCache(string $key): void
    {
        Cache::forget("page_seo:{$key}");
    }

    public function toSeoArray(): array
    {
        return [
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'og_title'         => $this->og_title ?: $this->meta_title,
            'og_description'   => $this->og_description ?: $this->meta_description,
            'og_image_url'     => $this->og_image_url,
            'robots'           => $this->robots ?? 'index,follow',
            'canonical_url'    => $this->canonical_url,
            'schema_json'      => $this->schema_json,
        ];
    }
}
