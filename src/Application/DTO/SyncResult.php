<?php

namespace Giovani\DocumentationEngine\Application\DTO;

class SyncResult
{
    /**
     * @param array<int, string> $readFiles
     * @param array<int, string> $changedFiles
     * @param array<int, string> $ignoredFiles
     * @param array<int, string> $createdVersions
     * @param array<int, string> $archivedDocuments
     * @param array<int, string> $errors
     */
    public function __construct(
        public ?string $commit = null,
        public array $readFiles = [],
        public array $changedFiles = [],
        public array $ignoredFiles = [],
        public array $createdVersions = [],
        public array $archivedDocuments = [],
        public array $errors = [],
        public bool $dryRun = false,
    ) {}

    public function createdVersionsCount(): int
    {
        return count($this->createdVersions);
    }

    public function archivedDocumentsCount(): int
    {
        return count($this->archivedDocuments);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'commit' => $this->commit,
            'read_files' => $this->readFiles,
            'changed_files' => $this->changedFiles,
            'ignored_files' => $this->ignoredFiles,
            'created_versions' => $this->createdVersions,
            'archived_documents' => $this->archivedDocuments,
            'errors' => $this->errors,
            'dry_run' => $this->dryRun,
            'summary' => [
                'read_files' => count($this->readFiles),
                'changed_files' => count($this->changedFiles),
                'ignored_files' => count($this->ignoredFiles),
                'created_versions' => $this->createdVersionsCount(),
                'archived_documents' => $this->archivedDocumentsCount(),
                'errors' => count($this->errors),
            ],
        ];
    }
}
