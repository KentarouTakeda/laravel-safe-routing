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
                    'methods' => [
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
        $this->assertInstanceOf(\KentarouTakeda\SafeRouting\Exception\GetValidationException::class, $response->exception);
    }

    /** @test */
    public function testGetTypeCast() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@get',
                    'methods' => [
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

    /** @test */
    public function testPostValidationFailure() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@get',
                    'methods' => [
                        'POST' => [
                            'type' => 'object',
                            'properties' => [
                                'i' => ['type' => 'integer']
                            ]
                        ]
                    ],
                ],
            ],
        ]);

        $response = $this->post('/viewname', ['i'=>'x']);
        $this->assertInstanceOf(\KentarouTakeda\SafeRouting\Exception\PostValidationException::class, $response->exception);
    }

    /** @test */
    public function testPostTypeCast() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@post',
                    'methods' => [
                        'POST' => [
                            'type' => 'object',
                            'properties' => [
                                'i' => ['type' => 'integer']
                            ]
                        ]
                    ],
                ],
            ],
        ]);

        $response = $this->post('/viewname', ['i'=>'1']);
        $data = $response->baseResponse->getOriginalContent()->getData();
        $this->assertSame(['i'=>1], $data);
    }

    /** @test */
    public function testResponseValidationFailure() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@get',
                    'RET' => [
                        'type' => 'null',
                    ]
                ],
            ],
        ]);

        $response = $this->get('/viewname');
        $this->assertInstanceOf(\KentarouTakeda\SafeRouting\Exception\ResponseValidationException::class, $response->exception);
    }

    /** @test */
    public function testResponseValidationOk() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'controller' => 'SomeController@withReturn',
                    'RET' => [
                        'type' => 'object',
                        'properties' => [
                            'foo' => [
                                'type' => "string",
                                "enum" => ['bar'],
                            ]
                        ]
                    ]
                ],
            ],
        ]);

        $response = $this->get('/viewname');
        $response->assertStatus(200);
    }

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
