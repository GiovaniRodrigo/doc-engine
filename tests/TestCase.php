<?php

namespace Giovani\DocumentationEngine\Tests;

use Giovani\DocumentationEngine\DocumentationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DocumentationServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('view.paths', [
            __DIR__ . '/Fixtures/resources/views',
        ]);
    }
}
