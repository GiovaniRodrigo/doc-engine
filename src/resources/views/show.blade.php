<div style="display:flex">

    <aside style="width:300px;border-right:1px solid #ddd;padding:10px">
        @include('documentation::partials.tree', ['node' => $nav])
    </aside>

    <main style="padding:20px;width:100%">

        <div style="margin-bottom:20px">
            @foreach($breadcrumbs as $bc)
                <a href="/docs/{{ $project }}/{{ $bc['path'] }}">
                    {{ $bc['title'] }}
                </a> /
            @endforeach
        </div>

        {!! $html !!}

    </main>

</div>