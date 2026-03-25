<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Entities\DocumentVersion;
use Illuminate\Support\Str;

class SyncMarkdownDocs
{
    public function __construct(
        private FilesystemMarkdownStorage $storage,
        private DocumentRepository $repository
    ) {}

    public function execute(): void
    {
        $files = $this->storage->all();

        foreach ($files as $file) {

            $content = $this->storage->get($file);

            $checksum = md5($content);

            $slug = basename($file, '.md');

            $document = new Document(
                id: (string) Str::uuid(),
                slug: $slug,
                title: ucfirst($slug)
            );

            $version = new DocumentVersion(
                documentId: $document->id,
                version: now()->timestamp,
                content: $content,
                checksum: $checksum
            );

            $this->repository->save($document);
            $this->repository->saveVersion($version);
        }
    }
}
