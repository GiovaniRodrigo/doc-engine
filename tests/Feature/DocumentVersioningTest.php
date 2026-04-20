<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Process;
use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;

class DocumentVersioningTest extends TestCase
{
    /** @test */
    public function document_versions_table_has_state_column()
    {
        $this->assertTrue(
            Schema::hasColumn('document_versions', 'state'),
            'Column "state" is missing in "document_versions" table.'
        );
    }

    /** @test */
    public function it_persists_git_commit_hash_when_syncing()
    {
        Process::fake([
            'git pull' => Process::result('Already up to date.'),
            'git rev-parse HEAD' => Process::result('a1b2c3d4e5f6g7h8i9j0'),
            'git diff-tree *' => Process::result(''),
        ]);

        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();

        $this->assertDatabaseHas('document_versions', [
            'git_commit' => 'a1b2c3d4e5f6g7h8i9j0'
        ]);
    }
}
