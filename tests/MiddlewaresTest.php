<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Illuminate\Support\Facades\Route;
use KentarouTakeda\SafeRouting\SafeRouting;

class MiddlewareTest extends TestCase
{
    /** @var \Illuminate\Routing\Route */
    private $route;

    /** @test */
    public function testTopLevel() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'foo' => [
                    'middlewares' => ['some-middleware']
                ]
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];

        $middlewares = $this->route->middleware();

        $this->assertEquals(['safe.routing', 'some-middleware'], $middlewares);
    }
}
