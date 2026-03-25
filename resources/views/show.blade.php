@extends(config('documentation-engine.layout'))

@section('content')

<div style="display:flex">

    <aside style="width:280px;border-right:1px solid #ddd;padding:20px">

        @include('documentation-engine::sidebar-node', ['nodes' => $sidebar])

    </aside>

    <main style="flex:1;padding:30px">

        <div style="margin-bottom:20px;color:#250c0c">

            @foreach ($breadcrumb as $b)
                <a href="/docs/{{ $b['slug'] }}" style="color: #250c0c">{{ $b['title'] }}</a>

                @if (!$loop->last)
                    /
                @endif
            @endforeach

        </div>

        {!! $html !!}

        <hr style="margin-top:40px">

        <div style="display:flex;justify-content:space-between">

            @if ($nav['prev'])
                <a href="/docs/{{ $nav['prev'] }}">← {{ $nav['prev'] }}</a>
            @endif

            @if ($nav['next'])
                <a href="/docs/{{ $nav['next'] }}">{{ $nav['next'] }} →</a>
            @endif

        </div>

    </main>

</div>

@endsection