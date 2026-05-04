@php
    $docsCssFileNames = [
        'themes.css',
        'layout.css',
        'sidebar.css',
        'article.css',
        'components.css',
    ];

    $customDocsCssPath = resource_path('css/documentation-engine/docs');
    $packageDocsCssPath = $documentationEnginePackagePath . '/resources/css/docs';

    $docsCssFiles = array_map(function ($docsCssFileName) use ($customDocsCssPath, $packageDocsCssPath) {
        $customDocsCssFile = $customDocsCssPath . '/' . $docsCssFileName;

        return app('files')->exists($customDocsCssFile)
            ? $customDocsCssFile
            : $packageDocsCssPath . '/' . $docsCssFileName;
    }, $docsCssFileNames);
@endphp
<style>
@foreach ($docsCssFiles as $docsCssFile)
{!! app('files')->get($docsCssFile) !!}

@endforeach
</style>
