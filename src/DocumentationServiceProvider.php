<?php

namespace Giovani\DocumentationPlatformEngine;

use Illuminate\Support\ServiceProvider;
use Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationPlatformEngine\Documentation\Infrastructure\Persistence\EloquentDocumentRepository;
use Giovani\DocumentationPlatformEngine\Rendering\Application\Services\Renderer;
use Giovani\DocumentationPlatformEngine\Rendering\Infrastructure\CommonMarkRenderer;
use Giovani\DocumentationPlatformEngine\SourceControl\Domain\Services\RepositoryDiffService;
use Giovani\DocumentationPlatformEngine\SourceControl\Infrastructure\CommitDiffAnalyzer;

class DocumentationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            DocumentRepository::class,
            EloquentDocumentRepository::class
        );

        $this->app->bind(
            Renderer::class,
            CommonMarkRenderer::class
        );

        $this->app->bind(
            RepositoryDiffService::class,
            CommitDiffAnalyzer::class
        );
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'documentation');

        $this->loadRoutesFrom(
            __DIR__ . '/Documentation/Interface/Http/Controllers/routes.php'
        );

        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations')
        ], 'documentation-migrations');

        $this->publishes([
            __DIR__ . '/config/documentation.php' => config_path('documentation.php'),
        ], 'documentation-config');

        $this->mergeConfigFrom(
            __DIR__ . '/config/documentation.php',
            'documentation'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Giovani\DocumentationPlatformEngine\Documentation\Interface\Console\SyncDocumentationCommand::class
            ]);
        }
    }
}
