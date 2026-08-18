<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_without_content_blocks_renders_empty_page_shell(): void
    {
        Page::query()->create([
            'slug' => 'lege-pagina',
            'title' => 'Lege pagina',
            'content_blocks' => [],
            'is_published' => true,
            'show_in_menu' => true,
        ]);

        $response = $this->get('/lege-pagina');

        $response->assertOk()
            ->assertSeeText('Lege pagina');
    }
}