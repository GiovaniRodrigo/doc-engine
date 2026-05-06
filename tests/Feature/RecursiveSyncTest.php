<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class RecursiveSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(base_path('docs/arquitetura'));
        File::ensureDirectoryExists(base_path('docs/Arquitetura/Decisoes'));
        File::ensureDirectoryExists(base_path('docs/backend/api'));

        File::put(base_path('docs/README.md'), '# Home');
        File::put(base_path('docs/arquitetura/ddd.md'), '# Domain-Driven Design');
        File::put(base_path('docs/Arquitetura/Decisoes/ADR-001-laravel-octane-swoole.md'), '# ADR 001');
        File::put(base_path('docs/backend/api/autenticacao.md'), '# Autenticação');

        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('recursive-hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('docs'));
        parent::tearDown();
    }

    #[Test]
    public function it_identifies_markdown_files_recursively_and_generates_nested_slugs()
    {
        $this->artisan('docs:sync')
            ->expectsOutput('READ FILES: 4')
            ->expectsOutput('CREATED VERSIONS: 4')
            ->assertExitCode(0);

        // Verifica se os slugs foram gerados corretamente com pontos
        $this->assertDatabaseHas('documents', ['slug' => 'readme']);
        $this->assertDatabaseHas('documents', ['slug' => 'arquitetura.ddd']);
        $this->assertDatabaseHas('documents', ['slug' => 'arquitetura.decisoes.adr-001-laravel-octane-swoole']);
        $this->assertDatabaseHas('documents', ['slug' => 'backend.api.autenticacao']);

        // Verifica se o título padrão é gerado corretamente a partir do slug
        $this->assertDatabaseHas('documents', [
            'slug' => 'backend.api.autenticacao',
            'title' => 'Backend api autenticacao',
        ]);
    }

    #[Test]
    public function it_includes_subdirectories_in_the_generated_summary()
    {
        $this->artisan('docs:sync')
            ->assertExitCode(0);

        $summaryPath = base_path('docs/summary.md');
        $this->assertTrue(File::exists($summaryPath));

        $content = File::get($summaryPath);

        // Verifica se os diretórios aparecem como cabeçalhos no sumário
        $this->assertStringContainsString('## Arquitetura', $content);
        $this->assertStringContainsString('## Backend api', $content);

        // Verifica se os links apontam para os slugs aninhados
        $this->assertStringContainsString('[Ddd](/docs/arquitetura.ddd)', $content);
        $this->assertStringContainsString('[Autenticacao](/docs/backend.api.autenticacao)', $content);
    }
}
