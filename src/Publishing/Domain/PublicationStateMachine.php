<?php

namespace Giovani\DocumentationPlatformEngine\Publishing\Domain;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\DocumentVersion;
use RuntimeException;

class PublicationStateMachine
{
    public function publish(DocumentVersion $version): void
    {
        if ($version->state !== 'draft') {
            throw new RuntimeException('Invalid state transition');
        }

        $version->state = 'published';
    }
}