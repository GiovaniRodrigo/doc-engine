@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @if (count($breadcrumb))
        <nav class="mb-6 text-sm text-gray-500">
            @foreach ($breadcrumb as $item)
                <a href="/docs/{{ $item['slug'] }}" class="hover:text-gray-700">
                    {{ $item['title'] }}
                </a>

                @if (!$loop->last)
                    <span class="mx-2 text-gray-300">/</span>
                @endif
            @endforeach
        </nav>
    @endif

    <article class="prose prose-slate max-w-none">
        {!! $html !!}
    </article>

    @if ($nav['prev'] || $nav['next'])
        <div class="mt-10 flex items-center justify-between gap-4 border-t pt-6">
            <div>
                @if ($nav['prev'])
                    <a href="/docs/{{ $nav['prev'] }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        ← {{ $nav['prev'] }}
                    </a>
                @endif
            </div>

            <div class="text-right">
                @if ($nav['next'])
                    <a href="/docs/{{ $nav['next'] }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        {{ $nav['next'] }} →
                    </a>
                @endif
            </div>
        </div>
    @endif
@endsection
