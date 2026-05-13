<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Markdown\Tests;

use Illuminate\Foundation\Application;
use LorneQuinn\Blog\Core\BlogCoreServiceProvider;
use LorneQuinn\Blog\Markdown\BlogMarkdownServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BlogCoreServiceProvider::class,
            BlogMarkdownServiceProvider::class,
        ];
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
