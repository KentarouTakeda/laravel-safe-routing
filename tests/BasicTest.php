<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Mockery;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;
use KentarouTakeda\SafeRouting\SafeRouting;

class BasicTest extends TestCase
{
    const ARRAY = [
        'routes' => [
            'top' => [
                'uri' => '/',
                'controller' => 'SomeController@top'
            ],
        ],
    ];

    /** @var \Illuminate\Routing\Route */
    private $route;

    public function setUp(): void {
        parent::setUp();
        resolve(SafeRouting::class)->makeRoute(self::ARRAY);
        $this->route = Route::getRoutes()->get()[0];
    }

    /** @test */
    public function testRoutingName() {
        $this->assertSame($this->route->getName(), 'top');
    }

    /** @test */
    public function testUri() {
        $this->assertSame($this->route->uri(), '/');
    }

    /** @test */
    public function testMethods() {
        $this->assertSame($this->route->methods(), ['GET', 'HEAD']);
    }

    /** @test */
    public function testController() {
        $this->assertSame($this->route->action['controller'], '\App\Http\Controllers\SomeController@top');
    }
}
