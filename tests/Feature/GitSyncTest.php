<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class GitSyncTest extends TestCase
{
    #[Test]
    public function it_can_pull_changes_from_remote_repository()
    {
        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);

        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();

        Process::assertRan(function ($process) {
            $command = is_array($process->command)
                ? implode(' ', $process->command)
                : $process->command;

            return str_contains($command, 'git') && str_contains($command, 'pull');
        });
    }

    #[Test]
    public function it_identifies_changed_files_using_git_diff_tree()
    {
        Process::fake([
            '*git*pull*' => Process::result(''),
            '*git*rev-parse*HEAD*' => Process::result('hash'),
            '*git*diff-tree*' => Process::result("M\tdocs/guia.md\nA\tdocs/novo.md"),
        ]);

        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();

        Process::assertRan(function ($process) {
            $command = is_array($process->command)
                ? implode(' ', $process->command)
                : $process->command;

            return str_contains($command, 'diff-tree');
        });
    }
}
