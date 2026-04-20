<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Process;

class GitSyncTest extends TestCase
{
    /** @test */
    public function it_can_pull_changes_from_remote_repository()
    {
        // RF01 - Sincronização via Git
        Process::fake([
            'git pull *' => Process::result('Already up to date.'),
        ]);

        // Simulação de um serviço ou comando que faria o pull
        // Atualmente não existe essa lógica centralizada
        $this->markTestIncomplete('Lógica de git pull automático não implementada.');
    }

    /** @test */
    public function it_identifies_changed_files_using_git_diff_tree()
    {
        // RF02 - Detecção de Mudanças via Git
        Process::fake([
            'git diff-tree *' => Process::result("M\tdocs/guia.md\nA\tdocs/novo.md"),
        ]);

        // Atualmente o SyncMarkdownDocs lê TODOS os arquivos do disco
        $this->markTestIncomplete('Lógica de detecção otimizada via git diff-tree não implementada.');
    }
}
