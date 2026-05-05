<?php

namespace Giovani\DocumentationEngine\Application\Services;

use Illuminate\Support\Str;

class TableOfContentsBuilder
{
    /**
     * @return array<int, array{id:string,level:int,title:string}>
     */
    public function build(string $markdown): array
    {
        $items = [];

        foreach (preg_split('/\R/', $markdown) ?: [] as $line) {
            if (! preg_match('/^(#{2,4})\s+(.+)$/', trim($line), $matches)) {
                continue;
            }

            $title = trim(preg_replace('/\s+#*$/', '', $matches[2]) ?? '');

            if ($title === '') {
                continue;
            }

            $items[] = [
                'id' => Str::slug($title),
                'level' => strlen($matches[1]),
                'title' => $title,
            ];
        }

        return $items;
    }
}
