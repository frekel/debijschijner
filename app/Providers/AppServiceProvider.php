<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('cms.partials.menu', function ($view): void {
            $view->with('menuItems', $this->buildMenuItems());
        });
    }

    private function buildMenuItems(): array
    {
        $currentPath = trim(request()->path(), '/');

        $items = [
            $this->buildStaticHomeItem($currentPath),
        ];

        if (! Schema::hasTable('pages')) {
            return $items;
        }

        $pages = Page::query()
            ->where('is_published', true)
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['slug', 'title']);

        $pagesByParent = $pages->groupBy(function (Page $page): string {
            $slug = trim((string) $page->slug, '/');

            return Str::contains($slug, '/') ? Str::beforeLast($slug, '/') : '';
        });

        /** @var Collection<int, Page> $rootPages */
        $rootPages = $pagesByParent->get('', collect())
            ->reject(fn (Page $page): bool => trim((string) $page->slug, '/') === 'home')
            ->values();

        foreach ($rootPages as $page) {
            $items[] = $this->buildPageMenuItem($page, $pagesByParent, $currentPath);
        }

        return $items;
    }

    private function buildStaticHomeItem(string $currentPath): array
    {
        $children = [
            ['label' => 'Waarom de Bijschijner?', 'url' => '/#waarom-de-bijschijner', 'children' => [], 'isActive' => false, 'hasActiveDescendant' => false],
            ['label' => 'Waarom een bij als logo?', 'url' => '/#waarom-een-bij-als-logo', 'children' => [], 'isActive' => false, 'hasActiveDescendant' => false],
            ['label' => 'Krachtgericht coachen', 'url' => '/#krachtgericht-coachen', 'children' => [], 'isActive' => false, 'hasActiveDescendant' => false],
            ['label' => 'Beeldbegeleiding', 'url' => '/#beeldbegeleiding', 'children' => [], 'isActive' => false, 'hasActiveDescendant' => false],
        ];

        return [
            'label' => 'Home',
            'url' => '/',
            'children' => $children,
            'isActive' => $currentPath === '',
            'hasActiveDescendant' => false,
        ];
    }

    private function buildPageMenuItem(Page $page, Collection $pagesByParent, string $currentPath): array
    {
        $slug = trim((string) $page->slug, '/');
        $children = $slug === 'ervaringen'
            ? $this->buildReviewerMenuItems($currentPath)
            : $this->buildPageChildren($slug, $pagesByParent, $currentPath);

        $hasActiveDescendant = collect($children)->contains(
            fn (array $child): bool => ($child['isActive'] ?? false) || ($child['hasActiveDescendant'] ?? false)
        );

        return [
            'label' => (string) $page->title,
            'url' => $this->pageUrl($slug),
            'children' => $children,
            'isActive' => $this->pathMatches($currentPath, $slug),
            'hasActiveDescendant' => $hasActiveDescendant,
        ];
    }

    private function buildPageChildren(string $parentSlug, Collection $pagesByParent, string $currentPath): array
    {
        return $pagesByParent
            ->get($parentSlug, collect())
            ->map(fn (Page $page): array => $this->buildPageMenuItem($page, $pagesByParent, $currentPath))
            ->values()
            ->all();
    }

    private function buildReviewerMenuItems(string $currentPath): array
    {
        if (! Schema::hasTable('posts')) {
            return [];
        }

        return Post::query()
            ->published()
            ->ofType('reviewer')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['slug', 'name'])
            ->map(function (Post $post) use ($currentPath): array {
                $slug = $this->reviewerSlug($post);
                $path = 'ervaringen/'.$slug;

                return [
                    'label' => trim((string) ($post->name ?? '')),
                    'url' => '/'.$path.'/',
                    'children' => [],
                    'isActive' => $this->pathMatches($currentPath, $path),
                    'hasActiveDescendant' => false,
                ];
            })
            ->values()
            ->all();
    }

    private function reviewerSlug(Post $post): string
    {
        $slug = trim((string) ($post->slug ?? ''));

        if ($slug !== '') {
            return trim($slug, '/');
        }

        return Str::slug(Str::lower(trim((string) ($post->name ?? ''))));
    }

    private function pageUrl(string $slug): string
    {
        return $slug === '' ? '/' : '/'.$slug.'/';
    }

    private function pathMatches(string $currentPath, string $slug): bool
    {
        $slug = trim($slug, '/');

        if ($slug === '') {
            return $currentPath === '';
        }

        return $currentPath === $slug || Str::startsWith($currentPath, $slug.'/');
    }
}
