<?php

namespace Giovani\DocumentationPlatformEngine\Console;

use Illuminate\Console\Command;

class TestDocumentationCommand extends Command
{
    protected $signature = 'doc-engine:test';
    protected $description = 'Test Documentation Engine';

    public function handle()
    {
        $this->info('Documentation Engine OK');
    }
}