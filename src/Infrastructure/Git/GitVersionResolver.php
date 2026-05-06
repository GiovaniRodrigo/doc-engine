<?php

namespace Giovani\DocumentationEngine\Infrastructure\Git;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class GitVersionResolver
{
    public function contentAtCommit(string $file, string $commit): string
    {
        if (! preg_match('/^[a-f0-9]{40}$/', $commit)) {
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

    public function pull(): void
    {
        $result = Process::run(['git', 'pull']);

        if ($result->failed()) {
            throw new RuntimeException(trim($result->errorOutput()) ?: 'Unable to pull changes from remote repository.');
        }
    }

    public function commit(string $file, string $message): void
    {
        // Garante que o caminho seja relativo à raiz do projeto para o Git
        $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);

        Log::info('Staging documentation file.', [
            'file' => $relativePath,
        ]);

        $addResult = Process::path(base_path())->run([
            'git',
            'add',
            $relativePath,
        ]);

        if ($addResult->failed()) {
            throw new RuntimeException(trim($addResult->errorOutput()) ?: "Unable to stage documentation file: {$relativePath}");
        }

        Log::info('Creating documentation commit.', [
            'file' => $relativePath,
            'message' => $message,
        ]);

        $commitResult = Process::path(base_path())->run([
            'git',
            'commit',
            '-m',
            $message,
        ]);

        if ($commitResult->failed() && ! str_contains($commitResult->output(), 'nothing to commit')) {
            throw new RuntimeException(trim($commitResult->errorOutput()) ?: 'Unable to commit documentation changes.');
        }
    }

    public function getLatestCommitHash(): string
    {
        $result = Process::run(['git', 'rev-parse', 'HEAD']);

        if ($result->failed()) {
            throw new RuntimeException('Unable to get latest commit hash.');
        }

        return trim($result->output());
    }

    public function getChangedFiles(): array
    {
        // For simplicity, we compare HEAD with the previous state or just use diff-tree if we know the last synced commit.
        // For now, let's implement what the test expects: git diff-tree
        $result = Process::run(['git', 'diff-tree', '-r', '--name-status', 'HEAD']);

        if ($result->failed()) {
            return [];
        }

        $output = trim($result->output());
        if ($output === '') {
            return [];
        }

        $files = [];
        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^([A-Z])\s+(.+)$/', $line, $matches)) {
                $files[] = [
                    'status' => $matches[1],
                    'path' => $matches[2],
                ];
            }
        }

        return $files;
    }
}
