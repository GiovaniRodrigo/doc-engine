<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class SyncDocsCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Garante que o diretório de teste existe
        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/README.md'), '# Documentação do Projeto');

        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('fake-hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);
    }

    protected function tearDown(): void
    {
        File::delete(base_path('docs/README.md'));
        File::delete(base_path('docs/meu-projeto.summary.md'));
        parent::tearDown();
    }

    #[Test]
    public function it_can_sync_documentation_using_project_argument()
    {
        $this->artisan('docs:sync meu-projeto')
            ->expectsOutput('Docs base path: '.base_path('docs'))
            ->expectsOutput('Syncing under project prefix: meu-projeto')
            ->expectsOutput('READ FILES: 1')
            ->expectsOutput('CREATED VERSIONS: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas('documents', [
            'slug' => 'meu-projeto',
        ]);
    }

    #[Test]
    public function it_uses_default_docs_path_when_no_argument_provided()
    {
        File::put(base_path('docs/index.md'), '# Home');

        $this->artisan('docs:sync')
            ->expectsOutput('Docs base path: '.base_path('docs'))
            ->assertExitCode(0);

        File::delete(base_path('docs/index.md'));
    }

    #[Test]
    public function it_can_use_custom_path_option()
    {
        $customPath = sys_get_temp_dir().'/custom_docs';
        File::ensureDirectoryExists($customPath);
        File::put($customPath.'/extra.md', '# Extra');

        $this->artisan('docs:sync --path='.$customPath)
            ->expectsOutput('Docs base path: '.$customPath)
            ->expectsOutput('READ FILES: 1')
            ->expectsOutput('CREATED VERSIONS: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas('documents', [
            'slug' => 'extra',
        ]);

        File::deleteDirectory($customPath);
    }

    #[Test]
    public function it_can_preview_sync_with_dry_run_without_writing_to_database()
    {
        $this->artisan('docs:sync --dry-run')
            ->expectsOutput('Dry-run enabled: no documents, versions, summaries, or caches will be changed.')
            ->expectsOutput('READ FILES: 1')
            ->expectsOutput('CREATED VERSIONS: 1')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('documents', [
            'slug' => 'readme',
        ]);
        $this->assertFalse(File::exists(base_path('docs/summary.md')));
    }

    #[Test]
    public function it_fails_with_clear_error_when_docs_path_does_not_exist()
    {
        $missingPath = sys_get_temp_dir().'/missing-docs-'.uniqid();

        $this->artisan('docs:sync --path='.$missingPath)
            ->expectsOutput('Docs base path: '.$missingPath)
            ->expectsOutput("Documentation sync failed: Documentation path does not exist: {$missingPath}")
            ->assertExitCode(1);
    }
}
