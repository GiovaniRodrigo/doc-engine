<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use RuntimeException;

class PublishDocumentVersion
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $slug, string $version): void
    {
        $document = $this->repository->findBySlug($slug);

        if (! $document) {
            throw new RuntimeException("Document not found: {$slug}");
        }

        if (! $this->repository->findVersion($document->id, $version)) {
            throw new RuntimeException("Version not found: {$version}");
        }

        $this->repository->publishVersion($document->id, $version);

        cache()->forget("doc_render_{$slug}");

        // Invalida navegação caso o título mude (futuro) ou por precaução
        $project = explode('.', $slug)[0];
        $projectKey = $project ? strtolower($project) : 'all';
        cache()->forget("doc_slugs_{$projectKey}");
        cache()->forget("doc_sidebar_{$projectKey}");
    }
}
