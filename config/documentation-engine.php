<?php

use Giovani\DocumentationEngine\Infrastructure\AI\DocumentationAiProviderFactory;

return [
    'docs_path' => env('DOC_ENGINE_PATH', 'docs'),

    'layout' => env(
        'DOC_ENGINE_LAYOUT',
        'documentation-engine::layouts.default'
    ),

    'css' => env(
        'DOC_ENGINE_CSS',
        null
    ),

    'webhook_secret' => env('DOC_ENGINE_WEBHOOK_SECRET'),

    'webhook_branch' => env('DOC_ENGINE_WEBHOOK_BRANCH', 'main'),

    'edit_middleware' => array_filter(
        array_map('trim', explode(',', (string) env('DOC_ENGINE_EDIT_MIDDLEWARE', 'web')))
    ),

    'ai' => [
        'enabled' => env(
            'DOC_ENGINE_AI_ENABLED',
            (bool) env('OPENAI_API_KEY') || (bool) env('GEMINI_API_KEY')
        ),
        'provider' => static fn () => app(DocumentationAiProviderFactory::class)->make(),
        'driver' => env('DOCUMENTATION_AI_PROVIDER', 'openai'),
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-5-mini'),
            'timeout' => env('OPENAI_TIMEOUT', 30),
            'retry_times' => env('OPENAI_RETRY_TIMES', 1),
            'max_output_tokens' => env('OPENAI_MAX_OUTPUT_TOKENS', 4000),
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
            'timeout' => env('GEMINI_TIMEOUT', 30),
            'retry_times' => env('GEMINI_RETRY_TIMES', 1),
            'max_output_tokens' => env('GEMINI_MAX_OUTPUT_TOKENS', 4000),
        ],
    ],

];
