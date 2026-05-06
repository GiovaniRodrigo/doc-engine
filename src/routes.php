<?php

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationEngine\Http\DocumentationController;

$editMiddleware = config('documentation-engine.edit_middleware', ['web']);

Route::middleware(['web'])->group(function () {
    Route::get('/docs', [DocumentationController::class, 'index']);
    Route::get('/docs/search', [DocumentationController::class, 'search']);
});

Route::middleware($editMiddleware)->group(function () {
    Route::get('/docs/{slug}/edit', [DocumentationController::class, 'edit'])->where('slug', '.*');
    Route::put('/docs/{slug}', [DocumentationController::class, 'update'])->where('slug', '.*');
    Route::get('/docs/{slug}/versions', [DocumentationController::class, 'versions'])->where('slug', '.*');
    Route::get('/docs/{slug}/versions/compare', [DocumentationController::class, 'compare'])->where('slug', '.*');
    Route::post('/docs/{slug}/versions/{version}/publish', [DocumentationController::class, 'publish'])->where('slug', '.*');
    Route::post('/docs/{slug}/generate', [DocumentationController::class, 'generate'])->where('slug', '.*');
    Route::post('/docs/{slug}/chat', [DocumentationController::class, 'chat'])->where('slug', '.*');
});

Route::middleware(['web'])->group(function () {
    Route::get('/docs/{slug}', [DocumentationController::class, 'show'])->where('slug', '.*');
});

Route::post('/docs/webhooks/github', [DocumentationController::class, 'githubWebhook']);
Route::post('/docs/webhooks/gitlab', [DocumentationController::class, 'gitlabWebhook']);
