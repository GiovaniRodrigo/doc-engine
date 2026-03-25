<div style="display:flex">

    <aside style="width:300px;border-right:1px solid #ddd;padding:10px">

        @foreach ($nav->children as $child)
            <a hx-get="/docs/{{ $project }}/{{ $child->path }}" hx-target="#doc-content" hx-push-url="true">
                {{ $child->title }}
            </a>
        @endforeach

    </aside>

    <main id="doc-content" style="padding:20px;width:100%">
        {!! $html !!}
    </main>

</div>
