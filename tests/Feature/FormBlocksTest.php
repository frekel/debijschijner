<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormBlocksTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_block_renders_decorated_contact_form(): void
    {
        Page::query()->create([
            'slug' => 'contact-blok',
            'title' => 'Contact blok',
            'template' => 'form',
            'form_title' => 'Neem contact op',
            'content_blocks' => [
                ['type' => 'contact_form', 'data' => []],
            ],
            'is_published' => true,
            'show_in_menu' => false,
        ]);

        $response = $this->get('/contact-blok');

        $response->assertOk()
            ->assertSeeText('Neem contact op')
            ->assertSeeText('Email')
            ->assertSeeText('Telefoon')
            ->assertSeeText('Adres')
            ->assertSeeText('Overige')
            ->assertSee('action="'.route('contact.submit').'"', false)
            ->assertSee('name="wpforms[fields][0][first]"', false)
            ->assertDontSee('wpforms-field-container', false)
            ->assertSee('_token', false);
    }

    public function test_apply_form_block_renders_decorated_apply_form(): void
    {
        Page::query()->create([
            'slug' => 'aanvraag-blok',
            'title' => 'Aanvraag blok',
            'template' => 'form',
            'form_title' => 'Vraag een traject aan',
            'content_blocks' => [
                ['type' => 'apply_form', 'data' => []],
            ],
            'is_published' => true,
            'show_in_menu' => false,
        ]);

        $response = $this->get('/aanvraag-blok');

        $response->assertOk()
            ->assertSeeText('Vraag een traject aan')
            ->assertSeeText('Email')
            ->assertSeeText('Telefoon')
            ->assertSeeText('Adres')
            ->assertSeeText('Overige')
            ->assertSee('action="'.route('apply.submit').'"', false)
            ->assertSee('name="wpforms[fields][10]"', false)
            ->assertDontSee('wpforms-field-container', false)
            ->assertSee('_token', false);
    }
}