<?php

namespace Giovani\DocumentationPlatformEngine\SourceControl\Infrastructure;

use Illuminate\Support\Facades\Process;

class BranchCheckoutService
{
    public function checkout(string $repoPath, string $branch): void
    {
        Process::run("git -C {$repoPath} fetch");
        Process::run("git -C {$repoPath} checkout {$branch}");
        Process::run("git -C {$repoPath} pull");
    }
}