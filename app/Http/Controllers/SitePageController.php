<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Str;
use Throwable;

class SitePageController extends Controller
{
    public function showOverMijBlade(): Response
    {
        try {
            $page = Page::query()
                ->where('slug', 'over-mij')
                ->first();
        } catch (Throwable) {
            $page = null;
        }

        $html = view('cms.templates.over-mij', [
            'page' => $page,
            'title' => $page?->meta_title ?: ($page?->title ?: 'Over mij'),
            'metaDescription' => $page?->meta_description,
            'canonicalUrl' => $page?->canonical_url,
            'ogImage' => $this->resolvePublicUrl($page?->og_image),
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

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
            if ($this->hasContentBlocks($page)) {
                $html = view('cms.page', [
                    'page' => $page,
                    'title' => $page->meta_title ?: $page->title,
                    'metaDescription' => $page->meta_description,
                    'canonicalUrl' => $page->canonical_url,
                    'ogImage' => $this->resolvePublicUrl($page->og_image),
                    'contentHtml' => $this->renderBlocks($page->content_blocks ?? []),
                ])->render();

                return response($html, 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ]);
            }

            $html = $slug === 'contact'
                ? $this->decorateContactPageHtml($page->html)
                : ($slug === 'aanvraag' ? $this->decorateApplyPageHtml($page->html) : $page->html);

            $html = $this->applySeoMetadata($html, $page);

            $html = $this->localizeExternalAssetReferences($html);

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
        } elseif ($slug === 'aanvraag') {
            $html = $this->decorateApplyPageHtml($html);
        }

        $html = $this->localizeExternalAssetReferences($html);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function hasContentBlocks(Page $page): bool
    {
        return is_array($page->content_blocks) && count($page->content_blocks) > 0;
    }

    private function renderBlocks(array $blocks): string
    {
        $html = '';

        foreach ($blocks as $block) {
            $html .= $this->renderBlock($block);
        }

        return $html;
    }

    private function renderBlock(array $block): string
    {
        $type = $block['type'] ?? '';
        $data = is_array($block['data'] ?? null) ? $block['data'] : [];

        return match ($type) {
            'hero' => $this->renderHeroBlock($data),
            'rich_text' => $this->renderRichTextBlock($data),
            'image_text' => $this->renderImageTextBlock($data),
            'cta' => $this->renderCtaBlock($data),
            'html' => $this->renderHtmlBlock($data),
            default => '',
        };
    }

    private function renderHeroBlock(array $data): string
    {
        $heading = e((string) ($data['heading'] ?? ''));
        $subheading = e((string) ($data['subheading'] ?? ''));
        $buttonText = e((string) ($data['button_text'] ?? ''));
        $buttonUrl = e((string) ($data['button_url'] ?? '#'));

        return '<section class="cms-section cms-hero">'
            .'<div class="cms-container">'
            .'<h1>'.$heading.'</h1>'
            .($subheading !== '' ? '<p class="cms-subheading">'.$subheading.'</p>' : '')
            .($buttonText !== '' ? '<p><a class="cms-button" href="'.$buttonUrl.'">'.$buttonText.'</a></p>' : '')
            .'</div>'
            .'</section>';
    }

    private function renderRichTextBlock(array $data): string
    {
        $heading = e((string) ($data['heading'] ?? ''));
        $editorMode = (string) ($data['editor_mode'] ?? 'wysiwyg');
        $body = $editorMode === 'html'
            ? (string) ($data['body_html'] ?? '')
            : (string) ($data['body'] ?? '');

        return '<section class="cms-section">'
            .'<div class="cms-container">'
            .($heading !== '' ? '<h2>'.$heading.'</h2>' : '')
            .'<div class="cms-richtext">'.$body.'</div>'
            .'</div>'
            .'</section>';
    }

    private function renderImageTextBlock(array $data): string
    {
        $image = $this->resolvePublicUrl((string) ($data['image'] ?? ''));
        $alt = e((string) ($data['alt'] ?? ''));
        $heading = e((string) ($data['heading'] ?? ''));
        $editorMode = (string) ($data['editor_mode'] ?? 'wysiwyg');
        $body = $editorMode === 'html'
            ? (string) ($data['body_html'] ?? '')
            : (string) ($data['body'] ?? '');

        return '<section class="cms-section">'
            .'<div class="cms-container cms-image-text">'
            .($image ? '<div class="cms-image"><img src="'.e($image).'" alt="'.$alt.'"></div>' : '')
            .'<div class="cms-copy">'
            .($heading !== '' ? '<h2>'.$heading.'</h2>' : '')
            .'<div class="cms-richtext">'.$body.'</div>'
            .'</div>'
            .'</div>'
            .'</section>';
    }

    private function renderCtaBlock(array $data): string
    {
        $heading = e((string) ($data['heading'] ?? ''));
        $text = e((string) ($data['text'] ?? ''));
        $buttonText = e((string) ($data['button_text'] ?? ''));
        $buttonUrl = e((string) ($data['button_url'] ?? '#'));

        return '<section class="cms-section cms-cta">'
            .'<div class="cms-container">'
            .'<h2>'.$heading.'</h2>'
            .($text !== '' ? '<p>'.$text.'</p>' : '')
            .($buttonText !== '' ? '<p><a class="cms-button" href="'.$buttonUrl.'">'.$buttonText.'</a></p>' : '')
            .'</div>'
            .'</section>';
    }

    private function renderHtmlBlock(array $data): string
    {
        $heading = (string) ($data['heading'] ?? '');
        $code = (string) ($data['code'] ?? '');

        // Render raw HTML code as-is (not escaped)
        return ($heading !== '' ? '<div class="cms-section"><h2>'.e($heading).'</h2></div>' : '')
            . '<div class="cms-section cms-html-block">'.$code.'</div>';
    }

    private function resolvePublicUrl(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'images/upload/')) {
            return '/'.$path;
        }

        if (Storage::disk('public_uploads')->exists($path)) {
            return Storage::disk('public_uploads')->url($path);
        }

        return Storage::disk('public')->url($path);
    }

    private function applySeoMetadata(string $html, Page $page): string
    {
        $metaTitle = trim((string) ($page->meta_title ?? ''));
        $metaDescription = trim((string) ($page->meta_description ?? ''));
        $canonicalUrl = trim((string) ($page->canonical_url ?? ''));
        $ogImage = $this->resolvePublicUrl($page->og_image);

        if ($metaTitle !== '') {
            if (preg_match('/<title>.*?<\/title>/is', $html)) {
                $html = preg_replace('/<title>.*?<\/title>/is', '<title>'.e($metaTitle).'</title>', $html, 1) ?? $html;
            } else {
                $html = preg_replace('/<head[^>]*>/i', '$0<title>'.e($metaTitle).'</title>', $html, 1) ?? $html;
            }
        }

        if ($metaDescription !== '') {
            $descriptionTag = '<meta name="description" content="'.e($metaDescription).'">';

            if (preg_match('/<meta\s+name=["\']description["\'][^>]*>/i', $html)) {
                $html = preg_replace('/<meta\s+name=["\']description["\'][^>]*>/i', $descriptionTag, $html, 1) ?? $html;
            } else {
                $html = preg_replace('/<head[^>]*>/i', '$0'.$descriptionTag, $html, 1) ?? $html;
            }
        }

        if ($canonicalUrl !== '') {
            $canonicalTag = '<link rel="canonical" href="'.e($canonicalUrl).'">';

            if (preg_match('/<link\s+rel=["\']canonical["\'][^>]*>/i', $html)) {
                $html = preg_replace('/<link\s+rel=["\']canonical["\'][^>]*>/i', $canonicalTag, $html, 1) ?? $html;
            } else {
                $html = preg_replace('/<head[^>]*>/i', '$0'.$canonicalTag, $html, 1) ?? $html;
            }
        }

        if (is_string($ogImage) && $ogImage !== '') {
            $ogTag = '<meta property="og:image" content="'.e($ogImage).'">';

            if (preg_match('/<meta\s+property=["\']og:image["\'][^>]*>/i', $html)) {
                $html = preg_replace('/<meta\s+property=["\']og:image["\'][^>]*>/i', $ogTag, $html, 1) ?? $html;
            } else {
                $html = preg_replace('/<head[^>]*>/i', '$0'.$ogTag, $html, 1) ?? $html;
            }
        }

        return $html;
    }

    private function localizeExternalAssetReferences(string $html): string
    {
        $html = str_replace(
            [
                "<link rel='dns-prefetch' href='//www.googletagmanager.com' />",
                "<link rel='dns-prefetch' href='//fonts.googleapis.com' />",
            ],
            '',
            $html,
        );

        $html = preg_replace_callback('/href=("|\')([^"\']*fonts\.googleapis\.com[^"\']*)\1/i', function (array $matches): string {
            $quote = $matches[1];
            $url = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (Str::startsWith($url, '//')) {
                $url = 'https:'.$url;
            }

            if (! Str::startsWith($url, 'https://fonts.googleapis.com')) {
                return $matches[0];
            }

            $local = '/css/external/fonts/google/'.substr(sha1($url), 0, 12).'.css';

            return 'href='.$quote.$local.$quote;
        }, $html) ?? $html;

        $html = preg_replace_callback('/src=("|\')https:\/\/www\.googletagmanager\.com\/gtag\/js\?id=GT-TX9T54P6\1/i', function (array $matches): string {
            $quote = $matches[1];

            return 'src='.$quote.'/js/external/googletag/gtag.js'.$quote;
        }, $html) ?? $html;

        return $html;
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

    private function decorateApplyPageHtml(string $html): string
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
                    $tag = preg_replace('/\saction=("|\')(.*?)\1/i', ' action="/aanvraag"', $tag) ?? $tag;
                } else {
                    $tag = rtrim($tag, '>').' action="/aanvraag">';
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

        $feedback = $this->buildApplyFeedbackHtml();

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

    private function buildApplyFeedbackHtml(): string
    {
        $chunks = [];

        $success = session('apply_success');

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
