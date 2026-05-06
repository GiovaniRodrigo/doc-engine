<?php

namespace Giovani\DocumentationEngine\Application\Services;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookProcessor
{
    public function __construct(
        private SyncMarkdownDocs $syncMarkdownDocs,
    ) {}

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function processGithub(Request $request): array
    {
        $secret = (string) config('documentation-engine.webhook_secret', '');

        if ($secret !== '' && ! $this->hasValidGithubSignature($request, $secret)) {
            Log::warning('Documentation webhook rejected.', [
                'provider' => 'github',
                'reason' => 'invalid_signature',
            ]);

            return [
                'status' => 403,
                'payload' => ['message' => 'Invalid GitHub signature.'],
            ];
        }

        if (! $this->branchIsAccepted((string) $request->input('ref', ''))) {
            Log::info('Documentation webhook ignored.', [
                'provider' => 'github',
                'reason' => 'branch_not_accepted',
                'ref' => (string) $request->input('ref', ''),
            ]);

            return [
                'status' => 202,
                'payload' => ['message' => 'Webhook ignored for this branch.'],
            ];
        }

        return $this->sync('github');
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function processGitlab(Request $request): array
    {
        $secret = (string) config('documentation-engine.webhook_secret', '');

        if ($secret !== '' && ! hash_equals($secret, (string) $request->header('X-Gitlab-Token', ''))) {
            Log::warning('Documentation webhook rejected.', [
                'provider' => 'gitlab',
                'reason' => 'invalid_token',
            ]);

            return [
                'status' => 403,
                'payload' => ['message' => 'Invalid GitLab token.'],
            ];
        }

        if (! $this->branchIsAccepted((string) $request->input('ref', ''))) {
            Log::info('Documentation webhook ignored.', [
                'provider' => 'gitlab',
                'reason' => 'branch_not_accepted',
                'ref' => (string) $request->input('ref', ''),
            ]);

            return [
                'status' => 202,
                'payload' => ['message' => 'Webhook ignored for this branch.'],
            ];
        }

        return $this->sync('gitlab');
    }

    private function hasValidGithubSignature(Request $request, string $secret): bool
    {
        $signature = (string) $request->header('X-Hub-Signature-256', '');

        if ($signature === '') {
            return false;
        }

        $hash = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($hash, $signature);
    }

    private function branchIsAccepted(string $ref): bool
    {
        if ($ref === '') {
            return true;
        }

        $branch = (string) config('documentation-engine.webhook_branch', 'main');

        return $ref === "refs/heads/{$branch}";
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    private function sync(string $provider): array
    {
        $result = $this->syncMarkdownDocs->execute();

        Log::info('Documentation webhook synced.', [
            'provider' => $provider,
            'commit' => $result->commit,
            'created_versions' => $result->createdVersionsCount(),
            'archived_documents' => $result->archivedDocumentsCount(),
            'errors' => count($result->errors),
        ]);

        return [
            'status' => 200,
            'payload' => ['message' => 'Documentation synced.'] + $result->toArray(),
        ];
    }
}
