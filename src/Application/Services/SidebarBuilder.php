<?php

namespace Giovani\DocumentationEngine\Application\Services;

use Giovani\DocumentationEngine\Application\DTO\DocNode;

class SidebarBuilder
{
    public function build(array $slugs): array
    {
        $tree = [];

        $formatter = new DocNameFormatter;

        foreach ($slugs as $slug) {

            $parts = explode('.', $slug);

            $current = &$tree;

            foreach ($parts as $index => $part) {

                $key = strtolower($part);

                if (! isset($current[$key])) {
                    $current[$key] = [
                        '_node' => new DocNode(
                            title: $formatter->format($part),
                            slug: $index === count($parts) - 1 ? $slug : null,
                            children: []
                        ),
                    ];
                }

                $current = &$current[$key]['_node']->children;
            }
        }

        return $this->normalize($tree);
    }

    protected function normalize(array $tree): array
    {
        $nodes = [];

        foreach ($tree as $item) {

            $node = $item['_node'];

            $node->children = $this->normalize($node->children);

            $nodes[] = $node;
        }

        usort($nodes, fn ($a, $b) => strcmp($a->title, $b->title));

        return $nodes;
    }
}
