<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\PromoHit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SitePageController extends Controller
{
    public function show(Request $request, string $path = ''): Response
    {
        $normalizedPath = trim($path, '/');
        $slug = $normalizedPath === '' ? 'home' : $normalizedPath;

        if ($normalizedPath === 'publicaties') {
            return redirect('/over-mij/publicaties', 301);
        }

        if (Str::startsWith($normalizedPath, 'publicaties/')) {
            return redirect('/over-mij/publicaties/'.trim(Str::after($normalizedPath, 'publicaties/'), '/'), 301);
        }

        if (Str::startsWith($normalizedPath, 'over-mij/publicaties/')) {
            return $this->renderPublicationPageResponse($normalizedPath);
        }

        if (Str::startsWith($normalizedPath, 'ervaringen/')) {
            $reviewerResponse = $this->renderReviewerPageResponse($normalizedPath);

            if ($reviewerResponse instanceof Response) {
                return $reviewerResponse;
            }
        }

        try {
            $page = Page::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first();
        } catch (Throwable) {
            $page = null;
        }

        if ($page) {
            if (($page->template ?? 'default') === 'promo') {
                $this->recordPromoHit($request, $page, $normalizedPath);

                return redirect((string) ($page->promo_redirect_url ?: '/'));
            }

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

        abort(404);
    }

    private function recordPromoHit(Request $request, Page $page, string $path): void
    {
        try {
            PromoHit::query()->create([
                'page_id' => $page->id,
                'page_slug' => $page->slug,
                'page_title' => $page->title,
                'path' => $path,
                'full_url' => $request->fullUrl(),
                'redirect_target' => (string) ($page->promo_redirect_url ?: '/'),
                'method' => $request->method(),
                'host' => $request->getHost(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'accept_language' => $request->headers->get('accept-language'),
                'query_params' => $request->query(),
                'headers' => collect($request->headers->all())
                    ->except(['cookie'])
                    ->map(fn (array $values): array|string => count($values) === 1 ? $values[0] : $values)
                    ->all(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Promo hit could not be stored.', ['exception' => $exception]);
        }
    }

    private function renderPublicationsIndexResponse(): Response
    {
        $page = $this->findPublishedPage('over-mij/publicaties');
        $publications = Post::query()
            ->published()
            ->ofType('publicatie')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $contentHtml = view('cms.posts.publications-index', [
            'publications' => $publications,
            'resolvePublicationUrl' => fn (Post $post): string => '/over-mij/publicaties/'.$this->publicationSlug($post),
            'resolveImageUrl' => fn (?string $path): ?string => $this->resolvePublicUrl($path),
        ])->render();

        $html = view('cms.page', [
            'layoutView' => $this->resolveLayoutView((string) ($page->template ?? 'default')),
            'page' => $page,
            'title' => $page?->meta_title ?: $page?->title ?: 'Publicaties',
            'metaDescription' => $page?->meta_description ?: 'Publicaties',
            'canonicalUrl' => $page?->canonical_url ?: url('/over-mij/publicaties'),
            'ogImage' => $this->resolvePublicUrl($page?->og_image),
            'contentHtml' => $contentHtml,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function renderPublicationPageResponse(string $normalizedPath): Response
    {
        $publicationSlug = trim(Str::after($normalizedPath, 'over-mij/publicaties/'), '/');
        $publication = Post::query()
            ->published()
            ->ofType('publicatie')
            ->get()
            ->first(fn (Post $post): bool => $this->publicationSlug($post) === $publicationSlug);

        if (! $publication) {
            abort(404);
        }

        $contentHtml = view('cms.posts.publication-detail', [
            'publication' => $publication,
            'image' => $this->resolvePublicUrl($publication->image),
        ])->render();

        $html = view('cms.page', [
            'layoutView' => 'cms.layouts.default',
            'title' => (string) ($publication->title ?? 'Publicatie'),
            'metaDescription' => strip_tags((string) ($publication->text ?? '')),
            'canonicalUrl' => url('/over-mij/publicaties/'.$publicationSlug),
            'ogImage' => $this->resolvePublicUrl($publication->image),
            'contentHtml' => $contentHtml,
            'page' => null,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function renderReviewerPageResponse(string $normalizedPath): ?Response
    {
        $reviewerSlug = trim(Str::after($normalizedPath, 'ervaringen/'), '/');

        if ($reviewerSlug === '') {
            return null;
        }

        $reviewer = Post::query()
            ->published()
            ->ofType('reviewer')
            ->get()
            ->first(fn (Post $post): bool => $this->reviewerSlug($post) === $reviewerSlug);

        if (! $reviewer) {
            abort(404);
        }

        $contentHtml = view('cms.posts.reviewer-detail', [
            'title' => (string) ($reviewer->title ?? ''),
            'text' => (string) ($reviewer->text ?? ''),
            'image' => $this->resolvePublicUrl($reviewer->image),
            'reviewerName' => trim((string) ($reviewer->name ?? '')),
        ])->render();

        $html = view('cms.page', [
            'layoutView' => 'cms.layouts.default',
            'title' => trim((string) ($reviewer->name ?? '')),
            'metaDescription' => (string) ($reviewer->text ?? ''),
            'canonicalUrl' => url('/ervaringen/'.$reviewerSlug),
            'ogImage' => $this->resolvePublicUrl($reviewer->image),
            'contentHtml' => $contentHtml,
            'page' => null,
        ])->render();

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
        $hasReviewsGroup = false;
        $hasPublicationsGroup = false;

        foreach ($blocks as $block) {
            $type = $block['type'] ?? '';

            if ($type === 'process_post') {
                if ($pricesItems !== '') {
                    $html .= $this->renderPricesGroup($pricesItems);
                    $pricesItems = '';
                }

                if ($hasPublicationsGroup) {
                    $html .= $this->renderPublicationsGroup();
                    $hasPublicationsGroup = false;
                }

                if ($hasReviewsGroup) {
                    $html .= $this->renderReviewsGroup();
                    $hasReviewsGroup = false;
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

                if ($hasPublicationsGroup) {
                    $html .= $this->renderPublicationsGroup();
                    $hasPublicationsGroup = false;
                }

                if ($hasReviewsGroup) {
                    $html .= $this->renderReviewsGroup();
                    $hasReviewsGroup = false;
                }

                $data = is_array($block['data'] ?? null) ? $block['data'] : [];
                $pricesItems .= $this->renderPricesBlock($data);

                continue;
            }

            if (in_array($type, ['review', 'reviews'], true)) {
                if ($processPostItems !== '') {
                    $html .= $this->renderProcessPostGroup($processPostItems);
                    $processPostItems = '';
                }

                if ($pricesItems !== '') {
                    $html .= $this->renderPricesGroup($pricesItems);
                    $pricesItems = '';
                }

                // The page only controls the position of this group. Its contents
                // are managed independently as reviewer posts.
                $hasReviewsGroup = true;

                continue;
            }

            if (in_array($type, ['publications', 'publicaties'], true)) {
                if ($processPostItems !== '') {
                    $html .= $this->renderProcessPostGroup($processPostItems);
                    $processPostItems = '';
                }

                if ($pricesItems !== '') {
                    $html .= $this->renderPricesGroup($pricesItems);
                    $pricesItems = '';
                }

                if ($hasReviewsGroup) {
                    $html .= $this->renderReviewsGroup();
                    $hasReviewsGroup = false;
                }

                $hasPublicationsGroup = true;

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

            if ($hasPublicationsGroup) {
                $html .= $this->renderPublicationsGroup();
                $hasPublicationsGroup = false;
            }

            if ($hasReviewsGroup) {
                $html .= $this->renderReviewsGroup();
                $hasReviewsGroup = false;
            }

            $html .= $this->renderBlock($block, $processPostIndex);
        }

        if ($processPostItems !== '') {
            $html .= $this->renderProcessPostGroup($processPostItems);
        }

        if ($pricesItems !== '') {
            $html .= $this->renderPricesGroup($pricesItems);
        }

        if ($hasPublicationsGroup) {
            $html .= $this->renderPublicationsGroup();
        }

        if ($hasReviewsGroup) {
            $html .= $this->renderReviewsGroup();
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

    private function renderReviewsGroup(): string
    {
        $itemsHtml = Post::query()
            ->published()
            ->ofType('reviewer')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Post $post): string => $this->renderReviewBlock([
                'reviewer_slug' => $this->reviewerSlug($post),
                'reviewer_name' => $post->name,
                'button_text' => $post->button_text,
                'title' => $post->title,
                'image' => $post->image,
                'text' => $post->text,
            ]))
            ->implode('');

        if ($itemsHtml === '') {
            return '';
        }

        return view('cms.groups.reviews', [
            'itemsHtml' => $itemsHtml,
        ])->render();
    }

    private function renderPublicationsGroup(): string
    {
        $publications = Post::query()
            ->published()
            ->ofType('publicatie')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($publications->isEmpty()) {
            return '';
        }

        return view('cms.posts.publications-index', [
            'publications' => $publications,
            'resolvePublicationUrl' => fn (Post $post): string => '/over-mij/publicaties/'.$this->publicationSlug($post),
            'resolveImageUrl' => fn (?string $path): ?string => $this->resolvePublicUrl($path),
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
            'review', 'reviews' => $this->renderReviewsGroup(),
            'publications', 'publicaties' => $this->renderPublicationsGroup(),
            'contact_form' => $this->renderContactFormBlock(),
            'apply_form' => $this->renderApplyFormBlock(),
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
            'reviewerSlug' => trim((string) ($data['reviewer_slug'] ?? '')),
            'reviewerName' => trim((string) ($data['reviewer_name'] ?? '')),
            'buttonText' => trim((string) ($data['button_text'] ?? '')),
            'title' => (string) ($data['title'] ?? ''),
            'image' => $this->resolvePublicUrl((string) ($data['image'] ?? '')),
            'text' => (string) ($data['text'] ?? ''),
        ])->render();
    }

    private function renderContactFormBlock(): string
    {
        return view('cms.blocks.contact-form')->render();
    }

    private function renderApplyFormBlock(): string
    {
        return view('cms.blocks.apply-form')->render();
    }

    private function findPublishedPage(string $slug): ?Page
    {
        try {
            return Page::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first();
        } catch (Throwable) {
            return null;
        }
    }

    private function reviewerSlug(Post $post): string
    {
        $slug = trim((string) ($post->slug ?? ''));

        if ($slug !== '') {
            return trim($slug, '/');
        }

        return Str::slug(Str::lower(trim((string) ($post->name ?? ''))));
    }

    private function publicationSlug(Post $post): string
    {
        $slug = trim((string) ($post->slug ?? ''));

        if ($slug !== '') {
            return trim($slug, '/');
        }

        return Str::slug(Str::lower(trim((string) ($post->title ?? ''))));
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
            'form' => 'cms.layouts.form',
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
            '/<form\b[^>]*id=["\']bsforms-form-\d+["\'][^>]*>/i',
            function (array $matches): string {
                $tag = $matches[0];

                $tag = preg_replace_callback('/\sclass=("|\')(.*?)\1/i', function (array $classMatches): string {
                    $quote = $classMatches[1];
                    $classes = preg_split('/\s+/', trim($classMatches[2])) ?: [];
                    $classes = array_values(array_filter($classes, fn (string $class) => $class !== 'bsforms-ajax-form'));

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

        $html = preg_replace('/<script[^>]+bsforms[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/<script[^>]+src=["\'][^"\']*bsforms-lite\/assets\/js\/[^"\']*["\'][^>]*><\/script>/is', '', $html) ?? $html;

        $html = preg_replace(
            '/(<form\b[^>]*id=["\']bsforms-form-\d+["\'][^>]*>)/i',
            '$1'.csrf_field(),
            $html,
            1
        ) ?? $html;

        $feedback = $this->buildContactFeedbackHtml();

        if ($feedback !== '') {
            $html = preg_replace(
                '/(<div class="bsforms-container[^>]*>)/i',
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
            '/<form\b[^>]*id=["\']bsforms-form-\d+["\'][^>]*>/i',
            function (array $matches): string {
                $tag = $matches[0];

                $tag = preg_replace_callback('/\sclass=("|\')(.*?)\1/i', function (array $classMatches): string {
                    $quote = $classMatches[1];
                    $classes = preg_split('/\s+/', trim($classMatches[2])) ?: [];
                    $classes = array_values(array_filter($classes, fn (string $class) => $class !== 'bsforms-ajax-form'));

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

        $html = preg_replace('/<script[^>]+bsforms[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/<script[^>]+src=["\'][^"\']*bsforms-lite\/assets\/js\/[^"\']*["\'][^>]*><\/script>/is', '', $html) ?? $html;

        $html = preg_replace(
            '/(<form\b[^>]*id=["\']bsforms-form-\d+["\'][^>]*>)/i',
            '$1'.csrf_field(),
            $html,
            1
        ) ?? $html;

        $feedback = $this->buildApplyFeedbackHtml();

        if ($feedback !== '') {
            $html = preg_replace(
                '/(<div class="bsforms-container[^>]*>)/i',
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
