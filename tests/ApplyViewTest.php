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
}

