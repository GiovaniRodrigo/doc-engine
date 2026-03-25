@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <div class="mb-6 flex items-center justify-end">
        <a
            href="/docs/{{ $slug }}/edit"
            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:text-gray-900"
        >
            Editar
        </a>
    </div>

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
