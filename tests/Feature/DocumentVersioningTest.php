<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class DocumentVersioningTest extends TestCase
{
    /** @test */
    public function document_versions_table_has_status_column()
    {
        // RF07 - Controle de Estados
        $this->assertTrue(
            Schema::hasColumn('document_versions', 'status'),
            'Column "status" is missing in "document_versions" table.'
        );
    }

    /** @test */
    public function it_persists_git_commit_hash_when_syncing()
    {
        // RF05 - Versionamento por Commit
        // Este teste verificaria se ao rodar o Sync, o hash do commit é salvo.
        $this->markTestIncomplete('A captura do hash do commit durante o Sync não está implementada.');
    }
}
