<?php

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationEngine\Http\DocumentationController;

$editMiddleware = config('documentation-engine.edit_middleware', []);

Route::get('/docs', [DocumentationController::class, 'index']);
Route::get('/docs/search', [DocumentationController::class, 'search']);
Route::middleware($editMiddleware)->group(function () {
    Route::get('/docs/{slug}/edit', [DocumentationController::class, 'edit']);
    Route::put('/docs/{slug}', [DocumentationController::class, 'update']);
    Route::get('/docs/{slug}/versions', [DocumentationController::class, 'versions']);
    Route::get('/docs/{slug}/versions/compare', [DocumentationController::class, 'compare']);
    Route::post('/docs/{slug}/versions/{version}/publish', [DocumentationController::class, 'publish']);
});
Route::get('/docs/{slug}', [DocumentationController::class, 'show']);
Route::post('/docs/webhooks/github', [DocumentationController::class, 'githubWebhook']);
Route::post('/docs/webhooks/gitlab', [DocumentationController::class, 'gitlabWebhook']);
