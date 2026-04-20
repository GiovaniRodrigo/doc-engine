<?php

namespace Giovani\DocumentationEngine\Http;

use Throwable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Giovani\DocumentationEngine\Application\UseCases\ListDocumentSlugs;
use Illuminate\Contracts\Container\BindingResolutionException;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Giovani\DocumentationEngine\Application\UseCases\GenerateWithAI;
use Giovani\DocumentationEngine\Application\UseCases\UpdateDocument;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;
use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Giovani\DocumentationEngine\Application\Services\SidebarBuilder;
use Giovani\DocumentationEngine\Infrastructure\AI\DocumentationAiProviderFactory;
use Giovani\DocumentationEngine\Application\Services\BreadcrumbBuilder;
use Giovani\DocumentationEngine\Application\Services\NavigationBuilder;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;

class DocumentationController extends Controller
{
    public function __construct(
        private ShowDocument $showDocument,
        private ListDocumentSlugs $listDocumentSlugs,
        private UpdateDocument $updateDocument,
        private MarkdownRenderer $renderer,
        private SidebarBuilder $sidebarBuilder,
        private BreadcrumbBuilder $breadcrumbBuilder,
        private NavigationBuilder $navigationBuilder,
        private DocumentationAiProviderFactory $aiProviderFactory,
        private SyncMarkdownDocs $syncMarkdownDocs,
    ) {}

    public function index()
    {
        $allSlugs = $this->listDocumentSlugs->execute();

        if (empty($allSlugs)) {
            abort(404, 'Nenhuma documentação encontrada.');
        }

        // Tenta encontrar um index ou readme
        $homeSlugs = ['readme', 'index', 'home', 'introducao'];
        foreach ($homeSlugs as $home) {
            if (in_array($home, $allSlugs)) {
                return redirect("/docs/{$home}");
            }
        }

        // Se não encontrar, redireciona para o primeiro
        return redirect("/docs/" . $allSlugs[0]);
    }

    public function webhook(Request $request)
    {
        $secret = config('documentation-engine.webhook_secret');
        
        // GitHub
        if ($request->hasHeader('X-Hub-Signature-256')) {
            $signature = $request->header('X-Hub-Signature-256');
            $payload = $request->getContent();
            $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

            if (!hash_equals($hash, $signature)) {
                return response()->json(['message' => 'Invalid GitHub signature.'], 403);
            }
        } 
        // GitLab
        elseif ($request->hasHeader('X-Gitlab-Token')) {
            $token = $request->header('X-Gitlab-Token');
            if ($secret && !hash_equals($secret, $token)) {
                return response()->json(['message' => 'Invalid GitLab token.'], 403);
            }
        }

        $this->syncMarkdownDocs->execute();

        return response()->json(['message' => 'Documentation synced.']);
    }

    public function show($slug)
    {
        $slug = $this->normalizeSlug($slug);
        
        $html = cache()->remember("doc_render_{$slug}", now()->addHours(24), function () use ($slug) {
            $doc = $this->showDocument->execute($slug);
            abort_if(!$doc, 404);
            return $this->renderer->render($doc->content);
        });

        $allSlugs = $this->listDocumentSlugs->execute();

        return view('documentation-engine::show', [
            'html' => $html,
            'sidebar' => $this->buildSidebar($allSlugs),
            'breadcrumb' => $this->breadcrumbBuilder->build($slug),
            'nav' => $this->navigationBuilder->build($allSlugs, $slug),
            'slug' => $slug,
        ]);
    }

    public function edit(string $slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(!$doc, 404);

        $allSlugs = $this->listDocumentSlugs->execute();

        return view('documentation-engine::edit', [
            'slug' => $slug,
            'content' => $doc->content,
            'sidebar' => $this->buildSidebar($allSlugs),
            'breadcrumb' => $this->breadcrumbBuilder->build($slug),
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(!$doc, 404);

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $this->updateDocument->execute($slug, $data['content']);
        
        cache()->forget("doc_render_{$slug}");

        return redirect("/docs/{$slug}");
    }

    public function generate(Request $request, string $slug): JsonResponse
    {
        abort_unless(config('documentation-engine.ai.enabled', true), 404);

        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(!$doc, 404);

        $data = $request->validate([
            'prompt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'provider' => ['nullable', 'string', 'in:openai,gemini'],
            'model' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'in:general,tldr,suggest_tags'],
        ]);

        try {
            $provider = $this->resolveAiProvider($data['provider'] ?? null);
        } catch (BindingResolutionException) {
            return response()->json(['message' => 'Nenhum provedor de IA configurado.'], 500);
        }

        $type = $data['type'] ?? 'general';
        $content = $data['content'] ?? $doc->content;

        $prompt = match ($type) {
            'tldr' => "Gere um resumo curto (TL;DR) em Markdown para este documento:\n\n{$content}",
            'suggest_tags' => "Sugira ate 5 tags curtas e relevantes separadas por virgula para este documento:\n\n{$content}",
            default => $this->buildAiPrompt($slug, $data['prompt'] ?? 'Melhore este texto', $content),
        };

        try {
            $generated = (new GenerateWithAI($provider))->execute($prompt, [
                'model' => $data['model'] ?? null,
            ]);
        } catch (Throwable $exception) {
            return response()->json(['message' => 'Erro ao gerar com IA.'], 500);
        }

        return response()->json(['content' => $generated]);
    }

    public function chat(Request $request, string $slug): JsonResponse
    {
        abort_unless(config('documentation-engine.ai.enabled', true), 404);

        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(!$doc, 404);

        $data = $request->validate([
            'message' => ['required', 'string'],
            'history' => ['nullable', 'array'],
        ]);

        $provider = $this->resolveAiProvider();

        $prompt = "Voce eh um assistente de documentacao. Responda duvidas com base no conteudo abaixo:\n\n" .
            "CONTEUDO:\n{$doc->content}\n\n" .
            "PERGUNTA: {$data['message']}";

        try {
            $response = (new GenerateWithAI($provider))->execute($prompt);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro no chat.'], 500);
        }

        return response()->json(['response' => $response]);
    }

    protected function buildSidebar(array $slugs): array
    {
        return $this->sidebarBuilder->build($slugs);
    }

    protected function resolveAiProvider(?string $provider = null): AiProvider
    {
        if ($provider === null || trim($provider) === '') {
            return app(AiProvider::class);
        }

        return $this->aiProviderFactory->make($provider);
    }

    protected function buildAiPrompt(string $slug, string $instruction, string $content): string
    {
        return implode("\n\n", [
            'Voce esta editando uma documentacao em Markdown.',
            "Slug do documento: {$slug}",
            'Tarefa do usuario:',
            trim($instruction),
            'Conteudo atual do documento:',
            trim($content) !== '' ? $content : '[vazio]',
            'Responda apenas com o Markdown final, sem explicacoes extras.',
        ]);
    }

    protected function normalizeSlug(string $slug): string
    {
        return strtolower(trim($slug));
    }
}
