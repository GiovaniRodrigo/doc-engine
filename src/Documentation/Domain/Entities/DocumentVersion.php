<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities;

use Document;

class DocumentVersion
{
    public function __construct(
        public readonly Document $document,
        public string $markdown,
        public string $hash,
        public string $commitHash,
        public ?string $html = null,
        public ?string $state = 'draft'
    ) {}
}