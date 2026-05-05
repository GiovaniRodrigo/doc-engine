<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Illuminate\Support\Str;

class SearchDocuments
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $query, int $limit = 20): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        return array_map(function (array $document) use ($query) {
            return [
                'slug' => $document['slug'],
                'title' => $document['title'],
                'excerpt' => $this->excerpt($document['content'], $query),
            ];
        }, $this->repository->search($query, $limit));
    }

    private function excerpt(string $content, string $query): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');

        if ($plain === '') {
            return '';
        }

        $position = mb_stripos($plain, $query);
        $start = $position === false ? 0 : max(0, $position - 70);

        return Str::limit(mb_substr($plain, $start), 180);
    }
}
