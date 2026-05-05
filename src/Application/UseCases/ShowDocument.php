<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Application\DTO\DocumentViewData;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class ShowDocument
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $slug): ?DocumentViewData
    {
        $doc = $this->repository->findBySlug(strtolower(trim($slug)));

        if (!$doc) {
            return null;
        }

        $version = $this->repository->latestPublishedVersion($doc->id);

        if (!$version) {
            return null;
        }

        return DocumentViewData::fromEntities($doc, $version);
    }
}
