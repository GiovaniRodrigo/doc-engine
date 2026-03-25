<?php

namespace Giovani\DocumentationEngine\Domain\Repositories;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Entities\DocumentVersion;

interface DocumentRepository
{
    public function save(Document $document): void;

    public function saveVersion(DocumentVersion $version): void;

    public function findBySlug(string $slug): ?Document;

    public function latestVersion(string $documentId): ?DocumentVersion;
}
