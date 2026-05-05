<?php

namespace Giovani\DocumentationEngine\Domain\Repositories;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Entities\DocumentVersion;

interface DocumentRepository
{
    public function save(Document $document): void;

    public function saveVersion(DocumentVersion $version): void;

    public function createVersion(
        string $documentId,
        string $content,
        string $checksum,
        ?string $gitCommit = null,
        string $state = 'published'
    ): DocumentVersion;

    public function findBySlug(string $slug): ?Document;

    public function latestVersion(string $documentId): ?DocumentVersion;

    public function latestPublishedVersion(string $documentId): ?DocumentVersion;

    public function findVersion(string $documentId, string $version): ?DocumentVersion;

    /**
     * @return array<int, DocumentVersion>
     */
    public function versions(string $documentId): array;

    public function publishVersion(string $documentId, string $version): void;

    public function allSlugs(): array;

    public function allActiveSlugs(?string $project = null): array;

    public function search(string $query, int $limit = 20): array;

    public function archiveBySlug(string $slug): void;

    public function activateBySlug(string $slug): void;
}
