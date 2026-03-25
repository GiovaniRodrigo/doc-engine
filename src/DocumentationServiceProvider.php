<?php

namespace Giovani\DocumentationEngine;

use Closure;
use Illuminate\Support\ServiceProvider;
use Giovani\DocumentationEngine\Console\SyncDocsCommand;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;

class DocumentationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/documentation-engine.php',
            'documentation-engine'
        );

        $this->bindAiProvider();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'documentation-engine');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/documentation-engine')
        ], 'documentation-views');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncDocsCommand::class
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/documentation-engine.php' =>
            config_path('documentation-engine.php'),
        ], 'documentation-config');
    }

    protected function bindAiProvider(): void
    {
        if ($this->app->bound(AiProvider::class)) {
            return;
        }

        $provider = config('documentation-engine.ai.provider');

        if (is_string($provider) && $provider !== '') {
            $this->app->bind(AiProvider::class, $provider);
            return;
        }

        if ($provider instanceof Closure) {
            $this->app->bind(AiProvider::class, $provider);
        }
    }
}
