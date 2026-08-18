<?php

namespace Tests\Feature;

use App\Models\ApplySubmission;
use App\Models\ContactSubmission;
use App\Models\PromoHit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAnalyticsWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_google_analytics_setup_message_when_not_configured(): void
    {
        config()->set('services.google_analytics.property_id', null);
        config()->set('services.google_analytics.service_account_json', null);

        PromoHit::query()->create([
            'page_slug' => 'mok',
            'page_title' => 'Mok',
            'path' => '/mok',
            'full_url' => 'https://example.com/mok',
            'redirect_target' => '/',
            'method' => 'GET',
            'host' => 'example.com',
        ]);

        ContactSubmission::query()->create([
            'first_name' => 'Debora',
            'last_name' => 'Test',
            'email' => 'debora@example.com',
            'phone' => '0612345678',
            'message' => 'Hallo',
        ]);

        ApplySubmission::query()->create([
            'first_name' => 'Debora',
            'last_name' => 'Aanvraag',
            'email' => 'aanvraag@example.com',
            'phone' => '0698765432',
            'trajectory' => 'Coaching',
            'message' => 'Ik wil meer informatie.',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('CMS DeBijschijner');
        $response->assertSee('Bekijk website');
        $response->assertSee('Google Analytics');
        $response->assertSee('GA4 nog niet gekoppeld');
        $response->assertSee('Configuratie vereist');
        $response->assertSee('Site activiteit');
        $response->assertSee('Aantal promohits');
        $response->assertSee('Aantal ingevulde contactformulieren');
        $response->assertSee('Aantal ingevulde aanvraagformulieren');
        $response->assertSee('1');
    }
}