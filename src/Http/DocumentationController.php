<?php

namespace Giovani\DocumentationEngine\Http;

use Giovani\DocumentationEngine\Application\Services\BreadcrumbBuilder;
use Giovani\DocumentationEngine\Application\Services\NavigationBuilder;
use Giovani\DocumentationEngine\Application\Services\SidebarBuilder;
use Giovani\DocumentationEngine\Application\Services\TableOfContentsBuilder;
use Giovani\DocumentationEngine\Application\Services\WebhookProcessor;
use Giovani\DocumentationEngine\Application\UseCases\CompareDocumentVersions;
use Giovani\DocumentationEngine\Application\UseCases\GenerateWithAI;
use Giovani\DocumentationEngine\Application\UseCases\ListDocumentSlugs;
use Giovani\DocumentationEngine\Application\UseCases\ListDocumentVersions;
use Giovani\DocumentationEngine\Application\UseCases\PublishDocumentVersion;
use Giovani\DocumentationEngine\Application\UseCases\SearchDocuments;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Giovani\DocumentationEngine\Application\UseCases\UpdateDocument;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;
use Giovani\DocumentationEngine\Infrastructure\AI\DocumentationAiProviderFactory;
use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;

class DocumentationController extends Controller
{
    public function __construct(
        private ShowDocument $showDocument,
        private ListDocumentSlugs $listDocumentSlugs,
        private ListDocumentVersions $listDocumentVersions,
        private PublishDocumentVersion $publishDocumentVersion,
        private CompareDocumentVersions $compareDocumentVersions,
        private SearchDocuments $searchDocuments,
        private UpdateDocument $updateDocument,
        private MarkdownRenderer $renderer,
        private SidebarBuilder $sidebarBuilder,
        private BreadcrumbBuilder $breadcrumbBuilder,
        private NavigationBuilder $navigationBuilder,
        private TableOfContentsBuilder $tableOfContentsBuilder,
        private DocumentationAiProviderFactory $aiProviderFactory,
        private WebhookProcessor $webhookProcessor,
        private DocumentRepository $repository,
    ) {}

    public function index()
    {
        $request = request();
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (empty($lang)) {
                session()->forget('docs_language');
            } else {
                session(['docs_language' => strtolower($lang)]);
            }
        }

        $selectedLanguage = session('docs_language');
        $allSlugs = $this->listDocumentSlugs->execute();

        if (empty($allSlugs)) {
            return response()->view(
                'documentation-engine::empty',
                $this->viewData($allSlugs),
                200
            );
        }

        // Recupera detalhes de todos os documentos ativos para exibir no catálogo
        $documents = [];
        $totalTags = [];
        foreach ($allSlugs as $slug) {
            $parts = explode('.', $slug);
            $first = $parts[0];
            $isLang = preg_match('/^[a-z]{2}(?:[._-][a-z]{2})?$/i', $first);

            if ($isLang) {
                if ($selectedLanguage && strtolower($first) !== strtolower($selectedLanguage)) {
                    continue; // Pula documentos de outros idiomas
                }
            }

            $docData = $this->showDocument->execute($slug);
            if ($docData) {
                $excerpt = $this->getExcerpt($docData->content);
                $documents[] = [
                    'id' => $docData->id,
                    'slug' => $docData->slug,
                    'title' => $docData->title,
                    'excerpt' => $excerpt,
                    'tags' => $docData->tags,
                    'version' => $docData->version,
                    'createdAt' => $docData->createdAt,
                ];
                foreach ($docData->tags as $tag) {
                    $totalTags[$tag] = ($totalTags[$tag] ?? 0) + 1;
                }
            }
        }

        // Ordena tags mais populares
        arsort($totalTags);
        $popularTags = array_slice(array_keys($totalTags), 0, 8);

        return response()->view(
            'documentation-engine::catalog',
            $this->viewData($allSlugs, [
                'documents' => $documents,
                'popularTags' => $popularTags,
                'stats' => [
                    'total_docs' => count($documents),
                    'total_tags' => count($totalTags),
                    'latest_update' => !empty($documents) ? ($documents[0]['createdAt'] ?? null) : null,
                ]
            ]),
            200
        );
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $allSlugs = $this->listDocumentSlugs->execute();

        return view('documentation-engine::search', $this->viewData($allSlugs, [
            'query' => $query,
            'results' => $this->searchDocuments->execute($query),
        ]));
    }

    public function githubWebhook(Request $request): JsonResponse
    {
        $result = $this->webhookProcessor->processGithub($request);

        return response()->json($result['payload'], $result['status']);
    }

    public function gitlabWebhook(Request $request): JsonResponse
    {
        $result = $this->webhookProcessor->processGitlab($request);

        return response()->json($result['payload'], $result['status']);
    }

    public function show($slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        if (! $doc) {
            return $this->notFound($slug);
        }

        // Detecção de idioma do documento atual
        $parts = explode('.', $slug);
        $first = $parts[0];
        $isLang = preg_match('/^[a-z]{2}(?:[._-][a-z]{2})?$/i', $first);

        if ($isLang) {
            session(['docs_language' => strtolower($first)]);
        }

        $html = cache()->remember("doc_render_{$slug}", now()->addHours(24), function () use ($doc) {
            return $this->renderer->render($doc->content);
        });

        $allSlugs = $this->listDocumentSlugs->execute();

        // Mapear traduções alternativas
        $translations = [];
        if ($isLang && count($parts) > 1) {
            $baseSlug = implode('.', array_slice($parts, 1));
            foreach ($allSlugs as $otherSlug) {
                $otherParts = explode('.', $otherSlug);
                if (count($otherParts) > 1) {
                    $otherFirst = $otherParts[0];
                    $otherIsLang = preg_match('/^[a-z]{2}(?:[._-][a-z]{2})?$/i', $otherFirst);

                    if ($otherIsLang) {
                        $otherBase = implode('.', array_slice($otherParts, 1));
                        if ($otherBase === $baseSlug && strtolower($otherFirst) !== strtolower($first)) {
                            $translations[strtolower($otherFirst)] = url('/docs/' . $otherSlug);
                        }
                    }
                }
            }
        }

        return view('documentation-engine::show', $this->viewData($allSlugs, [
            'document' => $doc,
            'html' => $html,
            'breadcrumb' => $this->breadcrumbBuilder->build($slug),
            'nav' => $this->navigationBuilder->build($allSlugs, $slug),
            'slug' => $slug,
            'toc' => $this->tableOfContentsBuilder->build($doc->content),
            'translations' => $translations,
        ]));
    }

    public function edit(string $slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->repository->findBySlug($slug);

        if (! $doc) {
            return $this->notFound($slug);
        }

        $allSlugs = $this->listDocumentSlugs->execute();
        $versions = $this->listDocumentVersions->execute($slug);

        $latestPublished = $this->repository->latestPublishedVersion($doc->id);
        $editableContent = $versions[0]->content ?? $latestPublished->content ?? '';

        return view('documentation-engine::edit', $this->viewData($allSlugs, [
            'document' => $doc,
            'slug' => $slug,
            'content' => $editableContent,
            'versions' => $versions,
            'breadcrumb' => $this->breadcrumbBuilder->build($slug),
        ]));
    }

    public function update(Request $request, string $slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->repository->findBySlug($slug);

        if (! $doc) {
            return $this->notFound($slug);
        }

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $version = $this->updateDocument->execute($slug, $data['content']);

        return redirect("/docs/{$slug}/edit")
            ->with('documentation_engine_status', "Rascunho salvo: {$version}");
    }

    public function versions(string $slug)
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->repository->findBySlug($slug);

        if (! $doc) {
            return $this->notFound($slug);
        }

        $allSlugs = $this->listDocumentSlugs->execute();

        return view('documentation-engine::versions', $this->viewData($allSlugs, [
            'document' => $doc,
            'slug' => $slug,
            'versions' => $this->listDocumentVersions->execute($slug),
            'breadcrumb' => $this->breadcrumbBuilder->build($slug),
        ]));
    }

    public function publish(string $slug, string $version)
    {
        $slug = $this->normalizeSlug($slug);

        try {
            $this->publishDocumentVersion->execute($slug, $version);
        } catch (\RuntimeException) {
            return $this->notFound($slug);
        }

        return redirect("/docs/{$slug}/versions")
            ->with('documentation_engine_status', "Versão publicada: {$version}");
    }

    public function compare(Request $request, string $slug)
    {
        $slug = $this->normalizeSlug($slug);
        $versions = $this->listDocumentVersions->execute($slug);

        if (count($versions) < 2) {
            return redirect("/docs/{$slug}/versions")
                ->with('documentation_engine_status', 'São necessárias pelo menos duas versões para comparar.');
        }

        $from = (string) $request->query('from', $versions[1]->version);
        $to = (string) $request->query('to', $versions[0]->version);

        try {
            $comparison = $this->compareDocumentVersions->execute($slug, $from, $to);
        } catch (\RuntimeException) {
            return $this->notFound($slug);
        }

        $allSlugs = $this->listDocumentSlugs->execute();

        return view('documentation-engine::compare', $this->viewData($allSlugs, [
            'slug' => $slug,
            'versions' => $versions,
            'comparison' => $comparison,
            'breadcrumb' => $this->breadcrumbBuilder->build($slug),
        ]));
    }

    public function generate(Request $request, string $slug): JsonResponse
    {
        abort_unless(config('documentation-engine.ai.enabled', true), 404);

        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(! $doc, 404);

        $data = $request->validate([
            'prompt' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string', 'max:30000'],
            'provider' => ['nullable', 'string', 'in:openai,gemini'],
            'model' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'in:general,tldr,suggest_tags'],
        ]);

        $selectedProvider = $data['provider'] ?? config('documentation-engine.ai.driver', 'openai');

        try {
            $provider = $this->resolveAiProvider($data['provider'] ?? null);
            $provider->validateConfiguration();
        } catch (BindingResolutionException) {
            return response()->json(['message' => 'Nenhum provedor de IA configurado.'], 500);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        $type = $data['type'] ?? 'general';
        $content = $data['content'] ?? $doc->content;

        $prompt = match ($type) {
            'tldr' => "Voce eh um editor tecnico. Gere um resumo curto e impactante (TL;DR) em Markdown para o documento abaixo. Use bullet points se necessario.\n\nCONTEUDO:\n{$content}",
            'suggest_tags' => "Voce eh um sistema de indexacao. Analise o documento abaixo e sugira ate 5 tags tecnicas, curtas e relevantes. Retorne apenas as tags separadas por virgula, sem explicacoes.\n\nCONTEUDO:\n{$content}",
            default => $this->buildAiPrompt($slug, $data['prompt'] ?? 'Melhore a clareza e o tom tecnico deste texto', $content),
        };

        try {
            $generated = (new GenerateWithAI($provider))->execute($prompt, [
                'model' => $data['model'] ?? null,
            ]);

            Log::info('AI documentation content generated.', [
                'slug' => $slug,
                'provider' => $selectedProvider,
                'type' => $type,
                'input_size' => strlen($content),
                'output_size' => strlen($generated),
            ]);
        } catch (Throwable $exception) {
            Log::error('AI documentation generation failed.', [
                'slug' => $slug,
                'provider' => $selectedProvider,
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Erro ao gerar com IA: '.$exception->getMessage()], 500);
        }

        return response()->json(['content' => $generated]);
    }

    public function chat(Request $request, string $slug): JsonResponse
    {
        abort_unless(config('documentation-engine.ai.enabled', true), 404);

        $slug = $this->normalizeSlug($slug);
        $doc = $this->showDocument->execute($slug);

        abort_if(! $doc, 404);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array'],
        ]);

        $selectedProvider = config('documentation-engine.ai.driver', 'openai');

        try {
            $provider = $this->resolveAiProvider();
            $provider->validateConfiguration();
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        $prompt = "Voce eh um assistente especializado na documentacao deste projeto. Responda de forma clara e concisa com base APENAS no conteudo fornecido abaixo. Se a resposta nao estiver no texto, diga que nao sabe.\n\n".
            "CONTEUDO:\n{$doc->content}\n\n".
            "PERGUNTA: {$data['message']}";

        try {
            $response = (new GenerateWithAI($provider))->execute($prompt);

            Log::info('AI documentation chat response generated.', [
                'slug' => $slug,
                'provider' => $selectedProvider,
                'question_size' => strlen($data['message']),
                'response_size' => strlen($response),
            ]);
        } catch (Throwable $e) {
            Log::error('AI documentation chat failed.', [
                'slug' => $slug,
                'provider' => $selectedProvider,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Erro no chat: '.$e->getMessage()], 500);
        }

        return response()->json(['response' => $response]);
    }

    protected function buildSidebar(array $slugs, ?string $selectedLanguage = null): array
    {
        return $this->sidebarBuilder->build($slugs, $selectedLanguage);
    }

    protected function viewData(array $allSlugs, array $data = []): array
    {
        $project = $data['project'] ?? null;
        $projectKey = $project ? strtolower($project) : 'all';
        $selectedLanguage = session('docs_language');
        $cacheKey = $selectedLanguage ? "doc_sidebar_{$projectKey}_{$selectedLanguage}" : "doc_sidebar_{$projectKey}";

        $sidebarData = cache()->remember($cacheKey, now()->addHours(24), function () use ($allSlugs, $selectedLanguage) {
            $nodes = $this->buildSidebar($allSlugs, $selectedLanguage);
            return $this->sidebarToArray($nodes);
        });

        $sidebar = $this->arrayToSidebar($sidebarData);

        return array_merge([
            'allSlugs' => $allSlugs,
            'sidebar' => $sidebar,
            'breadcrumb' => [],
            'nav' => ['prev' => null, 'next' => null],
            'slug' => null,
            'document' => null,
            'toc' => [],
            'selectedLanguage' => $selectedLanguage,
            'availableLanguages' => $this->getAvailableLanguages($allSlugs),
            'translations' => [],
        ], $data);
    }

    protected function getAvailableLanguages(array $allSlugs): array
    {
        $languages = [];
        $mappings = [
            'pt' => 'Português',
            'pt-br' => 'Português (Brasil)',
            'pt_br' => 'Português (Brasil)',
            'en' => 'English',
            'en-us' => 'English (US)',
            'en_us' => 'English (US)',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'it' => 'Italiano',
        ];

        foreach ($allSlugs as $slug) {
            $parts = explode('.', $slug);
            if (count($parts) > 1) {
                $first = $parts[0];
                if (preg_match('/^[a-z]{2}(?:[._-][a-z]{2})?$/i', $first)) {
                    $code = strtolower($first);
                    $languages[$code] = $mappings[$code] ?? strtoupper($code);
                }
            }
        }

        asort($languages);

        return $languages;
    }

    private function sidebarToArray(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $result[] = [
                'title' => $node->title,
                'slug' => $node->slug,
                'children' => $this->sidebarToArray($node->children),
            ];
        }
        return $result;
    }

    private function arrayToSidebar(array $data): array
    {
        $nodes = [];
        foreach ($data as $item) {
            $nodes[] = new \Giovani\DocumentationEngine\Application\DTO\DocNode(
                title: $item['title'],
                slug: $item['slug'],
                children: $this->arrayToSidebar($item['children'])
            );
        }
        return $nodes;
    }

    protected function notFound(string $slug)
    {
        $allSlugs = $this->listDocumentSlugs->execute();

        return response()->view(
            'documentation-engine::not-found',
            $this->viewData($allSlugs, [
                'slug' => $slug,
            ]),
            404
        );
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

    public function collaboration(Request $request, string $slug): JsonResponse
    {
        $slug = $this->normalizeSlug($slug);
        $doc = $this->repository->findBySlug($slug);

        if (! $doc) {
            return response()->json(['message' => 'Documento nao encontrado.'], 404);
        }

        $userId = auth()->check() ? (string) auth()->id() : (session()->isStarted() ? session()->getId() : 'session_' . uniqid());
        $userName = auth()->check()
            ? (auth()->user()->name ?? auth()->user()->email ?? 'Usuario ' . auth()->id())
            : 'Editor #' . substr(md5($userId), 0, 5);

        $cacheKey = "documentation-engine:collaboration:{$slug}";
        $activeUsers = (array) \Illuminate\Support\Facades\Cache::get($cacheKey, []);

        $now = time();
        $activeUsers = array_filter($activeUsers, function ($u) use ($now) {
            return is_array($u) && isset($u['last_seen']) && ($now - $u['last_seen']) <= 15;
        });

        $activeUsers[$userId] = [
            'id' => $userId,
            'name' => $userName,
            'last_seen' => $now,
        ];

        \Illuminate\Support\Facades\Cache::put($cacheKey, $activeUsers, 60);

        $responseUsers = [];
        $hasConflict = false;
        foreach ($activeUsers as $id => $user) {
            $isCurrent = ($id === $userId);
            if (! $isCurrent) {
                $hasConflict = true;
            }
            $responseUsers[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'is_current' => $isCurrent,
                'last_seen' => $user['last_seen'],
            ];
        }

        return response()->json([
            'users' => $responseUsers,
            'has_conflict' => $hasConflict,
        ]);
    }

    protected function getExcerpt(string $content, int $length = 150): string
    {
        // Remove markdown tags, headers, links, and bold formatting
        $text = preg_replace('/[#*`_\-\[\]\(\)]+/', ' ', $content);
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        if (mb_strlen($text) > $length) {
            return mb_substr($text, 0, $length) . '...';
        }
        return $text;
    }

    protected function normalizeSlug(string $slug): string
    {
        // Prevent path traversal and keep only alphanumeric, dots and dashes
        $slug = str_replace(['..', './', '..\\', '.\\'], '', $slug);

        $slug = str_replace(['/', '\\'], '.', $slug);

        return strtolower(trim($slug));
    }
}
