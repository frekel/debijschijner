<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
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

        abort(404);
    }
}
