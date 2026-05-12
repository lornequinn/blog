<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core;

use Illuminate\Support\ServiceProvider;
use LorneQuinn\Blog\Core\Component\ComponentRegistry;
use LorneQuinn\Blog\Core\DataType\DataTypeRegistry;

final class BlogCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blog-core.php', 'blog-core');

        $this->app->singleton(DataTypeRegistry::class);
        $this->app->singleton(ComponentRegistry::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/blog-core.php' => config_path('blog-core.php'),
            ], 'blog-core-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'blog-core-migrations');
        }
    }
}
