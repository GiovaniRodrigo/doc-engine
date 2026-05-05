<?php

namespace Giovani\DocumentationEngine\Domain\Entities;

class Document
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public array $tags = [],
        public string $state = 'active'
    ) {}
}
