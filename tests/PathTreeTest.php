<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Illuminate\Support\Facades\Route;
use KentarouTakeda\SafeRouting\SafeRouting;

class PathTreeTest extends TestCase
{
    /** @var \Illuminate\Routing\Route */
    private $route;

    /** @test */
    public function testTopLevel() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'foo' => null
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];

        $this->assertSame('foo', $this->route->uri());
    }

    /** @test */
    public function testSecondLevel() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'foo.bar' => null
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];

        $this->assertSame('foo/bar', $this->route->uri());
    }

    /** @test */
    public function testSecondLevelIndex() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'dir.index' => null
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];

        $this->assertSame('dir', $this->route->uri());
    }

    /** @test */
    public function testTopLevelIndex() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'index' => null
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];

        $this->assertSame('/', $this->route->uri());
    }

    /** @test */
    public function testDirectoryNameIsIndex() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'index.index' => null
            ]
        ]);
        $this->route = Route::getRoutes()->get()[0];

        $this->assertSame('index', $this->route->uri());
    }
}
