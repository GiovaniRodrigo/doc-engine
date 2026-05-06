<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Giovani\DocumentationEngine\Tests\TestCase;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EditorialWorkflowTest extends TestCase
{
    #[Test]
    public function editing_a_document_saves_a_draft_without_changing_the_published_version(): void
    {
        $this->createDocument('guia', '# Publicado');

        $response = $this->put('/docs/guia', [
            'content' => '# Rascunho',
        ]);

        $response->assertRedirect('/docs/guia/edit');
        $this->assertDatabaseHas('document_versions', [
            'content' => '# Rascunho',
            'state' => 'draft',
        ]);

        $response = $this->get('/docs/guia');

        $response->assertStatus(200);
        $response->assertSee('Publicado');
        $response->assertDontSee('Rascunho');
    }

    #[Test]
    public function edit_page_is_accessible_and_shows_current_content(): void
    {
        $this->createDocument('guia', '# Conteudo Atual');

        $response = $this->get('/docs/guia/edit');

        $response->assertStatus(200);
        $response->assertSee('Editar documento');
        $response->assertSee('guia');
        $response->assertSee('# Conteudo Atual');
    }

    #[Test]
    public function edit_page_shows_validation_errors(): void
    {
        $this->createDocument('guia', '# Conteudo');

        // Simula erro de validacao ao postar conteudo vazio
        $response = $this->from('/docs/guia/edit')->put('/docs/guia', [
            'content' => '',
        ]);

        $response->assertRedirect('/docs/guia/edit');
        $response->assertSessionHasErrors('content');

        $response = $this->get('/docs/guia/edit');
        $response->assertStatus(200);
        $response->assertSee('The content field is required');
    }

    #[Test]
    public function publishing_a_draft_replaces_the_public_version_and_invalidates_cache(): void
    {
        $created = $this->createDocument('guia', '# Publicado');

        $this->put('/docs/guia', [
            'content' => '# Rascunho',
        ]);

        $draft = DB::table('document_versions')
            ->where('state', 'draft')
            ->first();

        Cache::spy();

        $response = $this->post("/docs/guia/versions/{$draft->version}/publish");

        $response->assertRedirect('/docs/guia/versions');
        $this->assertDatabaseHas('document_versions', [
            'version' => $draft->version,
            'state' => 'published',
        ]);
        $this->assertDatabaseHas('document_versions', [
            'version' => $created['version'],
            'state' => 'archived',
        ]);
        Cache::shouldHaveReceived('forget')->with('doc_render_guia')->once();

        $document = app(ShowDocument::class)->execute('guia');

        $this->assertSame('# Rascunho', $document->content);
    }

    #[Test]
    public function versions_page_lists_document_versions(): void
    {
        $this->createDocument('guia', '# Publicado');

        $this->put('/docs/guia', [
            'content' => '# Rascunho',
        ]);

        $response = $this->get('/docs/guia/versions');

        $response->assertStatus(200);
        $response->assertSee('Histórico de versões');
        $response->assertSee('draft');
        $response->assertSee('published');
    }

    #[Test]
    public function versions_can_be_compared(): void
    {
        $created = $this->createDocument('guia', "# Título\nLinha antiga");

        $this->put('/docs/guia', [
            'content' => "# Título\nLinha nova",
        ]);

        $draft = DB::table('document_versions')
            ->where('state', 'draft')
            ->first();

        $response = $this->get("/docs/guia/versions/compare?from={$created['version']}&to={$draft->version}");

        $response->assertStatus(200);
        $response->assertSee('Comparar versões');
        $response->assertSee('- Linha antiga');
        $response->assertSee('+ Linha nova');
    }
}
