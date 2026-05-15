<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use LorneQuinn\Blog\Core\Component\Component;
use LorneQuinn\Blog\Core\Component\ComponentRegistry;
use LorneQuinn\Blog\Core\DataType\DataTypeRegistry;
use LorneQuinn\Blog\Core\Rendering\BodyPipeline;
use LorneQuinn\Blog\Core\Shortcode\ShortcodeParser;

final class BlogCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blog-core.php', 'blog-core');

        $this->app->singleton(DataTypeRegistry::class);
        $this->app->singleton(ComponentRegistry::class);
        $this->app->singleton(BodyPipeline::class);

        $this->app->singleton(ShortcodeParser::class, function (Application $app): ShortcodeParser {
            $registry = $app->make(ComponentRegistry::class);

            return new ShortcodeParser(static function (string $name, array $attrs) use ($registry): string {
                $component = $registry->resolve($name);
                if (! $component instanceof Component) {
                    return "<!-- shortcode '{$name}' not registered -->";
                }

                return View::make($component->view(), $attrs)->render();
            });
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blog-core');

        // Wire ShortcodeParser into the BodyPipeline as the early pass (priority 0).
        // Markdown (or any other renderer) registers later with priority > 0.
        $pipeline = $this->app->make(BodyPipeline::class);
        $shortcode = $this->app->make(ShortcodeParser::class);
        $pipeline->register(fn (string $body): string => $shortcode->parse($body), 0);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/blog-core.php' => config_path('blog-core.php'),
            ], 'blog-core-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'blog-core-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/blog-core'),
            ], 'blog-core-views');
        }
    }
}
