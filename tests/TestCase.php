<?php

namespace Giovani\DocumentationEngine\Tests;

use Giovani\DocumentationEngine\DocumentationServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            DocumentationServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('view.paths', [
            __DIR__ . '/Fixtures/resources/views',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('docs'));

        parent::tearDown();
    }

    protected function createDocument(string $slug = 'guia', string $content = '# Guia'): array
    {
        $documentId = (string) Str::uuid();
        $version = (string) Str::uuid();

        DB::table('documents')->insert([
            'id' => $documentId,
            'slug' => $slug,
            'title' => ucfirst(str_replace('.', ' ', $slug)),
            'tags' => json_encode([]),
            'state' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('document_versions')->insert([
            'document_id' => $documentId,
            'version' => $version,
            'content' => $content,
            'checksum' => md5($content),
            'state' => 'published',
            'git_commit' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'document_id' => $documentId,
            'version' => $version,
        ];
    }
}
