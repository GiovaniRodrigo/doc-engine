<?php

namespace Giovani\DocumentationEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;

class SyncDocsCommand extends Command
{
    protected $signature = 'docs:sync {--path=}';

    protected $description = 'Synchronize markdown documentation with database';

    public function __construct(private DocumentRepository $repository)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $path = $this->resolveDocsPath();

        $this->info("Docs path: {$path}");

        try {
            $storage = new FilesystemMarkdownStorage($path);
            
            // Resolvemos o Use Case do container para garantir que GitVersionResolver e Repository sejam injetados
            // Mas sobrescrevemos o storage que pode ter um path customizado via CLI
            $syncUseCase = app(SyncMarkdownDocs::class, ['storage' => $storage]);
            
            $files = $storage->all();
            $this->info('FILES FOUND: ' . count($files));

            $synced = $syncUseCase->execute();

            $this->info('SYNCED: ' . $synced);

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('Documentation sync failed.', [
                'path' => $path,
                'exception' => $exception,
            ]);

            $this->error('Documentation sync failed: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }

    private function resolveDocsPath(): string
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
