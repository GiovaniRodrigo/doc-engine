<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\Events;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\DocumentVersion;

class DocumentVersionCreated
{
    public function __construct(
        public readonly string $project,
        public readonly string $documentSlug,
        public readonly string $documentPath,
        public readonly string $commitHash,
        public readonly string $versionHash,
        public readonly string $state,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}

    public static function fromVersion(DocumentVersion $version): self
    {
        return new self(
            project: $version->document->project,
            documentSlug: $version->document->slug,
            documentPath: $version->document->path,
            commitHash: $version->commitHash,
            versionHash: $version->hash,
            state: $version->state ?? 'draft',
            occurredAt: new \DateTimeImmutable(),
        );
    }
}