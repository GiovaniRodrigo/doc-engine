<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Illuminate\Support\Str;

class SyncMarkdownDocs
{
    public function __construct(
        private FilesystemMarkdownStorage $storage,
        private DocumentRepository $repository
    ) {}

    public function execute(): int
    {
        $files = $this->storage->all();

        $count = 0;

        foreach ($files as $file) {
            $content = $file['content'];
            $checksum = md5($content);
            $slug = strtolower(trim($file['slug']));

            $document = $this->repository->findBySlug($slug);

            if (!$document) {
                $document = new Document(
                    id: (string) Str::uuid(),
                    slug: $slug,
                    title: ucfirst(str_replace('.', ' ', $slug))
                );

                $this->repository->save($document);
            }

            $latestVersion = $this->repository->latestVersion($document->id);

            if ($latestVersion && $latestVersion->checksum === $checksum) {
                continue;
            }

            $this->repository->createVersion(
                documentId: $document->id,
                content: $content,
                checksum: $checksum
            );

            $count++;
        }

        return $count;
    }
}
