<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Illuminate\Support\Facades\Route;
use KentarouTakeda\SafeRouting\SafeRouting;

class PrefixTest extends TestCase
{
    /** @var \Illuminate\Routing\Route */
    private $route;

    /** @test */
    public function testWithoutController() {
        resolve(SafeRouting::class)->makeRoute([
            'prefix' => 'foo',
            'routes' => [
                'bar' => null
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];
        $this->assertSame('foo/bar', $this->route->uri());
    }

    /** @test */
    public function testWithController() {
        resolve(SafeRouting::class)->makeRoute([
            'prefix' => 'foo',
            'routes' => [
                'bar' => [
                    'controller' => 'SomeController@method',
                ]
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];
        $this->assertSame('foo/bar', $this->route->uri());
    }
}
