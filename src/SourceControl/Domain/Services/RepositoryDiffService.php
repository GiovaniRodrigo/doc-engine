<?php

namespace Giovani\DocumentationPlatformEngine\SourceControl\Domain\Services;

interface RepositoryDiffService
{
    public function changedMarkdownFiles(
        string $repoPath,
        string $commit
    ): array;
}