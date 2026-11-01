<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <flux:heading size="xl" level="1">Jogos</flux:heading>

        <div class="grid grid-cols-[repeat(auto-fill,minmax(10.5rem,1fr))] gap-x-4 gap-y-6">
            @foreach ($games as $game)
                <div
                    x-data="{ open: false, left: false }"
                    x-on:mouseenter="left = $el.getBoundingClientRect().right + 288 > window.innerWidth; open = true"
                    x-on:mouseleave="open = false"
                    class="relative"
                >
                    <a href="{{ route('games.show', $game['id']) }}" class="group block">
                        <div class="relative aspect-[2/3] overflow-hidden rounded-md bg-neutral-200 shadow-sm dark:bg-neutral-800">
                            <img src="{{ $game['imagens'][0] }}" alt="{{ $game['nome'] }}" loading="lazy" class="size-full object-cover transition-transform duration-300 group-hover:scale-105">
                        </div>
                        <div class="mt-2 line-clamp-2 text-sm font-medium text-neutral-700 transition-colors group-hover:text-sky-500 dark:text-neutral-200">
                            {{ $game['nome'] }}
                        </div>
                    </a>

                    <div
                        x-cloak
                        x-show="open"
                        x-transition.opacity.duration.150ms
                        x-bind:class="left ? 'right-full mr-2' : 'left-full ml-2'"
                        class="absolute top-0 z-20 hidden w-72 rounded-md bg-white p-4 text-sm shadow-xl ring-1 ring-black/5 md:block dark:bg-neutral-800 dark:ring-white/10"
                    >
                        <div class="flex items-center justify-between gap-2 text-xs text-neutral-500 dark:text-neutral-400">
                            <span>{{ \Illuminate\Support\Carbon::parse($game['data_lancamento'])->year }}</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $game['score'] }}%</span>
                        </div>
                        <div class="mt-1 font-semibold text-neutral-800 dark:text-neutral-100">{{ implode(', ', $game['desenvolvedoras']) }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ implode(', ', $game['plataformas']) }} · {{ $game['preco'] }}
                        </div>
                        <p class="mt-2 line-clamp-4 text-neutral-600 dark:text-neutral-300">{{ $game['descricao'] }}</p>
                        <div class="mt-3 flex flex-wrap gap-1">
                            @foreach ($game['generos'] as $genero)
                                <span class="rounded-full bg-sky-500 px-2 py-0.5 text-xs font-medium text-white">{{ $genero }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <form method="GET" action="{{ route('games.index') }}" class="flex items-center gap-2 text-sm">
                <label for="per_page">Por página</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()" class="rounded-lg border border-neutral-300 bg-white px-2 py-1 dark:border-neutral-600 dark:bg-neutral-800">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($option === $perPage)>{{ $option }}</option>
                    @endforeach
                </select>
                <span class="text-neutral-500">
                    Exibindo {{ $games->firstItem() }}–{{ $games->lastItem() }} de {{ $games->total() }}
                </span>
            </form>

            <div>{{ $games->links() }}</div>
        </div>
    </div>
</x-layouts.app>
