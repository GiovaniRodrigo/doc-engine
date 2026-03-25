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

    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar documento</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $slug }}</p>
        </div>

        <a
            href="/docs/{{ $slug }}"
            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:text-gray-900"
        >
            Voltar
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('content') }}
        </div>
    @endif

    <form method="POST" action="/docs/{{ $slug }}" class="space-y-4">
        @csrf
        @method('PUT')

        <label for="content" class="block text-sm font-medium text-gray-700">
            Conteúdo em Markdown
        </label>

        <textarea
            id="content"
            name="content"
            rows="24"
            class="block min-h-[32rem] w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
        >{{ old('content', $content) }}</textarea>

        <div class="flex items-center justify-end gap-3">
            <a
                href="/docs/{{ $slug }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:text-gray-900"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
            >
                Salvar
            </button>
        </div>
    </form>
@endsection
