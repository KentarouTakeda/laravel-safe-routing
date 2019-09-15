<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Illuminate\Support\Facades\Route;
use KentarouTakeda\SafeRouting\SafeRouting;

class ViewRouteTest extends TestCase
{
    const ARRAY = [
        'routes' => [
            'view' => [
                'uri' => '/view',
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
        $this->assertSame('view', $this->route->getName());
    }

    /** @test */
    public function testControllerShuoldBeViewController() {
        $this->assertNull($this->route->controller);
        $this->assertSame(\Illuminate\Routing\ViewController::class, get_class($this->route->getController()));
    }

    /** @test */
    public function testUri() {
        $this->assertSame('view', $this->route->uri());
    }
}

