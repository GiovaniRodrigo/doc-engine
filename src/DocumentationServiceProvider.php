<?php

namespace Giovani\DocumentationEngine;

use Closure;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Giovani\DocumentationEngine\Console\SyncDocsCommand;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;
use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Infrastructure\Persistence\EloquentDocumentRepository;

class DocumentationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/documentation-engine.php',
            'documentation-engine'
        );

        $this->bindAiProvider();
        $this->bindRepositories();
        $this->bindInfrastructure();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'documentation-engine');
        View::share('documentationEnginePackagePath', dirname(__DIR__));

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/documentation-engine')
        ], 'documentation-views');

        $this->publishes([
            __DIR__ . '/../resources/css/docs' => public_path('vendor/documentation-engine/docs')
        ], 'documentation-assets');

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

    protected function bindRepositories(): void
    {
        $this->app->bind(DocumentRepository::class, EloquentDocumentRepository::class);
    }

    protected function bindInfrastructure(): void
    {
        $this->app->singleton(FilesystemMarkdownStorage::class, function () {
            return new FilesystemMarkdownStorage(
                base_path(config('documentation-engine.docs_path', 'docs'))
            );
        });

        $this->app->singleton(GitVersionResolver::class);
    }
}
