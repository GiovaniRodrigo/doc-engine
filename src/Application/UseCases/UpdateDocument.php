<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use RuntimeException;

class UpdateDocument
{
    public function __construct(
        private DocumentRepository $repository,
    ) {}

    public function execute(string $slug, string $content): string
    {
        $document = $this->repository->findBySlug($slug);

        if (! $document) {
            throw new RuntimeException("Document not found: {$slug}");
        }

        $checksum = md5($content);

        $version = $this->repository->createVersion(
            documentId: $document->id,
            content: $content,
            checksum: $checksum,
            state: 'draft'
        );

        return $version->version;
    }
}
