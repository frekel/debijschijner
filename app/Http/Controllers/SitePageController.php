<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Throwable;

class SitePageController extends Controller
{
    public function show(string $path = ''): Response
    {
        $normalizedPath = trim($path, '/');
        $slug = $normalizedPath === '' ? 'home' : $normalizedPath;

        try {
            $page = Page::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first();
        } catch (Throwable) {
            $page = null;
        }

        if ($page) {
            $html = $slug === 'contact'
                ? $this->decorateContactPageHtml($page->html)
                : $page->html;

            return response($html, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }

        $candidates = $normalizedPath === ''
            ? ['home.html']
            : [
                $normalizedPath.'.html',
                str_replace('/', '__', $normalizedPath).'.html',
            ];

        $filePath = null;

        foreach ($candidates as $candidate) {
            $candidatePath = resource_path('migrated/site/'.$candidate);

            if (File::exists($candidatePath)) {
                $filePath = $candidatePath;
                break;
            }
        }

        if (! $filePath) {
            abort(404);
        }

        $html = File::get($filePath);

        if ($slug === 'contact') {
            $html = $this->decorateContactPageHtml($html);
        }

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function decorateContactPageHtml(string $html): string
    {
        $html = preg_replace_callback(
            '/<form\b[^>]*id=["\']wpforms-form-\d+["\'][^>]*>/i',
            function (array $matches): string {
                $tag = $matches[0];

                $tag = preg_replace_callback('/\sclass=("|\')(.*?)\1/i', function (array $classMatches): string {
                    $quote = $classMatches[1];
                    $classes = preg_split('/\s+/', trim($classMatches[2])) ?: [];
                    $classes = array_values(array_filter($classes, fn (string $class) => $class !== 'wpforms-ajax-form'));

                    return ' class='.$quote.implode(' ', $classes).$quote;
                }, $tag) ?? $tag;

                if (preg_match('/\saction=("|\')(.*?)\1/i', $tag)) {
                    $tag = preg_replace('/\saction=("|\')(.*?)\1/i', ' action="/contact"', $tag) ?? $tag;
                } else {
                    $tag = rtrim($tag, '>').' action="/contact">';
                }

                return $tag;
            },
            $html,
            1
        ) ?? $html;

        $html = preg_replace('/<script[^>]+wpforms[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/<script[^>]+src=["\'][^"\']*wpforms-lite\/assets\/js\/[^"\']*["\'][^>]*><\/script>/is', '', $html) ?? $html;

        $html = preg_replace(
            '/(<form\b[^>]*id=["\']wpforms-form-\d+["\'][^>]*>)/i',
            '$1'.csrf_field(),
            $html,
            1
        ) ?? $html;

        $feedback = $this->buildContactFeedbackHtml();

        if ($feedback !== '') {
            $html = preg_replace(
                '/(<div class="wpforms-container[^>]*>)/i',
                $feedback.'$1',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    private function buildContactFeedbackHtml(): string
    {
        $chunks = [];

        $success = session('contact_success');

        if (is_string($success) && $success !== '') {
            $chunks[] = sprintf(
                '<div style="margin:16px 0;padding:12px 16px;border:1px solid #2e7d32;background:#e8f5e9;color:#1b5e20;border-radius:4px;">%s</div>',
                e($success)
            );
        }

        $errors = session('errors');

        if ($errors instanceof ViewErrorBag) {
            $errorBag = $errors->getBag('default');

            if ($errorBag instanceof MessageBag && $errorBag->any()) {
                $listItems = collect($errorBag->all())
                    ->map(fn (string $message) => '<li>'.e($message).'</li>')
                    ->implode('');

                $chunks[] = '<div style="margin:16px 0;padding:12px 16px;border:1px solid #c62828;background:#ffebee;color:#b71c1c;border-radius:4px;">'
                    .'<strong>Controleer het formulier:</strong><ul style="margin:8px 0 0 20px;">'.$listItems.'</ul>'
                    .'</div>';
            }
        }

        return implode('', $chunks);
    }
}
