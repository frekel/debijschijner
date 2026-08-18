<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyPolicyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_renders_standard_privacy_statement(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertOk()
            ->assertSeeText('Privacyverklaring')
            ->assertSeeText('Welke gegevens verwerk ik?')
            ->assertSeeText('Promopagina’s en campagneverkeer')
            ->assertSeeText('Jouw rechten');
    }
}