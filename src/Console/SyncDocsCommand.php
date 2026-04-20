<?php

namespace Giovani\DocumentationEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;

class SyncDocsCommand extends Command
{
    protected $signature = 'docs:sync {project?} {--path=}';

    protected $description = 'Synchronize markdown documentation with database';

    public function __construct(private DocumentRepository $repository)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $project = (string) $this->argument('project');
        $basePath = $this->resolveBasePath();

        $this->info("Docs base path: {$basePath}");
        if ($project) {
            $this->info("Syncing under project prefix: {$project}");
        }

        try {
            $storage = new FilesystemMarkdownStorage($basePath);
            
            // Resolvemos o Use Case do container
            $syncUseCase = app(SyncMarkdownDocs::class, ['storage' => $storage]);
            
            $synced = $syncUseCase->execute($project);

            $this->info('SYNCED: ' . $synced);

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('Documentation sync failed.', [
                'base_path' => $basePath,
                'project' => $project,
                'exception' => $exception,
            ]);

            $this->error('Documentation sync failed: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }

    private function resolveBasePath(): string
    {
        $configuredPath = (string) $this->option('path');

        if ($configuredPath === '') {
            $configuredPath = (string) config('documentation-engine.docs_path', 'docs');
        }

        if ($this->isAbsolutePath($configuredPath)) {
            return $configuredPath;
        }

        return base_path($configuredPath);
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:\\\\/', $path) === 1;
    }
}
