<?php

namespace Giovani\DocumentationEngine\Console;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SyncDocsCommand extends Command
{
    protected $signature = 'docs:sync {project?} {--path=} {--dry-run}';

    protected $description = 'Synchronize markdown documentation with database';

    public function handle(): int
    {
        $project = (string) $this->argument('project');
        $basePath = $this->resolveBasePath();
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Docs base path: {$basePath}");
        if ($project) {
            $this->info("Syncing under project prefix: {$project}");
        }
        if ($dryRun) {
            $this->warn('Dry-run enabled: no documents, versions, summaries, or caches will be changed.');
        }

        try {
            if (! is_dir($basePath)) {
                throw new RuntimeException("Documentation path does not exist: {$basePath}");
            }

            $storage = new FilesystemMarkdownStorage($basePath);

            // Resolvemos o Use Case do container
            $syncUseCase = app(SyncMarkdownDocs::class, ['storage' => $storage]);

            $result = $syncUseCase->execute($project, $dryRun);

            $this->line('COMMIT: '.($result->commit ?: 'unavailable'));
            $this->info('READ FILES: '.count($result->readFiles));
            $this->info('CHANGED FILES: '.count($result->changedFiles));
            $this->info('IGNORED FILES: '.count($result->ignoredFiles));
            $this->info('CREATED VERSIONS: '.$result->createdVersionsCount());
            $this->info('ARCHIVED DOCUMENTS: '.$result->archivedDocumentsCount());

            foreach ($result->errors as $error) {
                $this->warn('WARNING: '.$error);
            }

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('Documentation sync failed.', [
                'base_path' => $basePath,
                'project' => $project,
                'dry_run' => $dryRun,
                'exception' => $exception,
            ]);

            $this->error('Documentation sync failed: '.$exception->getMessage());

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
