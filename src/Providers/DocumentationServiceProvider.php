<?php

namespace Giovani\DocumentationPlatformEngine\Providers;

use Illuminate\Support\ServiceProvider;

class DocumentationServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Giovani\DocumentationPlatformEngine\Console\TestDocumentationCommand::class,
                \Giovani\DocumentationPlatformEngine\Documentation\Console\TestPipelineCommand::class
            ]);
        }
    }
}