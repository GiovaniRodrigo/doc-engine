<?php

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationEngine\Http\DocumentationController;

Route::get('/docs/{slug}/edit', [DocumentationController::class, 'edit']);
Route::post('/docs/{slug}/generate', [DocumentationController::class, 'generate']);
Route::put('/docs/{slug}', [DocumentationController::class, 'update']);
Route::get('/docs/{slug}', [DocumentationController::class, 'show']);
