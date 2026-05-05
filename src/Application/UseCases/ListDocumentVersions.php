<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class ListDocumentVersions
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $slug): array
    {
        $document = $this->repository->findBySlug($slug);

        if (! $document) {
            return [];
        }

        return $this->repository->versions($document->id);
    }
}
