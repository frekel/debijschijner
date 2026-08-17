<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            if ($this->hasContentBlocks($page)) {
                $html = view('cms.page', [
                    'layoutView' => $this->resolveLayoutView((string) ($page->template ?? 'default')),
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
        $processPostIndex = 0;
        $processPostItems = '';
        $pricesItems = '';
        $reviewItems = '';

        foreach ($blocks as $block) {
            $type = $block['type'] ?? '';

            if ($type === 'process_post') {
                if ($pricesItems !== '') {
                    $html .= $this->renderPricesGroup($pricesItems);
                    $pricesItems = '';
                }

                if ($reviewItems !== '') {
                    $html .= $this->renderReviewsGroup($reviewItems);
                    $reviewItems = '';
                }

                $data = is_array($block['data'] ?? null) ? $block['data'] : [];
                $processPostItems .= $this->renderProcessPostBlock($data, $processPostIndex++);

                continue;
            }

            if ($type === 'prices') {
                if ($processPostItems !== '') {
                    $html .= $this->renderProcessPostGroup($processPostItems);
                    $processPostItems = '';
                }

                if ($reviewItems !== '') {
                    $html .= $this->renderReviewsGroup($reviewItems);
                    $reviewItems = '';
                }

                $data = is_array($block['data'] ?? null) ? $block['data'] : [];
                $pricesItems .= $this->renderPricesBlock($data);

                continue;
            }

            if ($type === 'review') {
                if ($processPostItems !== '') {
                    $html .= $this->renderProcessPostGroup($processPostItems);
                    $processPostItems = '';
                }

                if ($pricesItems !== '') {
                    $html .= $this->renderPricesGroup($pricesItems);
                    $pricesItems = '';
                }

                $data = is_array($block['data'] ?? null) ? $block['data'] : [];
                $reviewItems .= $this->renderReviewBlock($data);

                continue;
            }

            if ($processPostItems !== '') {
                $html .= $this->renderProcessPostGroup($processPostItems);
                $processPostItems = '';
            }

            if ($pricesItems !== '') {
                $html .= $this->renderPricesGroup($pricesItems);
                $pricesItems = '';
            }

            if ($reviewItems !== '') {
                $html .= $this->renderReviewsGroup($reviewItems);
                $reviewItems = '';
            }

            $html .= $this->renderBlock($block, $processPostIndex);
        }

        if ($processPostItems !== '') {
            $html .= $this->renderProcessPostGroup($processPostItems);
        }

        if ($pricesItems !== '') {
            $html .= $this->renderPricesGroup($pricesItems);
        }

        if ($reviewItems !== '') {
            $html .= $this->renderReviewsGroup($reviewItems);
        }

        return $html;
    }

    private function renderProcessPostGroup(string $itemsHtml): string
    {
        return view('cms.groups.process-post', [
            'itemsHtml' => $itemsHtml,
        ])->render();
    }

    private function renderPricesGroup(string $itemsHtml): string
    {
        return view('cms.groups.prices', [
            'itemsHtml' => $itemsHtml,
        ])->render();
    }

    private function renderReviewsGroup(string $itemsHtml): string
    {
        return view('cms.groups.reviews', [
            'itemsHtml' => $itemsHtml,
        ])->render();
    }

    private function renderBlock(array $block, int &$processPostIndex): string
    {
        $type = $block['type'] ?? '';
        $data = is_array($block['data'] ?? null) ? $block['data'] : [];

        return match ($type) {
            'rich_text' => $this->renderRichTextBlock($data),
            'image_text' => $this->renderImageTextBlock($data),
            'quote' => $this->renderQuoteBlock($data),
            'homepage_post' => $this->renderHomePagePostBlock($data),
            'process_post' => $this->renderProcessPostBlock($data, $processPostIndex++),
            'prices' => $this->renderPricesBlock($data),
            'review' => $this->renderReviewBlock($data),
            default => '',
        };
    }

    private function renderRichTextBlock(array $data): string
    {
        $editorMode = (string) ($data['editor_mode'] ?? 'wysiwyg');
        $body = $editorMode === 'html'
            ? (string) ($data['body_html'] ?? '')
            : (string) ($data['body'] ?? '');

        return view('cms.blocks.rich-text', [
            'heading' => (string) ($data['heading'] ?? ''),
            'body' => $body,
        ])->render();
    }

    private function renderImageTextBlock(array $data): string
    {
        $image = $this->resolvePublicUrl((string) ($data['image'] ?? ''));
        $editorMode = (string) ($data['editor_mode'] ?? 'wysiwyg');
        $body = $editorMode === 'html'
            ? (string) ($data['body_html'] ?? '')
            : (string) ($data['body'] ?? '');

        return view('cms.blocks.image-text', [
            'image' => $image,
            'alt' => (string) ($data['alt'] ?? ''),
            'heading' => (string) ($data['heading'] ?? ''),
            'body' => $body,
        ])->render();
    }

    private function renderQuoteBlock(array $data): string
    {
        $quoteText = trim((string) ($data['quote_text'] ?? ''));
        $quoteAuthor = trim((string) ($data['quote_author'] ?? ''));

        if ($quoteText === '' && $quoteAuthor === '') {
            return '';
        }

        return view('cms.blocks.quote', [
            'quoteText' => $quoteText,
            'quoteAuthor' => $quoteAuthor,
        ])->render();
    }

    private function renderHomePagePostBlock(array $data): string
    {
        return view('cms.blocks.homepage-tekst', [
            'title' => (string) ($data['title'] ?? ''),
            'text' => (string) ($data['text'] ?? ''),
        ])->render();
    }

    private function renderProcessPostBlock(array $data, int $index): string
    {
        return view('cms.blocks.process-post', [
            'title' => (string) ($data['title'] ?? ''),
            'time' => trim((string) ($data['time'] ?? '')),
            'text' => (string) ($data['text'] ?? ''),
            'itemIndex' => $index,
            'parityClass' => $index % 2 === 0 ? 'odd timeline-right' : 'even timeline-left',
        ])->render();
    }

    private function renderPricesBlock(array $data): string
    {
        return view('cms.blocks.prices', [
            'price' => trim((string) ($data['price'] ?? '')),
            'title' => (string) ($data['title'] ?? ''),
            'text' => (string) ($data['text'] ?? ''),
        ])->render();
    }

    private function renderReviewBlock(array $data): string
    {
        return view('cms.blocks.review', [
            'reviewerName' => trim((string) ($data['reviewer_name'] ?? '')),
            'buttonText' => trim((string) ($data['button_text'] ?? '')),
            'title' => (string) ($data['title'] ?? ''),
            'image' => $this->resolvePublicUrl((string) ($data['image'] ?? '')),
            'text' => (string) ($data['text'] ?? ''),
        ])->render();
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

    private function resolveLayoutView(string $template): string
    {
        return match ($template) {
            'homepage' => 'cms.layouts.homepage',
            'full_screen' => 'cms.layouts.full-screen',
            default => 'cms.layouts.default',
        };
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
        return $this->renderFormFeedbackHtml('contact_success');
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
        return $this->renderFormFeedbackHtml('apply_success');
    }

    private function renderFormFeedbackHtml(string $successSessionKey): string
    {
        $success = session($successSessionKey);
        $successMessage = is_string($success) ? trim($success) : '';

        $errors = session('errors');
        $errorMessages = [];

        if ($errors instanceof \Illuminate\Support\ViewErrorBag) {
            $errorMessages = $errors->getBag('default')->all();
        } elseif (is_object($errors) && method_exists($errors, 'all')) {
            $errorMessages = $errors->all();
        }

        if ($successMessage === '' && count($errorMessages) === 0) {
            return '';
        }

        return view('cms.partials.form-feedback', [
            'successMessage' => $successMessage,
            'errorMessages' => $errorMessages,
            'errorTitle' => 'Controleer het formulier:',
        ])->render();
    }
}
