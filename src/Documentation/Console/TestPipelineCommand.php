<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Console;

use Illuminate\Console\Command;
use Giovani\DocumentationPlatformEngine\Documentation\Application\Services\DocumentationService;

class TestPipelineCommand extends Command
{
    protected $signature = 'doc-engine:pipeline';

    protected $description = 'Test Documentation Pipeline';

    public function handle(DocumentationService $service)
    {
        $service->syncAndRender(
            project: 'test',
            repositoryPath: base_path('docs'),
            commit: 'HEAD'
        );

        $this->info('Pipeline executada');
    }
}
