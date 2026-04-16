@php
    $depth = $depth ?? 0;
@endphp

<ul class="{{ $depth === 0 ? 'docs-nav-list' : 'docs-nav-sublist' }}">
    @foreach ($nodes as $node)
        <li class="{{ $node->slug ? 'docs-nav-item' : 'docs-nav-group' }}">
            @if ($node->slug)
                <a
                    href="/docs/{{ $node->slug }}"
                    class="docs-nav-link{{ request()->is('docs/' . $node->slug) ? ' active' : '' }}"
                >
                    {{ $node->title }}
                </a>
            @else
                <span class="docs-nav-group-title">{{ $node->title }}</span>
            @endif

            @if (count($node->children))
                @include('documentation-engine::sidebar-node', ['nodes' => $node->children, 'depth' => $depth + 1])
            @endif
        </li>
    @endforeach
</ul>
