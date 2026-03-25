<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Domain\Aggregates;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\Document;
use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\DocumentVersion;
use DomainException;

class DocumentAggregate
{
    public function createVersion(
        Document $document,
        string $markdown,
        string $commit
    ): DocumentVersion {

        $hash = sha1($markdown);

        if ($document->hasVersionHash($hash)) {
            throw new DomainException('Version already exists');
        }

        return $document->addVersion(
            markdown: $markdown,
            hash: $hash,
            commit: $commit
        );
    }
}