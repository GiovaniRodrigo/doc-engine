<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Domain\Events\DocumentDraftSaved;
use Giovani\DocumentationEngine\Domain\Events\DocumentVersionPublished;
use Giovani\DocumentationEngine\Domain\Events\DocumentViewed;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;

class EventTrackingTest extends TestCase
{
    // ─── Laravel Events ────────────────────────────────────────────────────────

    #[Test]
    public function viewing_a_document_dispatches_document_viewed_event(): void
    {
        Event::fake([DocumentViewed::class]);

        $this->createDocument('evento-view', '# Evento');

        $this->get('/docs/evento-view');

        Event::assertDispatched(DocumentViewed::class, function (DocumentViewed $e) {
            return $e->slug === 'evento-view';
        });
    }

    #[Test]
    public function saving_a_draft_dispatches_document_draft_saved_event(): void
    {
        Event::fake([DocumentDraftSaved::class]);

        $this->createDocument('evento-draft', '# Rascunho');

        $this->put('/docs/evento-draft', ['content' => '# Atualizado']);

        Event::assertDispatched(DocumentDraftSaved::class, function (DocumentDraftSaved $e) {
            return $e->slug === 'evento-draft';
        });
    }

    #[Test]
    public function publishing_a_version_dispatches_document_version_published_event(): void
    {
        Event::fake([DocumentVersionPublished::class]);

        $slug = 'evento-publish';
        ['version' => $version] = $this->createDocument($slug, '# Publicar');

        $this->post("/docs/{$slug}/versions/{$version}/publish");

        Event::assertDispatched(DocumentVersionPublished::class, function (DocumentVersionPublished $e) use ($slug, $version) {
            return $e->slug === $slug && $e->version === $version;
        });
    }

    // ─── Audit Log ─────────────────────────────────────────────────────────────

    #[Test]
    public function viewing_a_document_writes_to_activity_log(): void
    {
        $this->createDocument('log-view', '# Log');

        $this->get('/docs/log-view');

        $this->assertDatabaseHas('document_activity_log', [
            'event' => 'document.viewed',
            'slug'  => 'log-view',
        ]);
    }

    #[Test]
    public function saving_a_draft_writes_to_activity_log(): void
    {
        $this->createDocument('log-draft', '# Log Draft');

        $this->put('/docs/log-draft', ['content' => '# Novo conteúdo']);

        $this->assertDatabaseHas('document_activity_log', [
            'event' => 'document.draft_saved',
            'slug'  => 'log-draft',
        ]);
    }

    #[Test]
    public function publishing_a_version_writes_to_activity_log(): void
    {
        $slug = 'log-publish';
        ['version' => $version] = $this->createDocument($slug, '# Log Publish');

        $this->post("/docs/{$slug}/versions/{$version}/publish");

        $this->assertDatabaseHas('document_activity_log', [
            'event' => 'document.version_published',
            'slug'  => $slug,
        ]);
    }

    // ─── Analytics ─────────────────────────────────────────────────────────────

    #[Test]
    public function viewing_a_document_increments_analytics_views_count(): void
    {
        $this->createDocument('analytics-view', '# Analytics');

        $this->get('/docs/analytics-view');

        $row = DB::table('document_analytics')->where('slug', 'analytics-view')->first();
        $this->assertNotNull($row);
        $this->assertGreaterThanOrEqual(1, $row->views_count);
        $this->assertNotNull($row->last_viewed_at);
    }

    #[Test]
    public function saving_a_draft_increments_analytics_edits_count(): void
    {
        $this->createDocument('analytics-edit', '# Analytics Edit');

        $this->put('/docs/analytics-edit', ['content' => '# Editado']);

        $row = DB::table('document_analytics')->where('slug', 'analytics-edit')->first();
        $this->assertNotNull($row);
        $this->assertGreaterThanOrEqual(1, $row->edits_count);
        $this->assertNotNull($row->last_edited_at);
    }

    #[Test]
    public function publishing_increments_analytics_publishes_count(): void
    {
        $slug = 'analytics-publish';
        ['version' => $version] = $this->createDocument($slug, '# Analytics Publish');

        $this->post("/docs/{$slug}/versions/{$version}/publish");

        $row = DB::table('document_analytics')->where('slug', $slug)->first();
        $this->assertNotNull($row);
        $this->assertGreaterThanOrEqual(1, $row->publishes_count);
        $this->assertNotNull($row->last_published_at);
    }

    #[Test]
    public function multiple_views_accumulate_in_analytics(): void
    {
        $this->createDocument('analytics-multi', '# Multi');

        $this->get('/docs/analytics-multi');
        $this->get('/docs/analytics-multi');
        $this->get('/docs/analytics-multi');

        $row = DB::table('document_analytics')->where('slug', 'analytics-multi')->first();
        $this->assertSame(3, (int) $row->views_count);
    }
}
