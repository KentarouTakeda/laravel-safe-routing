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
}
