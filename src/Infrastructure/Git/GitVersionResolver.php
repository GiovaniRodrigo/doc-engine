<?php

namespace Giovani\DocumentationEngine\Infrastructure\Git;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class GitVersionResolver
{
    public function contentAtCommit(string $file, string $commit): string
    {
        if (!preg_match('/^[a-f0-9]{40}$/', $commit)) {
            throw new RuntimeException('Invalid git commit hash.');
        }

        $result = Process::run([
            'git',
            'show',
            "{$commit}:{$file}",
        ]);

        if ($result->failed()) {
            throw new RuntimeException(trim($result->errorOutput()) ?: 'Unable to read file content at commit.');
        }

        return $result->output();
    }

    public function commit(string $file, string $message): void
    {
        Log::info('Staging documentation file.', [
            'file' => $file,
        ]);

        $addResult = Process::run([
            'git',
            'add',
            $file,
        ]);

        if ($addResult->failed()) {
            throw new RuntimeException(trim($addResult->errorOutput()) ?: 'Unable to stage documentation file.');
        }

        Log::info('Creating documentation commit.', [
            'file' => $file,
            'message' => $message,
        ]);

        $commitResult = Process::run([
            'git',
            'commit',
            '-m',
            $message,
        ]);

        if ($commitResult->failed()) {
            throw new RuntimeException(trim($commitResult->errorOutput()) ?: 'Unable to commit documentation changes.');
        }
    }
}
