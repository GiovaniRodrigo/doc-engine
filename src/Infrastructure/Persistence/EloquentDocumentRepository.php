<?php

namespace Giovani\DocumentationEngine\Infrastructure\Persistence;

use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Entities\DocumentVersion;
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
            'updated_at' => now()
        ]);
    }

    public function findBySlug(string $slug): ?Document
    {
        $row = DB::table('documents')->where('slug', $slug)->first();

        if (!$row) return null;

        return new Document(
            $row->id,
            $row->slug,
            $row->title,
            json_decode($row->tags, true)
        );
    }

    public function latestVersion(string $documentId): ?DocumentVersion
    {
        $row = DB::table('document_versions')
            ->where('document_id', $documentId)
            ->latest('id')
            ->first();

        if (!$row) return null;

        return new DocumentVersion(
            $row->document_id,
            $row->version,
            $row->content,
            $row->checksum,
            $row->state ?? 'published',
            $row->git_commit
        );
    }

    public function createVersion(
        string $documentId,
        string $content,
        string $checksum,
        ?string $gitCommit = null
    ): void {

        DB::table('document_versions')->insert([
            'document_id' => $documentId,
            'version' => (string) Str::uuid(),
            'content' => $content,
            'checksum' => $checksum,
            'state' => 'published',
            'git_commit' => $gitCommit,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function allSlugs(): array
    {
        return DB::table('documents')
            ->orderBy('slug')
            ->pluck('slug')
            ->toArray();
    }
}
