<?php

namespace Giovani\DocumentationEngine\Application\DTO;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Entities\DocumentVersion;

class DocumentViewData
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public array $tags,
        public string $content,
        public string $version,
        public string $checksum,
        public ?string $gitCommit,
        public string $state = 'published',
        public ?string $createdAt = null,
    ) {}

    public static function fromEntities(Document $document, DocumentVersion $version): self
    {
        return new self(
            id: $document->id,
            slug: $document->slug,
            title: $document->title,
            tags: $document->tags,
            content: $version->content,
            version: $version->version,
            checksum: $version->checksum,
            gitCommit: $version->gitCommit,
            state: $version->state,
            createdAt: $version->createdAt,
        );
    }
}
