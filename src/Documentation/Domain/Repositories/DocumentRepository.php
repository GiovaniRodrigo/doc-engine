<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\Document;
use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\DocumentVersion;

interface DocumentRepository
{
    public function findByProjectAndPath(
        string $project,
        string $path
    ): ?Document;

    public function save(Document $document): void;

    public function saveVersion(DocumentVersion $version): void;
}