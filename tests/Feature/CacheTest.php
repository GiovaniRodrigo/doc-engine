<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Giovani\DocumentationEngine\Http\DocumentationController;

class CacheTest extends TestCase
{
    /** @test */
    public function it_caches_rendered_html()
    {
        // RF17 - Sistema de Cache
        // Este teste verifica se o HTML renderizado é buscado no cache
        $slug = 'guia-instalação';
        
        // Simular que o documento existe no banco (mock ou factory se disponível)
        // Como o cache ainda não está implementado no Controller, este teste deve mostrar a falha
        
        Cache::shouldReceive('remember')
            ->once()
            ->with("doc_render_{$slug}", \Mockery::any(), \Mockery::any());

        // Chamar o endpoint que renderiza
        $this->get("/docs/{$slug}");
    }
}
