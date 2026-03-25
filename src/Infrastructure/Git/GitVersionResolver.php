<?php

namespace Giovani\DocumentationEngine\Infrastructure\Git;

class GitVersionResolver
{
    public function contentAtCommit(string $file, string $commit): string
    {
        return shell_exec("git show $commit:$file");
    }

    public function commit(string $file, string $message): void
    {
        shell_exec("git add $file");
        shell_exec("git commit -m '$message'");
    }
}
