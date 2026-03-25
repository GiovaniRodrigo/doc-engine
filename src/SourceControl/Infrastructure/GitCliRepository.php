<?php

namespace Giovani\DocumentationPlatformEngine\SourceControl\Infrastructure;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class GitCliRepository
{
    public function pull(string $repoPath): void
    {
        $result = Process::run(
            "git -C {$repoPath} pull --ff-only"
        );

        if ($result->failed()) {
            throw new RuntimeException($result->errorOutput());
        }
    }
}