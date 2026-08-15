<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WpAssetController extends Controller
{
    public function serveWpContent(string $path): Response
    {
        return $this->serveAsset('wp-content', $path);
    }

    public function serveWpIncludes(string $path): Response
    {
        return $this->serveAsset('wp-includes', $path);
    }

    private function serveAsset(string $prefix, string $path): Response
    {
        if ($path === '' || Str::contains($path, '..')) {
            abort(404);
        }

        $relativePath = $prefix.'/'.ltrim($path, '/');
        $publicPath = public_path($relativePath);

        if (File::exists($publicPath) && File::isFile($publicPath)) {
            return response()->file($publicPath, [
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }

        $s3 = Storage::disk('s3');

        if (! $s3->exists($relativePath)) {
            abort(404);
        }

        $content = $s3->get($relativePath);
        $mime = $s3->mimeType($relativePath) ?: 'application/octet-stream';

        return response($content, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
