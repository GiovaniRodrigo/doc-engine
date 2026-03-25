<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities;

use Giovani\DocumentationPlatformEngine\Domain\Entities\DocumentVersion;

class Document
{
    private array $versions = [];

    public function __construct(
        public readonly string $project,
        public readonly string $path,
        public string $slug,
        public ?string $title = null,
    ) {}

    public function addVersion(
        string $markdown,
        string $hash,
        string $commit
    ): DocumentVersion {

        $version = new DocumentVersion(
            document: $this,
            markdown: $markdown,
            hash: $hash,
            commitHash: $commit
        );

        $this->versions[] = $version;

        return $version;
    }

    public function hasVersionHash(string $hash): bool
    {
        foreach ($this->versions as $v) {
            if ($v->hash === $hash) {
                return true;
            }
        }

        return false;
    }
}