<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PromoHit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoHitTest extends TestCase
{
    use RefreshDatabase;

    public function test_promo_page_request_is_logged_and_redirected(): void
    {
        Page::query()->create([
            'slug' => 'mok',
            'title' => 'Promo Mok',
            'template' => 'promo',
            'promo_redirect_url' => '/',
            'content_blocks' => [],
            'is_published' => true,
            'show_in_menu' => false,
        ]);

        $response = $this->withHeaders([
            'Referer' => 'https://example.com/qr',
            'User-Agent' => 'Mozilla/5.0 Test Browser',
            'Accept-Language' => 'nl-NL,nl;q=0.9',
            'X-Test-Header' => 'abc123',
        ])->get('/mok?utm_source=qr&campaign=zomer');

        $response->assertRedirect('/');

        $hit = PromoHit::query()->first();

        $this->assertNotNull($hit);
        $this->assertSame('mok', $hit->path);
        $this->assertSame('mok', $hit->page_slug);
        $this->assertSame('Promo Mok', $hit->page_title);
        $this->assertSame('/', $hit->redirect_target);
        $this->assertSame('GET', $hit->method);
        $this->assertSame('https://example.com/qr', $hit->referer);
        $this->assertSame('Mozilla/5.0 Test Browser', $hit->user_agent);
        $this->assertSame('nl-NL,nl;q=0.9', $hit->accept_language);
        $this->assertSame('qr', $hit->query_params['utm_source'] ?? null);
        $this->assertSame('zomer', $hit->query_params['campaign'] ?? null);
        $this->assertSame('abc123', $hit->headers['x-test-header'] ?? null);
    }

    public function test_nested_promo_page_request_is_logged_and_redirected(): void
    {
        Page::query()->create([
            'slug' => 'tv/sbs6',
            'title' => 'TV SBS6',
            'template' => 'promo',
            'promo_redirect_url' => '/',
            'content_blocks' => [],
            'is_published' => true,
            'show_in_menu' => false,
        ]);

        $this->get('/tv/sbs6')->assertRedirect('/');

        $this->assertDatabaseHas('promo_hits', [
            'path' => 'tv/sbs6',
            'page_slug' => 'tv/sbs6',
            'page_title' => 'TV SBS6',
        ]);
    }
}