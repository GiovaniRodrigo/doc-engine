<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Infrastructure\Persistence\EloquentDocumentRepository;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;

class DocumentVersioningTest extends TestCase
{
    #[Test]
    public function document_versions_table_has_state_column()
    {
        $this->assertTrue(
            Schema::hasColumn('document_versions', 'state'),
            'Column "state" is missing in "document_versions" table.'
        );
    }

    #[Test]
    public function it_persists_git_commit_hash_when_syncing()
    {
        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('a1b2c3d4e5f6g7h8i9j0'),
            '*git*diff-tree*' => Process::result(''),
        ]);

        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/guia.md'), '# Guia');

        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();

        $this->assertDatabaseHas('document_versions', [
            'git_commit' => 'a1b2c3d4e5f6g7h8i9j0',
        ]);
    }

    #[Test]
    public function it_does_not_create_a_new_version_when_content_did_not_change()
    {
        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('same-hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);

        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/guia.md'), '# Guia');

        $sync = app(SyncMarkdownDocs::class);
        $first = $sync->execute();
        $second = $sync->execute();

        $this->assertSame(['guia'], $first->createdVersions);
        $this->assertSame([], $second->createdVersions);
        $this->assertSame(['guia.md', 'summary.md'], $second->ignoredFiles);
        $this->assertDatabaseCount('document_versions', 1);
    }

    #[Test]
    public function it_archives_documents_removed_from_filesystem()
    {
        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('archive-hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);

        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/guia.md'), '# Guia');

        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();

        File::delete(base_path('docs/guia.md'));

        $result = $sync->execute();

        $this->assertSame(['guia'], $result->archivedDocuments);
        $this->assertDatabaseHas('documents', [
            'slug' => 'guia',
            'state' => 'archived',
        ]);
    }

    #[Test]
    public function dry_run_reports_changes_without_creating_versions()
    {
        Process::fake([
            '*git*rev-parse*HEAD*' => Process::result('dry-hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);

        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/guia.md'), '# Guia');

        $result = app(SyncMarkdownDocs::class)->execute(dryRun: true);

        $this->assertTrue($result->dryRun);
        $this->assertSame(['guia'], $result->createdVersions);
        $this->assertDatabaseCount('documents', 0);
        $this->assertDatabaseCount('document_versions', 0);
    }

    #[Test]
    public function it_hydrates_latest_version_with_default_state_when_git_commit_is_null()
    {
        $documentId = (string) Str::uuid();

        DB::table('documents')->insert([
            'id' => $documentId,
            'slug' => 'example',
            'title' => 'Example',
            'tags' => json_encode([]),
            'state' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('document_versions')->insert([
            'document_id' => $documentId,
            'version' => 'v1',
            'content' => '# Example',
            'checksum' => md5('# Example'),
            'state' => 'published',
            'git_commit' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $version = (new EloquentDocumentRepository)->latestVersion($documentId);

        $this->assertSame('published', $version->state);
        $this->assertNull($version->gitCommit);
    }
}
