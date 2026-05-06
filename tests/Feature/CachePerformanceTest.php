<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CachePerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (Cache::supportsTags()) {
            Cache::tags(['docs'])->flush();
        } else {
            Cache::flush();
        }
    }

    /** @test */
    public function it_caches_rendered_html_by_slug_and_invalidates_on_publish()
    {
        $repository = app(DocumentRepository::class);

        // 1. Setup doc and version
        $docId = (string) Str::uuid();
        $slug = 'cache-test-'.time();
        $content = '# Hello Cache';
        $checksum = md5($content);

        $repository->save(new Document($docId, $slug, 'Cache Test'));
        $version = $repository->createVersion($docId, $content, $checksum);

        // 2. Request the document (should trigger cache)
        $this->get("/docs/{$slug}")->assertStatus(200);

        $this->assertTrue(Cache::has("doc_render_{$slug}"), "Cache for doc_render_{$slug} should exist");

        // 3. Publish a new version (should invalidate cache)
        $newContent = '# Hello New Cache';
        $newChecksum = md5($newContent);
        $newVersion = $repository->createVersion($docId, $newContent, $newChecksum, null, 'draft');

        $this->post("/docs/{$slug}/versions/{$newVersion->version}/publish")->assertRedirect();

        $this->assertFalse(Cache::has("doc_render_{$slug}"), "Cache for doc_render_{$slug} should be invalidated");
    }

    /** @test */
    public function it_caches_sidebar_and_slug_list()
    {
        // Limpa para garantir estado conhecido
        Cache::forget('doc_slugs_all');
        Cache::forget('doc_sidebar_all');

        // 1. Initial request to populate cache
        $this->get('/docs');

        $this->assertTrue(Cache::has('doc_slugs_all'), "Cache 'doc_slugs_all' should exist");
        $this->assertTrue(Cache::has('doc_sidebar_all'), "Cache 'doc_sidebar_all' should exist");
    }

    /** @test */
    public function it_invalidates_navigation_cache_on_sync()
    {
        // 1. Populate cache
        Cache::forever('doc_slugs_all', ['old-slug']);
        Cache::forever('doc_sidebar_all', ['old-sidebar']);

        // 2. Run sync
        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();

        // 3. Cache should be gone
        $this->assertFalse(Cache::has('doc_slugs_all'), "Cache 'doc_slugs_all' should be invalidated on sync");
        $this->assertFalse(Cache::has('doc_sidebar_all'), "Cache 'doc_sidebar_all' should be invalidated on sync");
    }

    /** @test */
    public function it_logs_performance_metrics_during_sync()
    {
        Log::shouldReceive('info')
            ->with(\Mockery::pattern('/Sync completed/i'))
            ->once();

        // Mantém outros logs funcionando se necessário, ou silencia
        Log::shouldReceive('info')->byDefault();
        Log::shouldReceive('debug')->byDefault();
        Log::shouldReceive('error')->byDefault();

        $sync = app(SyncMarkdownDocs::class);
        $sync->execute();
    }

    /** @test */
    public function it_logs_performance_metrics_during_rendering()
    {
        Log::shouldReceive('debug')
            ->with(\Mockery::pattern('/Markdown rendered/i'))
            ->atLeast()->once();

        Log::shouldReceive('info')->byDefault();
        Log::shouldReceive('debug')->byDefault();

        $renderer = app(MarkdownRenderer::class);
        $renderer->render('# Test');
    }
}
