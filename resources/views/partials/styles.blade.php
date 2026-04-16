@php
    $docsCssFiles = [
        $documentationEnginePackagePath . '/resources/css/docs/themes.css',
        $documentationEnginePackagePath . '/resources/css/docs/layout.css',
        $documentationEnginePackagePath . '/resources/css/docs/sidebar.css',
        $documentationEnginePackagePath . '/resources/css/docs/article.css',
        $documentationEnginePackagePath . '/resources/css/docs/components.css',
    ];
@endphp
<style>
@foreach ($docsCssFiles as $docsCssFile)
{!! app('files')->get($docsCssFile) !!}

@endforeach
</style>
