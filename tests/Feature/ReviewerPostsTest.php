<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviews_block_renders_published_reviewer_posts_in_sort_order(): void
    {
        Page::query()->create([
            'slug' => 'ervaringen',
            'title' => 'Ervaringen',
            'html' => '',
            'content_blocks' => [['type' => 'reviews', 'data' => []]],
            'is_published' => true,
        ]);

        Post::query()->create($this->review(['name' => 'Tweede', 'sort_order' => 20]));
        Post::query()->create($this->review(['name' => 'Eerste', 'sort_order' => 10]));
        Post::query()->create($this->review(['name' => 'Verborgen', 'is_published' => false]));
        Post::query()->create($this->review(['name' => 'Geen review', 'type' => 'post']));

        $response = $this->get('/ervaringen');

        $response->assertOk()
            ->assertSeeTextInOrder(['EERSTE', 'TWEEDE'])
            ->assertDontSeeText('VERBORGEN')
            ->assertDontSeeText('GEEN REVIEW');
    }

    public function test_reviewer_detail_page_renders_from_reviewer_post_without_page_record(): void
    {
        Post::query()->create($this->review([
            'name' => 'Romy van Drosthagen',
            'slug' => 'romy-van-drosthagen',
            'title' => 'Titel reviewer',
            'text' => '<p>Volledige reviewtekst</p>',
            'image' => 'images/upload/2026/08/romy.jpg',
        ]));

        $response = $this->get('/ervaringen/romy-van-drosthagen');

        $response->assertOk()
            ->assertSeeText('Romy van Drosthagen')
            ->assertSeeText('Titel reviewer')
            ->assertSee('Volledige reviewtekst', false)
            ->assertSee('/images/upload/2026/08/romy.jpg', false);
    }

    public function test_reviewer_detail_page_takes_precedence_over_page_with_same_slug(): void
    {
        Page::query()->create([
            'slug' => 'ervaringen/romy-van-drosthagen',
            'title' => 'Legacy reviewer page',
            'html' => 'Dit is oude pagina-inhoud',
            'is_published' => true,
        ]);

        Post::query()->create($this->review([
            'name' => 'Romy van Drosthagen',
            'slug' => 'romy-van-drosthagen',
            'title' => 'Nieuwe reviewer pagina',
            'text' => '<p>Review uit posts tabel</p>',
        ]));

        $response = $this->get('/ervaringen/romy-van-drosthagen');

        $response->assertOk()
            ->assertSeeText('Nieuwe reviewer pagina')
            ->assertSee('Review uit posts tabel', false)
            ->assertDontSeeText('Dit is oude pagina-inhoud');
    }

    private function review(array $overrides = []): array
    {
        return array_merge([
            'type' => 'reviewer',
            'slug' => null,
            'name' => 'Reviewer',
            'title' => 'Titel',
            'text' => 'Tekst',
            'sort_order' => 0,
            'is_published' => true,
        ], $overrides);
    }
}
