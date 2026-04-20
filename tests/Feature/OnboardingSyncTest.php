<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class OnboardingSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Garante que o diretório de teste existe
        // O teste agora reflete que os arquivos podem estar em qualquer lugar (base_path('docs'))
        // e o argumento 'meu-sistema' apenas cria a rota/slug prefixado.
        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/README.md'), '# Documentação Global ou do Projeto');
        File::put(base_path('docs/instalacao.md'), '# Guia de Instalação');
        
        Process::fake([
            'git pull' => Process::result('Already up to date.'),
            'git rev-parse HEAD' => Process::result('abcdef1234567890'),
            'git diff-tree *' => Process::result(''),
        ]);
    }

    protected function tearDown(): void
    {
        File::delete(base_path('docs/README.md'));
        File::delete(base_path('docs/instalacao.md'));
        File::delete(base_path('docs/summary.md'));
        File::delete(base_path('docs/meu-sistema.summary.md'));
        parent::tearDown();
    }

    /** @test */
    public function it_syncs_under_project_prefix_without_requiring_subdirectory()
    {
        // php artisan docs:sync meu-sistema
        // Deve pegar os arquivos de base_path('docs') e prefixá-los
        $this->artisan('docs:sync meu-sistema')
            ->expectsOutput('Docs base path: ' . base_path('docs'))
            ->expectsOutput('Syncing under project prefix: meu-sistema')
            ->expectsOutput('SYNCED: 2')
            ->assertExitCode(0);

        // README.md da raiz deve virar exatamente o slug do projeto
        $this->assertDatabaseHas('documents', [
            'slug' => 'meu-sistema'
        ]);

        // Outros arquivos ganham prefixo ponto
        $this->assertDatabaseHas('documents', [
            'slug' => 'meu-sistema.instalacao'
        ]);
    }

    /** @test */
    public function it_generates_project_specific_summary()
    {
        $this->artisan('docs:sync meu-sistema')
            ->assertExitCode(0);

        // Deve criar um meu-sistema.summary.md na raiz docs/
        $this->assertTrue(File::exists(base_path('docs/meu-sistema.summary.md')));
        
        $content = File::get(base_path('docs/meu-sistema.summary.md'));
        $this->assertStringContainsString('# Sumário: Meu-sistema', $content);
        $this->assertStringContainsString('[Instalacao](/docs/meu-sistema.instalacao)', $content);
        $this->assertStringContainsString('[Readme](/docs/meu-sistema)', $content);
    }
}
