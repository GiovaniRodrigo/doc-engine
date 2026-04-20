<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class SyncDocsCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Garante que o diretório de teste existe
        File::ensureDirectoryExists(base_path('docs/meu-projeto'));
        File::put(base_path('docs/meu-projeto/README.md'), '# Documentação do Projeto');
        
        Process::fake([
            'git pull' => Process::result('Already up to date.'),
            'git rev-parse HEAD' => Process::result('fake-hash'),
            'git diff-tree *' => Process::result(''),
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('docs/meu-projeto'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_sync_documentation_using_project_argument()
    {
        $this->artisan('docs:sync meu-projeto')
            ->expectsOutput('Docs path: ' . base_path('docs/meu-projeto'))
            ->expectsOutput('FILES FOUND: 1')
            ->expectsOutput('SYNCED: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas('documents', [
            'slug' => 'meu-projeto.readme'
        ]);
    }

    /** @test */
    public function it_uses_default_docs_path_when_no_argument_provided()
    {
        File::put(base_path('docs/index.md'), '# Home');

        $this->artisan('docs:sync')
            ->expectsOutput('Docs path: ' . base_path('docs'))
            ->assertExitCode(0);
        
        File::delete(base_path('docs/index.md'));
    }

    /** @test */
    public function it_can_use_custom_path_option()
    {
        $customPath = sys_get_temp_dir() . '/custom_docs';
        File::ensureDirectoryExists($customPath);
        File::put($customPath . '/extra.md', '# Extra');

        $this->artisan('docs:sync --path=' . $customPath)
            ->expectsOutput('Docs path: ' . $customPath)
            ->expectsOutput('FILES FOUND: 1')
            ->assertExitCode(0);

        File::deleteDirectory($customPath);
    }
}
