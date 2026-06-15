@php
    $depth = $depth ?? 0;
@endphp

<ul class="{{ $depth === 0 ? 'docs-nav-list' : 'docs-nav-sublist' }}">
    @foreach ($nodes as $node)
        @php
            $hasChildren = count($node->children) > 0;
        @endphp
        <li class="{{ $node->slug ? 'docs-nav-item' : 'docs-nav-group' }}">
            @if ($node->slug)
                <a
                    href="/docs/{{ $node->slug }}"
                    class="docs-nav-link{{ request()->is('docs/' . $node->slug) ? ' active' : '' }}"
                    @if ($hasChildren) aria-expanded="true" @endif
                >
                    {{ $node->title }}
                </a>
            @else
                <span 
                    class="docs-nav-group-title"
                    @if ($hasChildren) aria-expanded="true" @endif
                >
                    {{ $node->title }}
                </span>
            @endif

            @if ($hasChildren)
                @include('documentation-engine::sidebar-node', ['nodes' => $node->children, 'depth' => $depth + 1])
            @endif
        </li>
    @endforeach
</ul>
