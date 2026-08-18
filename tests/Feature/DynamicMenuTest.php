<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_renders_pages_and_dynamic_reviewer_links(): void
    {
        Page::query()->create([
            'slug' => 'werkwijze',
            'title' => 'Werkwijze',
            'html' => '',
            'content_blocks' => [['type' => 'rich_text', 'data' => ['body' => '<p>Body</p>', 'editor_mode' => 'wysiwyg']]],
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Page::query()->create([
            'slug' => 'tarieven',
            'title' => 'Tarieven',
            'html' => '',
            'content_blocks' => [],
            'sort_order' => 2,
            'is_published' => true,
        ]);

        Page::query()->create([
            'slug' => 'ervaringen',
            'title' => 'Ervaringen',
            'html' => '',
            'content_blocks' => [['type' => 'reviews', 'data' => []]],
            'sort_order' => 3,
            'is_published' => true,
        ]);

        Page::query()->create([
            'slug' => 'over-mij',
            'title' => 'Over mij',
            'html' => '',
            'content_blocks' => [['type' => 'rich_text', 'data' => ['body' => '<p>Over mij</p>', 'editor_mode' => 'wysiwyg']]],
            'sort_order' => 4,
            'is_published' => true,
        ]);

        Page::query()->create([
            'slug' => 'over-mij/publicaties',
            'title' => 'Publicaties',
            'html' => '',
            'content_blocks' => [],
            'sort_order' => 5,
            'is_published' => true,
        ]);

        Page::query()->create([
            'slug' => 'contact',
            'title' => 'Contact',
            'content_blocks' => [],
            'sort_order' => 6,
            'is_published' => true,
        ]);

        Page::query()->create([
            'slug' => 'verborgen-pagina',
            'title' => 'Verborgen pagina',
            'content_blocks' => [],
            'sort_order' => 7,
            'is_published' => true,
            'show_in_menu' => false,
        ]);

        Post::query()->create([
            'type' => 'reviewer',
            'slug' => 'romy-van-drosthagen',
            'name' => 'Romy',
            'title' => 'Titel',
            'text' => 'Tekst',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get('/werkwijze');

        $response->assertOk()
            ->assertSee('/werkwijze/', false)
            ->assertSee('/tarieven/', false)
            ->assertSee('/ervaringen/', false)
            ->assertSee('/ervaringen/romy-van-drosthagen/', false)
            ->assertSee('/over-mij/', false)
            ->assertSee('/over-mij/publicaties/', false)
            ->assertSee('/contact/', false)
            ->assertDontSeeText('Verborgen pagina')
            ->assertSeeText('Romy');
    }
}