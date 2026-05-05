<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;

class DocumentationEngineEnvScriptTest extends TestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDirectory = sys_get_temp_dir() . '/documentation-engine-env-' . uniqid();
        File::ensureDirectoryExists($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tempDirectory);

        parent::tearDown();
    }

    #[Test]
    public function it_creates_env_when_it_does_not_exist(): void
    {
        $envPath = $this->tempDirectory . '/.env';

        $this->runScript(['--env=' . $envPath]);

        $this->assertFileExists($envPath);
        $this->assertStringContainsString('DOC_ENGINE_PATH=docs', File::get($envPath));
    }

    #[Test]
    public function it_preserves_existing_variables_and_adds_only_missing_keys(): void
    {
        $envPath = $this->tempDirectory . '/.env';
        File::put($envPath, "APP_NAME=Demo\nDOC_ENGINE_PATH=custom-docs\n");

        $this->runScript(['--env=' . $envPath]);

        $contents = File::get($envPath);

        $this->assertStringContainsString('APP_NAME=Demo', $contents);
        $this->assertStringContainsString('DOC_ENGINE_PATH=custom-docs', $contents);
        $this->assertSame(1, substr_count($contents, 'DOC_ENGINE_PATH='));
        $this->assertStringContainsString('DOCUMENTATION_AI_PROVIDER=openai', $contents);
    }

    #[Test]
    public function it_updates_env_and_env_example_by_default(): void
    {
        File::put($this->tempDirectory . '/.env.example', "APP_NAME=Demo\n");

        $this->runScript([], $this->tempDirectory);

        $this->assertFileExists($this->tempDirectory . '/.env');
        $this->assertStringContainsString('DOC_ENGINE_PATH=docs', File::get($this->tempDirectory . '/.env'));
        $this->assertStringContainsString('DOC_ENGINE_PATH=docs', File::get($this->tempDirectory . '/.env.example'));
    }

    #[Test]
    public function it_does_not_duplicate_the_documentation_engine_block(): void
    {
        $envPath = $this->tempDirectory . '/.env';

        $this->runScript(['--env=' . $envPath]);
        $this->runScript(['--env=' . $envPath]);

        $contents = File::get($envPath);

        $this->assertSame(1, substr_count($contents, '# Documentation Engine'));
        $this->assertSame(1, substr_count($contents, 'GEMINI_MAX_OUTPUT_TOKENS='));
    }

    /**
     * @param array<int, string> $arguments
     */
    private function runScript(array $arguments = [], ?string $workingDirectory = null): void
    {
        $command = implode(' ', array_map('escapeshellarg', array_merge([
            PHP_BINARY,
            dirname(__DIR__, 2) . '/scripts/documentation-engine-env',
        ], $arguments)));

        $descriptorSpec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, $workingDirectory ?? dirname(__DIR__, 2));

        $this->assertIsResource($process);

        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode, trim((string) $output . "\n" . (string) $errors));
    }
}
