<?php

namespace Giovani\DocumentationEngine\Console;

use Illuminate\Console\Command;
use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Infrastructure\Persistence\EloquentDocumentRepository;

class SyncDocsCommand extends Command
{
    protected $signature = 'docs:sync {--path=docs}';

    protected $description = 'Synchronize markdown documentation with database';

    public function handle(): int
    {
        $path = base_path($this->option('path'));

        if (!is_dir($path)) {
            $this->error("Docs path not found: {$path}");
            return self::FAILURE;
        }

        $this->info('Starting documentation sync...');

        $storage = new FilesystemMarkdownStorage($path);

        $repo = new EloquentDocumentRepository();

        $sync = new SyncMarkdownDocs($storage, $repo);

        $sync->execute();

        $this->info('Documentation synchronized successfully.');

        return self::SUCCESS;
    }
}
