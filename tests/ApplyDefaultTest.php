<?php

namespace KentarouTakeda\SafeRouting\Tests;

use KentarouTakeda\SafeRouting\SafeRouting;

class ApplyDefaultTest extends TestCase
{
    /** @test */
    public function testResponseValidationApplyDefault() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@withReturn',
                    'RET' => [
                        'type' => 'object',
                        'properties' => [
                            'universe' => [
                                'type' => "integer",
                                'default' => 42
                            ]
                        ]
                    ]
                ],
            ],
        ]);

        $response = $this->get('/viewname');
        $response->assertViewHas('universe', 42);
    }
}
