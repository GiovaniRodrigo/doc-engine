<?php

class SearchDocuments
{
    public function search(string $project, string $query): array
    {
        $files = collect(
            glob(storage_path("docs/{$project}/**/*.md"))
        );

        return $files
            ->filter(
                fn($file) =>
                str_contains(
                    strtolower(file_get_contents($file)),
                    strtolower($query)
                )
            )
            ->map(
                fn($file) =>
                str_replace(
                    storage_path("docs/{$project}/"),
                    '',
                    $file
                )
            )
            ->values()
            ->toArray();
    }
}
