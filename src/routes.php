<?php

use Giovani\DocumentationEngine\Http\DocumentationController;
use Illuminate\Support\Facades\Route;

$middleware = static function (array|string ...$middlewareGroups): array {
    $middleware = [];

    foreach ($middlewareGroups as $middlewareGroup) {
        $middleware = array_merge($middleware, (array) $middlewareGroup);
    }

    return array_values(array_unique(array_filter($middleware)));
};

$docsMiddleware = $middleware(config('documentation-engine.middleware', ['web']));
$editMiddleware = config('documentation-engine.edit_middleware', ['web']);

Route::middleware($docsMiddleware)->group(function () {
    Route::get('/docs', [DocumentationController::class, 'index']);
    Route::get('/docs/search', [DocumentationController::class, 'search']);
});

Route::middleware($middleware($docsMiddleware, $editMiddleware))->group(function () {
    Route::get('/docs/{slug}/edit', [DocumentationController::class, 'edit'])->where('slug', '.*');
    Route::put('/docs/{slug}', [DocumentationController::class, 'update'])->where('slug', '.*');
    Route::get('/docs/{slug}/versions', [DocumentationController::class, 'versions'])->where('slug', '.*');
    Route::get('/docs/{slug}/versions/compare', [DocumentationController::class, 'compare'])->where('slug', '.*');
    Route::post('/docs/{slug}/versions/{version}/publish', [DocumentationController::class, 'publish'])->where('slug', '.*');
    Route::post('/docs/{slug}/generate', [DocumentationController::class, 'generate'])->where('slug', '.*');
    Route::post('/docs/{slug}/chat', [DocumentationController::class, 'chat'])->where('slug', '.*');
});

Route::middleware($docsMiddleware)->group(function () {
    Route::get('/docs/{slug}', [DocumentationController::class, 'show'])->where('slug', '.*');
});

Route::post('/docs/webhooks/github', [DocumentationController::class, 'githubWebhook']);
Route::post('/docs/webhooks/gitlab', [DocumentationController::class, 'gitlabWebhook']);
