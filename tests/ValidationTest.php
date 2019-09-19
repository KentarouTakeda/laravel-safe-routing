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

    /** @test */
    public function testGetTypeCast() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@get',
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

        $response = $this->get('/viewname?i=1');
        $data = $response->baseResponse->getOriginalContent()->getData();
        $this->assertSame(['i'=>1], $data);
    }
}
