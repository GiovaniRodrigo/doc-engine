<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\Fixtures\RejectDocsMiddleware;
use Giovani\DocumentationEngine\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RouteEditMiddlewareTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('documentation-engine.edit_middleware', [
            RejectDocsMiddleware::class,
        ]);
    }

    #[Test]
    public function edit_routes_use_the_configured_edit_middleware(): void
    {
        $this->createDocument('guia', '# Guia');

        // Rotas de leitura nao devem ser rejeitadas pelo edit_middleware
        $this->get('/docs')->assertStatus(200);
        $this->get('/docs/guia')->assertStatus(200);

        // Rotas de edicao devem ser rejeitadas pelo edit_middleware (retorna 418)
        $this->get('/docs/guia/edit')->assertStatus(418);
        $this->put('/docs/guia', ['content' => 'new'])->assertStatus(418);
        $this->post('/docs/guia/collaboration')->assertStatus(418);
    }
}
