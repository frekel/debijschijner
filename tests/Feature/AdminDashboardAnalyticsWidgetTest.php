<?php

namespace Tests\Feature;

use App\Models\ApplySubmission;
use App\Models\ContactSubmission;
use App\Models\PromoHit;
use App\Services\GoogleAnalytics\GoogleAnalyticsService;
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
        $response->assertSee('Stel zowel GOOGLE_ANALYTICS_PROPERTY_ID als GOOGLE_ANALYTICS_SERVICE_ACCOUNT_JSON in.');
        $response->assertSee('Site activiteit');
        $response->assertSee('Aantal promohits');
        $response->assertSee('Aantal ingevulde contactformulieren');
        $response->assertSee('Aantal ingevulde aanvraagformulieren');
        $response->assertSee('1');
    }

    public function test_google_analytics_service_reports_missing_property_id_separately(): void
    {
        config()->set('services.google_analytics.property_id', null);
        config()->set('services.google_analytics.service_account_json', 'storage/app/google-analytics-service-account.json');

        $status = app(GoogleAnalyticsService::class)->configurationStatus();

        $this->assertFalse($status['configured']);
        $this->assertSame('GOOGLE_ANALYTICS_PROPERTY_ID is niet ingesteld.', $status['message']);
    }

    public function test_google_analytics_service_reports_unreadable_service_account_file(): void
    {
        $directory = storage_path('framework/testing/google-analytics');
        $path = $directory.'/service-account.json';

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, '{"client_email":"demo@example.com","private_key":"demo"}');
        chmod($path, 0000);

        config()->set('services.google_analytics.property_id', '123456789');
        config()->set('services.google_analytics.service_account_json', $path);

        try {
            $status = app(GoogleAnalyticsService::class)->configurationStatus();

            $this->assertFalse($status['configured']);
            $this->assertSame(sprintf('Het Google Analytics service account bestand is niet leesbaar op: %s', $path), $status['message']);
        } finally {
            chmod($path, 0644);
            unlink($path);
            rmdir($directory);
        }
    }
}