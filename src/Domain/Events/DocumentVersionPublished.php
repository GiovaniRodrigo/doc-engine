<?php

namespace Giovani\DocumentationEngine\Domain\Events;

class DocumentVersionPublished
{
    public function __construct(
        public readonly string $slug,
        public readonly string $version,
        public readonly ?string $actorId,
        public readonly ?string $actorName,
        public readonly ?string $ipAddress,
    ) {}
}
