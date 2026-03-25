<ul>
@foreach($node->children as $child)

    @if($child->isDirectory)

        <li>
            <strong>{{ $child->title }}</strong>
            @include('documentation::partials.tree', ['node' => $child])
        </li>

    @else

        <li>
            <a href="/docs/{{ $project }}/{{ $child->path }}">
                {{ $child->title }}
            </a>
        </li>

    @endif

@endforeach
</ul>