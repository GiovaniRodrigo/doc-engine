<?php

namespace Giovani\DocumentationEngine;

use Illuminate\Support\ServiceProvider;
use Giovani\DocumentationEngine\Console\SyncDocsCommand;

class DocumentationServiceProvider extends ServiceProvider
{
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
    }
}
