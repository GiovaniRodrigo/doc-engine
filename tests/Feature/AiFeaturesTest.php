<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;

class AiFeaturesTest extends TestCase
{
    /** @test */
    public function it_can_generate_a_tldr_summary()
    {
        // RF19 - TL;DR automático
        // Atualmente o caso de uso GenerateWithAI é genérico.
        // Faltaria um endpoint ou lógica específica para resumos.
        $this->markTestIncomplete('Funcionalidade de TL;DR automático não implementada.');
    }

    /** @test */
    public function it_can_suggest_smart_tags()
    {
        // RF19 - Tags inteligentes
        $this->markTestIncomplete('Funcionalidade de tags inteligentes via IA não implementada.');
    }

    /** @test */
    public function it_provides_a_chat_interface_context()
    {
        // RF19 - Interface de Chat
        $this->markTestIncomplete('Interface de chat para dúvidas sobre documentação não implementada.');
    }
}
