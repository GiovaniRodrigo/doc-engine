<?php

namespace Giovani\DocumentationEngine\Infrastructure\Listeners;

use Giovani\DocumentationEngine\Domain\Events\DocumentDraftSaved;
use Giovani\DocumentationEngine\Domain\Events\DocumentSynced;
use Giovani\DocumentationEngine\Domain\Events\DocumentVersionPublished;
use Giovani\DocumentationEngine\Domain\Events\DocumentViewed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ActivityLogListener
{
    public function onDocumentViewed(DocumentViewed $event): void
    {
        $this->log('document.viewed', $event->slug, [
            'actor_id'   => $event->actorId,
            'actor_name' => $event->actorName,
            'ip_address' => $event->ipAddress,
        ]);

        $this->incrementAnalytics($event->slug, views: 1);
    }

    public function onDocumentDraftSaved(DocumentDraftSaved $event): void
    {
        $this->log('document.draft_saved', $event->slug, [
            'actor_id'   => $event->actorId,
            'actor_name' => $event->actorName,
            'ip_address' => $event->ipAddress,
            'metadata'   => ['version' => $event->version],
        ]);

        $this->incrementAnalytics($event->slug, edits: 1);
    }

    public function onDocumentVersionPublished(DocumentVersionPublished $event): void
    {
        $this->log('document.version_published', $event->slug, [
            'actor_id'   => $event->actorId,
            'actor_name' => $event->actorName,
            'ip_address' => $event->ipAddress,
            'metadata'   => ['version' => $event->version],
        ]);

        $this->incrementAnalytics($event->slug, publishes: 1);
    }

    public function onDocumentSynced(DocumentSynced $event): void
    {
        $this->log('document.synced', null, [
            'actor_type' => 'system',
            'metadata'   => [
                'created' => $event->created,
                'updated' => $event->updated,
                'skipped' => $event->skipped,
                'commit'  => $event->commit,
            ],
        ]);
    }

    private function log(string $event, ?string $slug, array $data): void
    {
        $actorId = $data['actor_id'] ?? null;

        try {
            DB::table('document_activity_log')->insert([
                'id'         => (string) Str::ulid(),
                'event'      => $event,
                'slug'       => $slug,
                'actor_type' => $actorId ? 'user' : ($data['actor_type'] ?? null),
                'actor_id'   => $actorId,
                'actor_name' => $data['actor_name'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'metadata'   => isset($data['metadata']) ? json_encode($data['metadata']) : null,
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('documentation-engine: activity log unavailable — run php artisan migrate', [
                'event' => $event,
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function incrementAnalytics(
        string $slug,
        int $views = 0,
        int $edits = 0,
        int $publishes = 0,
    ): void {
        try {
            $now = now();

            $existing = DB::table('document_analytics')->where('slug', $slug)->first();

            if ($existing) {
                $update = ['updated_at' => $now];

                if ($views > 0) {
                    $update['views_count']    = $existing->views_count + $views;
                    $update['last_viewed_at'] = $now;
                }
                if ($edits > 0) {
                    $update['edits_count']    = $existing->edits_count + $edits;
                    $update['last_edited_at'] = $now;
                }
                if ($publishes > 0) {
                    $update['publishes_count']   = $existing->publishes_count + $publishes;
                    $update['last_published_at'] = $now;
                }

                DB::table('document_analytics')->where('slug', $slug)->update($update);
            } else {
                DB::table('document_analytics')->insert([
                    'slug'              => $slug,
                    'views_count'       => $views,
                    'edits_count'       => $edits,
                    'publishes_count'   => $publishes,
                    'last_viewed_at'    => $views > 0 ? $now : null,
                    'last_edited_at'    => $edits > 0 ? $now : null,
                    'last_published_at' => $publishes > 0 ? $now : null,
                    'updated_at'        => $now,
                ]);
            }
        } catch (Throwable $e) {
            Log::warning('documentation-engine: analytics table unavailable — run php artisan migrate', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
