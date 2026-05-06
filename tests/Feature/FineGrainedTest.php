<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class FineGrainedTest extends TestCase
{
    #[Test]
    public function it_handles_non_utf8_encoding_gracefully_during_sync()
    {
        File::ensureDirectoryExists(base_path('docs'));

        // Create a file with ISO-8859-1 encoding
        $originalText = '# Título com acentuação';
        $content = iconv('UTF-8', 'ISO-8859-1', $originalText);
        File::put(base_path('docs/encoding.md'), $content);

        $this->artisan('docs:sync')
            ->assertExitCode(0);

        $this->assertDatabaseHas('documents', ['slug' => 'encoding']);

        // Verify content was correctly converted/read (Eloquent should have it in UTF-8 now)
        $doc = DB::table('document_versions')
            ->join('documents', 'documents.id', '=', 'document_versions.document_id')
            ->where('documents.slug', 'encoding')
            ->first();

        // If it was correctly handled, the accent should be there
        $this->assertStringContainsString('Título com acentuação', $doc->content);
    }

    #[Test]
    public function it_normalizes_slugs_from_filenames_with_accents_and_spaces()
    {
        File::ensureDirectoryExists(base_path('docs'));
        File::put(base_path('docs/Guia do Usuário.md'), '# Guia');

        $this->artisan('docs:sync')
            ->assertExitCode(0);

        // Expectation: slug should be 'guia.do.usuario' or 'guia-do-usuario'
        // Current implementation uses str_replace([' ', '/'], '.', ...) loosely or doesn't handle spaces.
        // We want 'guia.do.usuario' (standard for this project)
        $this->assertDatabaseHas('documents', ['slug' => 'guia.do.usuario']);
    }

    #[Test]
    public function it_prevents_xss_in_markdown_rendering()
    {
        $renderer = new MarkdownRenderer;
        $markdown = "# Hello\n<script>alert('xss')</script>\n<img src=x onerror=alert('xss')>";

        $html = $renderer->render($markdown);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('onerror', $html);
    }

    #[Test]
    public function it_prevents_path_traversal_via_slug()
    {
        $this->createDocument('secret', 'Sensitive Data');

        // Attempting to access using traversal
        $response = $this->get('/docs/../../secret');

        // Laravel's routing might catch ../.., but if it passes, normalizeSlug should handle it.
        // We expect it to not find the 'secret' document via traversal.
        $response->assertStatus(404);
    }

    #[Test]
    public function it_invalidates_cache_surgically_per_project()
    {
        $this->createDocument('proj-a.doc1', 'Content A');
        $this->createDocument('proj-b.doc2', 'Content B');

        Cache::forever('doc_sidebar_proj-a', ['sidebar-a']);
        Cache::forever('doc_sidebar_proj-b', ['sidebar-b']);

        // Publish a new version of proj-a.doc1
        $this->put('/docs/proj-a.doc1', ['content' => 'New Content A']);
        $draft = DB::table('document_versions')
            ->where('state', 'draft')
            ->first();

        $this->post("/docs/proj-a.doc1/versions/{$draft->version}/publish");

        // Proj A sidebar cache should be gone
        $this->assertFalse(Cache::has('doc_sidebar_proj-a'));
        // Proj B sidebar cache should remain
        $this->assertTrue(Cache::has('doc_sidebar_proj-b'));
    }
}
