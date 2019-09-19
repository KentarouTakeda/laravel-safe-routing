<?php

namespace KentarouTakeda\SafeRouting\Tests;

use KentarouTakeda\SafeRouting\SafeRouting;

class ValidationTest extends TestCase
{
    /** @test */
    public function testGetValidationFailure() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'viewname' => [
                    'validation' => [
                        'GET' => [
                            'type' => 'object',
                            'properties' => [
                                'i' => ['type' => 'integer']
                            ]
                        ]
                    ],
                ],
            ],
        ]);

        $response = $this->get('/viewname?i=a');
        $response->assertStatus(400);
    }
}
