<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Page::query()->create([
            'slug' => 'home',
            'title' => 'Home',
            'template' => 'homepage',
            'content_blocks' => [
                ['type' => 'rich_text', 'data' => ['heading' => 'Welkom', 'body' => '<p>Home</p>', 'editor_mode' => 'wysiwyg']],
            ],
            'is_published' => true,
            'show_in_menu' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
