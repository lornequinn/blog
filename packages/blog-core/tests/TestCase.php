<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Tests;

use Illuminate\Foundation\Application;
use LorneQuinn\Blog\Core\BlogCoreServiceProvider;
use LorneQuinn\Blog\Core\Tests\Fixtures\TestViewsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BlogCoreServiceProvider::class, TestViewsServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        /** @var Application $app */
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
