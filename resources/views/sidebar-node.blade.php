<ul style="list-style:none;padding-left:10px">

@foreach($nodes as $node)

<li>

    @if($node->slug)
        <a href="/docs/{{ $node->slug }}" style="color: black">
            {{ $node->title }}
        </a>
    @else
        <strong>{{ $node->title }}</strong>
    @endif

    @if(count($node->children))
        @include('documentation-engine::sidebar-node', ['nodes'=>$node->children])
    @endif

</li>

@endforeach

</ul>