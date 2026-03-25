<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Illuminate\Support\Str;

class SyncMarkdownDocs
{
    public function __construct(
        protected $storage,
        protected $repository
    ) {}

    public function execute(): int
    {
        $files = $this->storage->all();

        $count = 0;

        foreach ($files as $file) {

            try {

                $content = $file['content'];
                $checksum = md5($content);
                $slug = strtolower(trim($file['slug']));

                $document = $this->repository->findBySlug($slug);

                if (!$document) {

                    $document = new Document(
                        id: (string) Str::uuid(),
                        slug: $slug,
                        title: ucfirst(str_replace('.', ' ', $slug))
                    );

                    $this->repository->save($document);
                }

                $this->repository->createVersion(
                    documentId: $document->id,
                    content: $content,
                    checksum: $checksum
                );

                $count++;
            } catch (\Throwable $e) {

                echo "\nERROR syncing: " . $file['slug'];
                echo "\n" . $e->getMessage() . "\n";
            }
        }

        return $count;
    }
}
