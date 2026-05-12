<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Tests;

use LorneQuinn\Blog\Core\BlogCoreServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BlogCoreServiceProvider::class];
    }
}
