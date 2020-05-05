<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Illuminate\Support\Facades\Route;
use KentarouTakeda\SafeRouting\SafeRouting;

class RouteMethodNameTest extends TestCase
{
    /**
     * @return array<string, $ret\Illuminate\Routing\Route>
     */
    private function makeRoute(array $route): array {
        resolve(SafeRouting::class)->makeRoute($route);
        $ret = [];
        foreach(Route::getRoutes()->get() as $route) {
            $ret[$route->getName()] = $route;
        }
        return $ret;
    }

    /** @test */
    public function testRoutingName() {
        $routes = $this->makeRoute([
            'routes' => [
                'top' => [
                    'uri' => '/',
                    'methods' => [
                        'GET' => [
                            'controller' => 'ResourceController@get'
                        ],
                        'POST' => [
                            'controller' => 'ResourceController@post'
                        ]
                    ]
                ],
            ],
        ]);
        $this->assertCount(2, $routes);
        $this->assertContains('top', array_keys($routes));
        $this->assertContains('top.__POST__', array_keys($routes));
    }

    /** @test */
    public function testRoutingOrder() {
        $routes = $this->makeRoute([
            'routes' => [
                'top' => [
                    'uri' => '/',
                    'methods' => [
                        'POST' => [
                            'controller' => 'ResourceController@post'
                        ],
                        'GET' => [
                            'controller' => 'ResourceController@get'
                        ],
                    ]
                ],
            ],
        ]);
        $this->assertCount(2, $routes);
        $this->assertContains('top', array_keys($routes));
        $this->assertContains('top.__POST__', array_keys($routes));
    }
}
