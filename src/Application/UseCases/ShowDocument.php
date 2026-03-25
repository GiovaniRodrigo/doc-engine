<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class ShowDocument
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $slug)
    {
        $doc = $this->repository->findBySlug($slug);

        return $this->repository->latestVersion($doc->id);
    }
}
