<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class ShowDocument
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $slug)
    {
        $doc = $this->repository->findBySlug(strtolower(trim($slug)));

        if (!$doc) {
            return null;
        }

        return $this->repository->latestVersion($doc->id);
    }
}
