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
    ) {}

    public function show($slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(!$doc, 404);

        $allSlugs = $this->listDocumentSlugs->execute();

        return view('documentation-engine::show', [
            'html' => $this->renderer->render($doc->content),
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

        return redirect("/docs/{$slug}");
    }

    public function generate(Request $request, string $slug): JsonResponse
    {
        abort_unless(config('documentation-engine.ai.enabled', true), 404);

        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(!$doc, 404);

        $data = $request->validate([
            'prompt' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'provider' => ['nullable', 'string', 'in:openai,gemini'],
            'model' => ['nullable', 'string', 'max:120'],
        ]);

        try {
            $provider = $this->resolveAiProvider($data['provider'] ?? null);
        } catch (BindingResolutionException) {
            return response()->json([
                'message' => 'Nenhum provedor de IA foi configurado para a documentação.',
            ], 500);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Nao foi possivel resolver o provedor de IA.',
            ], 422);
        }

        try {
            $generated = (new GenerateWithAI($provider))->execute(
                $this->buildAiPrompt($slug, $data['prompt'], $data['content'] ?? $doc->content),
                [
                    'model' => $data['model'] ?? null,
                ]
            );
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Não foi possível gerar conteúdo com IA.',
            ], 500);
        }

        return response()->json([
            'content' => $generated,
        ]);
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
