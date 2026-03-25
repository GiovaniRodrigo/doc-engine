<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <title>Documentação</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    @if (config('documentation-engine.css'))
        <link rel="stylesheet" href="{{ config('documentation-engine.css') }}">
    @endif

</head>

<body class="bg-gray-50">

    <div class="flex h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-72 bg-white border-r overflow-y-auto p-6">

            <h2 class="text-xl font-semibold mb-6">
                Docs
            </h2>

            @include('documentation-engine::sidebar-node', ['nodes' => $sidebar])

        </aside>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto">

            <div class="max-w-4xl mx-auto p-10">

                @yield('content')

            </div>

        </main>

    </div>

</body>

</html>
