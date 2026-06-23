<?php

namespace Giovani\DocumentationEngine\Domain\Events;

class DocumentSynced
{
    public function __construct(
        public readonly int $created,
        public readonly int $updated,
        public readonly int $skipped,
        public readonly ?string $commit,
    ) {}
}
