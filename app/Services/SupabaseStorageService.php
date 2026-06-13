<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SupabaseStorageService
{
    private string $url;
    private string $serviceKey;
    private string $bucket;

    public function __construct()
    {
        $this->url        = rtrim(config('services.supabase.url'), '/');
        $this->serviceKey = config('services.supabase.service_key');
        $this->bucket     = config('services.supabase.storage_bucket', 'portfolio-media');
    }

    /**
     * Upload a file to Supabase Storage and return [path, url].
     */
    public function upload(UploadedFile $file, string $path): array
    {
        $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";

        $response = Http::withToken($this->serviceKey)
            ->withHeaders([
                'Content-Type' => $file->getMimeType(),
                'x-upsert'     => 'true',
            ])
            ->withBody($file->getContent(), $file->getMimeType())
            ->post($endpoint);

        if ($response->failed()) {
            Log::error('Supabase upload failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'path'   => $path,
            ]);
            throw new RuntimeException('อัปโหลดไฟล์ไม่สำเร็จ: ' . $response->body());
        }

        return [
            'path' => $path,
            'url'  => "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}",
        ];
    }

    /**
     * Delete one or more files from Supabase Storage.
     */
    public function delete(string|array $paths): void
    {
        $prefixes = is_array($paths) ? $paths : [$paths];

        $response = Http::withToken($this->serviceKey)
            ->delete("{$this->url}/storage/v1/object/{$this->bucket}", [
                'prefixes' => $prefixes,
            ]);

        if ($response->failed()) {
            Log::warning('Supabase delete failed', [
                'paths'  => $prefixes,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        }
    }

    public function getBucket(): string
    {
        return $this->bucket;
    }
}
