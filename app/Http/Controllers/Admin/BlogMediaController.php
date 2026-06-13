<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogMedia;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;

class BlogMediaController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function upload(Request $request)
    {
        $request->validate([
            'file'      => 'required|file|image|max:5120',
            'blog_uuid' => 'required|string|max:36',
            'alt_text'  => 'nullable|string|max:255',
        ]);

        $file     = $request->file('file');
        $blogUuid = $request->input('blog_uuid');
        $ext      = $file->getClientOriginalExtension();
        $mediaId  = \Illuminate\Support\Str::uuid();
        $path     = "articles/{$blogUuid}/content/{$mediaId}.{$ext}";

        $uploaded = $this->storage->upload($file, $path);

        $media = BlogMedia::create([
            'id'           => (string) $mediaId,
            'blog_uuid'    => $blogUuid,
            'storage_path' => $uploaded['path'],
            'url'          => $uploaded['url'],
            'alt_text'     => $request->input('alt_text'),
            'file_size'    => $file->getSize(),
            'mime_type'    => $file->getMimeType(),
        ]);

        return response()->json([
            'url'      => $media->url,
            'media_id' => $media->id,
        ]);
    }
}
