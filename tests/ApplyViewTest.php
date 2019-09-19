<?php
    
namespace KentarouTakeda\SafeRouting\Tests;

use KentarouTakeda\SafeRouting\SafeRouting;

class ApplyViewTest extends TestCase
{
    /** @test */
    public function testViewApplied() {
        resolve(SafeRouting::class)->makeRoute([
            'routes' => [
                'viewname' => [
                    'uri' => '/view',
                ],
            ],
        ]);

        $response = $this->get('/view');
        $response->assertViewIs('viewname');
    }

    /** @test */
    public function testViewAppliedWithConteoller() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'uri' => '/view',
                    'controller' => 'SomeController@method'
                ],
            ],
        ]);

        $response = $this->get('/view');
        $response->assertViewIs('viewname');
        $this->assertSame([], $response->baseResponse->getOriginalContent()->getData());
    }

    /** @test */
    public function testViewAppliedWithReturnValue() {
        resolve(SafeRouting::class)->makeRoute([
            'namespace' => __NAMESPACE__ . '\Controllers',
            'routes' => [
                'viewname' => [
                    'uri' => '/view',
                    'controller' => 'SomeController@withReturn'
                ],
            ],
        ]);

        $response = $this->get('/view');
        $this->assertSame(['foo' => 'var'], $response->baseResponse->getOriginalContent()->getData());
    }
}
