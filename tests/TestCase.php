<?php

namespace KentarouTakeda\SafeRouting\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use KentarouTakeda\SafeRouting\SafeRoutingServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app) {
        return [
            SafeRoutingServiceProvider::class,
        ];
    }

    protected function setUp(): void {
        parent::setUp();
        app('view')->getFinder()->setPaths([__DIR__ . '/views/']);
    }
}
