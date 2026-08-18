<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicatiesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_publicaties_url_redirects_to_nested_over_mij_publicaties_url(): void
    {
        $this->get('/publicaties')
            ->assertRedirect('/over-mij/publicaties');
    }

    public function test_publications_index_renders_publicatie_posts(): void
    {
        Page::query()->create([
            'slug' => 'over-mij/publicaties',
            'title' => 'Publicaties',
            'content_blocks' => [['type' => 'publications', 'data' => []]],
            'is_published' => true,
        ]);

        Post::query()->create([
            'type' => 'publicatie',
            'slug' => 'mijn-publicatie',
            'title' => 'Mijn publicatie',
            'text' => '<p>Korte samenvatting</p>',
            'button_text' => '<p>Korte samenvatting</p>',
            'editor_mode' => 'html',
            'image' => '/images/uploads/publicatie.jpg',
            'sort_order' => 999,
            'is_published' => true,
        ]);

        $this->get('/over-mij/publicaties')
            ->assertOk()
            ->assertSeeText('Mijn publicatie')
            ->assertSee('Korte samenvatting', false);
    }

    public function test_old_publication_detail_url_redirects_to_nested_publication_detail_url(): void
    {
        Post::query()->create([
            'type' => 'publicatie',
            'slug' => 'mijn-publicatie',
            'title' => 'Mijn publicatie',
            'text' => '<p>Korte samenvatting</p>',
            'editor_mode' => 'html',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->get('/publicaties/mijn-publicatie')
            ->assertRedirect('/over-mij/publicaties/mijn-publicatie');
    }

    public function test_nested_publication_detail_renders_publicatie_post(): void
    {
        Post::query()->create([
            'type' => 'publicatie',
            'slug' => 'mijn-publicatie',
            'title' => 'Mijn publicatie',
            'text' => '<p>Korte samenvatting</p>',
            'editor_mode' => 'html',
            'image' => '/images/uploads/publicatie.jpg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->get('/over-mij/publicaties/mijn-publicatie')
            ->assertOk()
            ->assertSeeText('Mijn publicatie')
            ->assertSee('Korte samenvatting', false)
            ->assertSee('/images/uploads/publicatie.jpg', false);
    }
}