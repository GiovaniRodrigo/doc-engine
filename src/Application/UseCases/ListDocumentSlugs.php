<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class ListDocumentSlugs
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(): array
    {
        return $this->repository->allSlugs();
    }
}
