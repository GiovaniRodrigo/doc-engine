<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Infrastructure\Persistence;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\Document;
use Giovani\DocumentationPlatformEngine\Documentation\Domain\Entities\DocumentVersion;
use Giovani\DocumentationPlatformEngine\Documentation\Infrastructure\Persistence\Models\DocumentModel;
use Giovani\DocumentationPlatformEngine\Documentation\Infrastructure\Persistence\Models\DocumentVersionModel;

class EloquentDocumentRepository implements DocumentRepository
{
    public function findByProjectAndPath(
        string $project,
        string $path
    ): ?Document {

        $model = DocumentModel::query()
            ->where('project', $project)
            ->where('path', $path)
            ->first();

        if (! $model) {
            return null;
        }

        return new Document(
            project: $model->project,
            path: $model->path,
            slug: $model->slug,
            title: $model->title
        );
    }

    public function save(Document $document): void
    {
        DocumentModel::updateOrCreate(
            [
                'project' => $document->project,
                'path' => $document->path,
            ],
            [
                'slug' => $document->slug,
                'title' => $document->title,
            ]
        );
    }

    public function saveVersion(DocumentVersion $version): void
    {
        $docModel = DocumentModel::query()
            ->where('project', $version->document->project)
            ->where('path', $version->document->path)
            ->firstOrFail();

        DocumentVersionModel::create([
            'document_id' => $docModel->id,
            'markdown' => $version->markdown,
            'html' => $version->html,
            'hash' => $version->hash,
            'commit_hash' => $version->commitHash,
            'state' => $version->state,
        ]);
    }

    public function findLatestVersionBySlug(
        string $project,
        string $slug
    ): ?DocumentVersion {

        $model = DocumentModel::query()
            ->where('project', $project)
            ->where('slug', $slug)
            ->first();

        if (! $model) {
            return null;
        }

        $version = $model->versions()
            ->latest()
            ->first();

        if (! $version) {
            return null;
        }

        return new DocumentVersion(
            document: new Document(
                project: $model->project,
                path: $model->path,
                slug: $model->slug,
                title: $model->title
            ),
            markdown: $version->markdown,
            hash: $version->hash,
            commitHash: $version->commit_hash,
            html: $version->html,
            state: $version->state
        );
    }
}