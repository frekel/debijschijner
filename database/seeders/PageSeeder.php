<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $directory = resource_path('migrated/site');

        if (! File::isDirectory($directory)) {
            return;
        }

        $files = File::files($directory);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'html') {
                continue;
            }

            $filename = $file->getFilename();
            $baseName = pathinfo($filename, PATHINFO_FILENAME);

            $slug = $baseName === 'home'
                ? 'home'
                : str_replace('__', '/', $baseName);

            $html = File::get($file->getPathname());
            $title = $this->extractTitle($html) ?? $this->titleFromSlug($slug);

            Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'html' => $html,
                    'is_published' => true,
                ]
            );
        }
    }

    private function extractTitle(string $html): ?string
    {
        if (! preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
            return null;
        }

        $title = html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim($title) !== '' ? trim($title) : null;
    }

    private function titleFromSlug(string $slug): string
    {
        if ($slug === 'home') {
            return 'Home';
        }

        return collect(explode('/', $slug))
            ->map(fn (string $part) => ucfirst(str_replace('-', ' ', $part)))
            ->join(' / ');
    }
}
