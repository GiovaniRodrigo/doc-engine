<?php

namespace Giovani\DocumentationPlatformEngine\SourceControl\Infrastructure;

use Illuminate\Support\Facades\Process;
use Giovani\DocumentationPlatformEngine\SourceControl\Domain\Services\RepositoryDiffService;

class CommitDiffAnalyzer implements RepositoryDiffService
{
    public function changedMarkdownFiles(
        string $repoPath,
        string $commit
    ): array {

        $result = Process::run(
            "git -C {$repoPath} diff-tree --no-commit-id --name-only -r {$commit}"
        );

        if ($result->failed()) {
            return [];
        }

        return collect(
            explode("\n", trim($result->output()))
        )
        ->filter(fn ($f) => str_ends_with($f, '.md'))
        ->values()
        ->toArray();
    }
}