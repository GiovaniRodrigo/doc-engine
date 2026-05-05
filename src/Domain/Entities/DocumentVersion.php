<?php

namespace Giovani\DocumentationEngine\Domain\Entities;

class DocumentVersion
{
    public function __construct(
        public string $documentId,
        public string $version,
        public string $content,
        public string $checksum,
        public string $state = 'published',
        public ?string $gitCommit = null,
        public ?string $createdAt = null
    ) {}
}
