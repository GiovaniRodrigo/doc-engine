<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Interface\Console;

use Illuminate\Console\Command;
use Giovani\DocumentationPlatformEngine\Documentation\Application\Services\DocumentationService;

class SyncDocsCommand extends Command
{
    protected $signature = 'docs:sync {project} {--commit=HEAD}';

    protected $description = 'Sync documentation from repository';

    public function handle(DocumentationService $service)
    {
        $project = $this->argument('project');
        $commit = $this->option('commit');

        $repoPath = base_path("docs/{$project}");

        $service->syncAndRender(
            project: $project,
            repositoryPath: $repoPath,
            commit: $commit
        );

        $this->info('Documentation synced successfully.');
    }
}