<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactFormTest extends TestCase
{
    public function test_contact_form_submits_successfully(): void
    {
        $token = 'test-csrf-token';

        $response = $this->withSession(['_token' => $token])->post('/contact', [
            '_token' => $token,
            'wpforms' => [
                'fields' => [
                    0 => [
                        'first' => 'Debora',
                        'last' => 'Tester',
                    ],
                    1 => 'debora@example.com',
                    2 => '',
                    3 => 'Dit is een testbericht via Laravel.',
                    9 => '06-12345678',
                ],
            ],
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('contact_success');
    }

    public function test_contact_form_requires_required_fields(): void
    {
        $token = 'test-csrf-token';

        $response = $this->withSession(['_token' => $token])->from('/contact')->post('/contact', [
            '_token' => $token,
            'wpforms' => [
                'fields' => [
                    0 => [
                        'first' => '',
                        'last' => '',
                    ],
                    1 => 'geen-geldig-emailadres',
                    3 => '',
                    9 => '',
                ],
            ],
        ]);

        $response
            ->assertRedirect('/contact')
            ->assertSessionHasErrors([
                'wpforms.fields.0.first',
                'wpforms.fields.0.last',
                'wpforms.fields.1',
                'wpforms.fields.3',
                'wpforms.fields.9',
            ]);
    }
}
