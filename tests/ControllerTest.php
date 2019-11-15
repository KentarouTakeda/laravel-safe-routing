<?php

namespace KentarouTakeda\SafeRouting\Tests;

use KentarouTakeda\SafeRouting\SafeRouting;

class ControllerTest extends TestCase
{
    /** @test */
    public function routeDefaultOverrideGet() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@returnA',
                    'methods' => [
                        'GET' => [
                            'controller' => 'SomeController@returnB',
                        ],
                        'POST' => [
                            'controller' => 'SomeController@returnC',
                        ],
                        'RET' => [
                            'type' => 'object',
                        ]
                    ],
                ],
            ],
        ]);

        $response = $this->get('/viewname');
        $response->assertViewHas('data', 'B');
    }

    /** @test */
    public function routeDefaultOverridePost() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@returnA',
                    'methods' => [
                        'GET' => [
                            'controller' => 'SomeController@returnB',
                        ],
                        'POST' => [
                            'controller' => 'SomeController@returnC',
                        ],
                        'RET' => [
                            'type' => 'object',
                        ]
                    ],
                ],
            ],
        ]);

        $response = $this->post('/viewname');
        $response->assertViewHas('data', 'C');
    }
}
