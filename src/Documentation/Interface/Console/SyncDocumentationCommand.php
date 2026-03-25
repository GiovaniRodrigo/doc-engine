<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Interface\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncDocumentationCommand extends Command
{
    protected $signature = 'doc-engine:sync {project?}';

    protected $description = 'Sync documentation from git repository';

    public function handle(): int
    {
        $project = $this->argument('project');

        $base = storage_path('docs');

        if ($project) {
            $this->syncProject("{$base}/{$project}");
        } else {
            foreach (scandir($base) as $dir) {

                if (in_array($dir, ['.', '..'])) {
                    continue;
                }

                $this->syncProject("{$base}/{$dir}");
            }
        }

        $this->info('Documentation synced.');

        return self::SUCCESS;
    }

    private function syncProject(string $path): void
    {
        if (! is_dir($path . '/.git')) {
            $this->warn("Skipping {$path} (no git repo)");
            return;
        }

        $this->info("Syncing {$path}");

        $process = proc_open(
            'git pull origin main',
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ],
            $pipes,
            $path
        );

        if (is_resource($process)) {

            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);

            $this->line($output);

            if ($error) {
                $this->error($error);
            }
        }

        // limpa cache navigation
        Cache::flush();
    }
}
