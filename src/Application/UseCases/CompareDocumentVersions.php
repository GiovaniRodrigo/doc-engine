<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use RuntimeException;

class CompareDocumentVersions
{
    public function __construct(private DocumentRepository $repository) {}

    public function execute(string $slug, string $from, string $to): array
    {
        $document = $this->repository->findBySlug($slug);

        if (! $document) {
            throw new RuntimeException("Document not found: {$slug}");
        }

        $fromVersion = $this->repository->findVersion($document->id, $from);
        $toVersion = $this->repository->findVersion($document->id, $to);

        if (! $fromVersion || ! $toVersion) {
            throw new RuntimeException('One or more versions were not found.');
        }

        return [
            'from' => $fromVersion,
            'to' => $toVersion,
            'lines' => $this->diffLines($fromVersion->content, $toVersion->content),
        ];
    }

    private function diffLines(string $from, string $to): array
    {
        $fromLines = preg_split('/\R/', $from) ?: [];
        $toLines = preg_split('/\R/', $to) ?: [];
        $max = max(count($fromLines), count($toLines));
        $diff = [];

        for ($index = 0; $index < $max; $index++) {
            $old = $fromLines[$index] ?? null;
            $new = $toLines[$index] ?? null;

            if ($old === $new && $old !== null) {
                $diff[] = ['type' => 'same', 'content' => $old];

                continue;
            }

            if ($old !== null) {
                $diff[] = ['type' => 'removed', 'content' => $old];
            }

            if ($new !== null) {
                $diff[] = ['type' => 'added', 'content' => $new];
            }
        }

        return $diff;
    }
}
