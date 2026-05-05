<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class UpdateDocument
{
    public function __construct(
        private DocumentRepository $repository,
    ) {}

    public function execute(string $slug, string $content): string
    {
        $document = $this->repository->findBySlug($slug);

        if (! $document) {
            throw new \RuntimeException("Document not found: {$slug}");
        }

        $version = $this->repository->createVersion(
            documentId: $document->id,
            content: $content,
            checksum: md5($content),
            state: 'draft',
        );

        return $version->version;
    }
}
