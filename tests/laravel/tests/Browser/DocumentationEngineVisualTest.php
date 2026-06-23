<?php

use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    // Garantir que o documento de teste 'getting-started' exista no banco de dados
    $doc = DB::table('documents')->where('slug', 'getting-started')->first();
    
    if (!$doc) {
        $docId = (string) Str::uuid();
        DB::table('documents')->insert([
            'id' => $docId,
            'slug' => 'getting-started',
            'title' => 'Getting Started',
            'tags' => json_encode(['onboarding', 'guide']),
            'state' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $doc = DB::table('documents')->where('slug', 'getting-started')->first();
    }

    // Certificar que há pelo menos duas versões registradas para testar a comparação
    $versionsCount = DB::table('document_versions')->where('document_id', $doc->id)->count();
    
    if ($versionsCount < 2) {
        DB::table('document_versions')->insert([
            'document_id' => $doc->id,
            'version' => 'v1-published',
            'content' => "# Getting Started\n\nDocumentation Engine turns your Markdown files into a versioned, searchable documentation site.\n\n## Installation\n\n```bash\ncomposer require giovani/documentation-engine\n```\n\n## Key Features\n\n- Git versioning — every sync is tied to a commit hash.\n- Editorial workflow — save drafts, compare versions, publish explicitly.\n- Full-text search — search by title, slug, and content.\n- AI assistance — generate summaries, suggest tags, chat with documents.",
            'checksum' => md5("v1 content"),
            'state' => 'published',
            'git_commit' => 'be1d375c3e21a9873185907207a90ce482c503e3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('document_versions')->insert([
            'document_id' => $doc->id,
            'version' => 'v2-draft',
            'content' => "# Getting Started\n\nDocumentation Engine turns your Markdown files into a versioned, searchable documentation site — with an editorial workflow, Git-tracked history, and optional AI assistance.\n\n## Installation\n\n```bash\ncomposer require giovani/documentation-engine\n```\n\n## Key Features\n\n- Git versioning — every sync is tied to a commit hash.\n- Editorial workflow — save drafts, compare versions, publish explicitly.\n- Full-text search — search by title, slug, and content.\n- AI assistance — generate summaries, suggest tags, chat with documents.\n- Webhooks — auto-sync on GitHub or GitLab push events.",
            'checksum' => md5("v2 content"),
            'state' => 'draft',
            'git_commit' => 'a548a81697da9362b56b9f397ea3ed583df0eef8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Limpar tabela de usuários para evitar conflitos de email único e IDs
    DB::table('users')->delete();

    // Criar usuários de teste
    DB::table('users')->insert([
        [
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => 2,
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);

    // Limpar o cache de colaboração para evitar poluição
    Cache::forget('documentation-engine:collaboration:getting-started');
});

/**
 * Auxiliar para capturar capturas de tela em resoluções Desktop, Tablet e Mobile.
 */
function captureResolutions(Browser $browser, string $testName): void
{
    $resolutions = [
        'desktop' => [1920, 1080],
        'tablet' => [768, 1024],
        'mobile' => [375, 667],
    ];

    foreach ($resolutions as $resName => $dimensions) {
        $browser->resize($dimensions[0], $dimensions[1]);
        $browser->pause(300);
        $browser->script("document.dispatchEvent(new MouseEvent('click', {bubbles:true}))");
        $browser->pause(100);
        $browser->screenshot("{$resName}/{$testName}");
    }
}

test('catalog index page rendering', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/docs')
            ->waitForText('Explore technical manuals')
            ->assertSee('Getting-started')
            ->pause(300);

        captureResolutions($browser, 'catalog_index');
    });
});

test('document view page and ai chat widget', function () {
    $this->browse(function (Browser $browser) {
        // 1. Visita a página de visualização do documento
        $browser->visit('/docs/getting-started')
            ->waitForText('Documentation Engine turns')
            ->assertSee('Getting Started');

        captureResolutions($browser, 'document_show');

        // 2. Abre a barra lateral do Chat de IA
        $browser->click('#docs-chat-toggle')
            ->waitFor('#docs-chat-card')
            ->pause(300); // Aguarda transição visual do CSS

        captureResolutions($browser, 'document_show_ai_chat');
    });
});

test('search functionality and results view', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/docs/search?q=started')
            ->waitForText('Results for')
            ->assertSee('getting-started');

        captureResolutions($browser, 'search_results');
    });
});

test('document editing view and ai generate panel', function () {
    $this->browse(function (Browser $browser) {
        $admin = \App\Models\User::find(1);
        $browser->loginAs($admin)
            ->visit('/docs/getting-started/edit')
            ->waitForText('Edit document')
            ->click('h1')
            ->pause(300);

        captureResolutions($browser, 'document_edit');
    });
});

test('document versions history view', function () {
    $this->browse(function (Browser $browser) {
        // Logar como Admin e visitar o histórico de versões
        $admin = \App\Models\User::find(1);
        $browser->loginAs($admin)
            ->visit('/docs/getting-started/versions')
            ->waitForText('Version history')
            ->assertSee('v1-published')
            ->assertSee('v2-draft');

        captureResolutions($browser, 'document_versions_list');
    });
});

test('document versions compare view', function () {
    $this->browse(function (Browser $browser) {
        // Logar como Admin e visitar a página de comparação de duas versões específicas
        $admin = \App\Models\User::find(1);
        $browser->loginAs($admin)
            ->visit('/docs/getting-started/versions/compare?from=v1-published&to=v2-draft')
            ->waitForText('Compare versions')
            ->pause(300)
            ->script("window.scrollTo(0, document.body.scrollHeight / 3)");

        captureResolutions($browser, 'document_versions_compare');
    });
});

test('access control guest and user constraints', function () {
    $this->browse(function (Browser $browser) {
        // 1. Convidado (Guest) é redirecionado para /login
        $browser->logout()
            ->visit('/docs/getting-started/edit')
            ->assertPathIs('/login');

        captureResolutions($browser, 'access_control_guest_redirect');

        // 2. Regular user (user role) receives 403 Forbidden
        $user = \App\Models\User::find(2);
        $browser->loginAs($user)
            ->visit('/docs/getting-started/edit')
            ->assertSee('You do not have permission');

        captureResolutions($browser, 'access_control_forbidden');

        // 3. Admin acessa com sucesso
        $admin = \App\Models\User::find(1);
        $browser->loginAs($admin)
            ->visit('/docs/getting-started/edit')
            ->waitForText('Edit document')
            ->pause(500);

        captureResolutions($browser, 'access_control_admin_success');
    });
});

test('real-time collaborative editing conflict warning', function () {
    // Colocar um colaborador ativo no cache
    Cache::put('documentation-engine:collaboration:getting-started', [
        'fake_user_id' => [
            'id' => 'fake_user_id',
            'name' => 'John Doe',
            'last_seen' => time(),
        ]
    ], 60);

    $this->browse(function (Browser $browser) {
        // Logar como Admin, acessar a tela de edição
        $admin = \App\Models\User::find(1);
        $browser->loginAs($admin)
            ->visit('/docs/getting-started/edit')
            // Esperar que o chip do outro colaborador apareça via JS
            ->waitFor('#collaboration-users-list .docs-user-chip')
            ->assertSee('John Doe')
            ->assertVisible('#docs-collaboration-conflict-alert')
            ->assertSee('Warning: Another user is editing this document right now');

        captureResolutions($browser, 'document_edit_collaboration');
    });
});
