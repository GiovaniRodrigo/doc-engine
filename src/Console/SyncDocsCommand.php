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
        $path = base_path(config('documentation-engine.docs_path'));

        $this->info("Docs path: " . $path);

        $storage = new FilesystemMarkdownStorage($path);
        $repo = new EloquentDocumentRepository();

        $files = $storage->all();

        $this->info("FILES FOUND: " . count($files));

        $count = 0;

        foreach ($files as $file) {

            try {

                $this->line("SYNCING: " . $file['slug']);

                $content = $file['content'];
                $checksum = md5($content);
                $slug = $file['slug'];

                $document = $repo->findBySlug($slug);

                if (!$document) {

                    $document = new \Giovani\DocumentationEngine\Domain\Entities\Document(
                        id: (string) \Illuminate\Support\Str::uuid(),
                        slug: $slug,
                        title: ucfirst(str_replace('.', ' ', $slug))
                    );

                    $repo->save($document);
                }

                $repo->createVersion(
                    documentId: $document->id,
                    content: $content,
                    checksum: $checksum
                );

                $count++;
            } catch (\Throwable $e) {

                $this->error("ERROR IN " . $file['slug']);
                $this->error($e->getMessage());

                break;
            }
        }

        $this->info("SYNCED: " . $count);

        return self::SUCCESS;
    }
}
