<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;

class ListDocumentSlugs
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(?string $project = null): array
    {
        $key = $project ? strtolower($project) : 'all';

        return cache()->remember("doc_slugs_{$key}", now()->addHours(24), function () use ($project) {
            return $this->repository->allActiveSlugs($project);
        });
    }
}
