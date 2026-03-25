<?php

namespace Giovani\DocumentationEngine\Application\DTO;

class DocNode
{
    public function __construct(
        public string $title,
        public ?string $slug = null,
        public array $children = []
    ) {}
}