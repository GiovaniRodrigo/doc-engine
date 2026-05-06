<?php

namespace Giovani\DocumentationEngine\Infrastructure\Persistence;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Entities\DocumentVersion;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentDocumentRepository implements DocumentRepository
{
    public function save(Document $document): void
    {
        DB::table('documents')->insert([
            'id' => $document->id,
            'slug' => $document->slug,
            'title' => $document->title,
            'tags' => json_encode($document->tags),
            'state' => $document->state,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function saveVersion(DocumentVersion $version): void
    {
        DB::table('document_versions')->insert([
            'document_id' => $version->documentId,
            'version' => $version->version,
            'content' => $version->content,
            'checksum' => $version->checksum,
            'state' => $version->state,
            'git_commit' => $version->gitCommit,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function findBySlug(string $slug): ?Document
    {
        $row = DB::table('documents')->where('slug', $slug)->first();

        if (! $row) {
            return null;
        }

        return new Document(
            $row->id,
            $row->slug,
            $row->title,
            json_decode($row->tags, true),
            $row->state ?? 'active'
        );
    }

    public function latestVersion(string $documentId): ?DocumentVersion
    {
        $row = DB::table('document_versions')
            ->where('document_id', $documentId)
            ->latest('id')
            ->first();

        return $row ? $this->hydrateVersion($row) : null;
    }

    public function latestPublishedVersion(string $documentId): ?DocumentVersion
    {
        $row = DB::table('document_versions')
            ->where('document_id', $documentId)
            ->where('state', 'published')
            ->latest('id')
            ->first();

        return $row ? $this->hydrateVersion($row) : null;
    }

    public function findVersion(string $documentId, string $version): ?DocumentVersion
    {
        $row = DB::table('document_versions')
            ->where('document_id', $documentId)
            ->where('version', $version)
            ->first();

        return $row ? $this->hydrateVersion($row) : null;
    }

    public function versions(string $documentId): array
    {
        return DB::table('document_versions')
            ->where('document_id', $documentId)
            ->latest('id')
            ->get()
            ->map(fn ($row) => $this->hydrateVersion($row))
            ->all();
    }

    public function publishVersion(string $documentId, string $version): void
    {
        DB::transaction(function () use ($documentId, $version) {
            DB::table('document_versions')
                ->where('document_id', $documentId)
                ->where('state', 'published')
                ->update([
                    'state' => 'archived',
                    'updated_at' => now(),
                ]);

            DB::table('document_versions')
                ->where('document_id', $documentId)
                ->where('version', $version)
                ->update([
                    'state' => 'published',
                    'updated_at' => now(),
                ]);
        });
    }

    protected function hydrateVersion(object $row): DocumentVersion
    {
        return new DocumentVersion(
            $row->document_id,
            $row->version,
            $row->content,
            $row->checksum,
            $row->state ?? 'published',
            $row->git_commit,
            isset($row->created_at) ? (string) $row->created_at : null
        );
    }

    public function createVersion(
        string $documentId,
        string $content,
        string $checksum,
        ?string $gitCommit = null,
        string $state = 'published'
    ): DocumentVersion {
        $version = (string) Str::uuid();

        DB::table('document_versions')->insert([
            'document_id' => $documentId,
            'version' => $version,
            'content' => $content,
            'checksum' => $checksum,
            'state' => $state,
            'git_commit' => $gitCommit,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return new DocumentVersion(
            documentId: $documentId,
            version: $version,
            content: $content,
            checksum: $checksum,
            state: $state,
            gitCommit: $gitCommit,
            createdAt: (string) now(),
        );
    }

    public function allSlugs(): array
    {
        return $this->allActiveSlugs();
    }

    public function allActiveSlugs(?string $project = null): array
    {
        $query = DB::table('documents')
            ->where('state', 'active');

        if ($project !== null && $project !== '') {
            $query->where(function ($query) use ($project) {
                $query->where('slug', $project)
                    ->orWhere('slug', 'like', "{$project}.%");
            });
        }

        return $query
            ->orderBy('slug')
            ->pluck('slug')
            ->toArray();
    }

    public function search(string $query, int $limit = 20): array
    {
        $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $query).'%';

        return DB::table('documents')
            ->join('document_versions', function ($join) {
                $join->on('document_versions.document_id', '=', 'documents.id')
                    ->where('document_versions.state', 'published')
                    ->whereRaw("document_versions.id = (
                        select max(latest_document_versions.id)
                        from document_versions as latest_document_versions
                        where latest_document_versions.document_id = documents.id
                        and latest_document_versions.state = 'published'
                    )");
            })
            ->where('documents.state', 'active')
            ->where(function ($builder) use ($term) {
                $builder->where('documents.title', 'like', $term)
                    ->orWhere('documents.slug', 'like', $term)
                    ->orWhere('document_versions.content', 'like', $term);
            })
            ->orderBy('documents.slug')
            ->limit($limit)
            ->get([
                'documents.id',
                'documents.slug',
                'documents.title',
                'documents.tags',
                'document_versions.content',
                'document_versions.version',
                'document_versions.checksum',
                'document_versions.git_commit',
            ])
            ->map(fn ($row) => [
                'id' => $row->id,
                'slug' => $row->slug,
                'title' => $row->title,
                'tags' => json_decode($row->tags, true) ?: [],
                'content' => $row->content,
                'version' => $row->version,
                'checksum' => $row->checksum,
                'git_commit' => $row->git_commit,
            ])
            ->toArray();
    }

    public function archiveBySlug(string $slug): void
    {
        DB::table('documents')
            ->where('slug', $slug)
            ->update([
                'state' => 'archived',
                'updated_at' => now(),
            ]);
    }

    public function activateBySlug(string $slug): void
    {
        DB::table('documents')
            ->where('slug', $slug)
            ->update([
                'state' => 'active',
                'updated_at' => now(),
            ]);
    }
}
