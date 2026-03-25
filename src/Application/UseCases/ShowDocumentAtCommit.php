<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;

class ShowDocumentAtCommit
{
    public function __construct(private GitVersionResolver $git) {}

    public function execute(string $path, string $commit)
    {
        return $this->git->contentAtCommit($path, $commit);
    }
}
