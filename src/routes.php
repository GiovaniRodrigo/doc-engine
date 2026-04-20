<?php

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationEngine\Http\DocumentationController;

Route::get('/docs', [DocumentationController::class, 'index']);
Route::get('/docs/{slug}/edit', [DocumentationController::class, 'edit']);
Route::post('/docs/{slug}/generate', [DocumentationController::class, 'generate']);
Route::put('/docs/{slug}', [DocumentationController::class, 'update']);
Route::get('/docs/{slug}', [DocumentationController::class, 'show']);
Route::post('/docs/webhooks/github', [DocumentationController::class, 'webhook']);
Route::post('/docs/{slug}/chat', [DocumentationController::class, 'chat']);
