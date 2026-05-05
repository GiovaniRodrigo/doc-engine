<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createDocument('guia', '# Guia');
    }

    #[Test]
    public function it_caches_rendered_html()
    {
        $slug = 'guia';
        
        Cache::shouldReceive('remember')
            ->once()
            ->with("doc_render_{$slug}", \Mockery::any(), \Mockery::any())
            ->andReturn('<h1>Rendered Content</h1>');

        $response = $this->get("/docs/{$slug}");
        $response->assertStatus(200);
        $response->assertSee('Rendered Content');
    }

    #[Test]
    public function it_does_not_invalidate_published_cache_when_saving_a_draft()
    {
        $slug = 'guia';

        Cache::spy();

        $response = $this->put("/docs/{$slug}", [
            'content' => '# Novo Conteudo'
        ]);

        $response->assertRedirect("/docs/{$slug}/edit");

        Cache::shouldNotHaveReceived('forget');
    }
}
