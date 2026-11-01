@php
    $capa = $game['imagens'][0];
    $capturas = array_slice($game['imagens'], 1);
    $lancamento = \Illuminate\Support\Carbon::parse($game['data_lancamento'])->format('d/m/Y');
    $midias = [
        ...array_map(fn (string $video): array => ['type' => 'video', 'src' => $video, 'preview' => $capturas[0] ?? $capa], $game['videos']),
        ...array_map(fn (string $captura): array => ['type' => 'image', 'src' => $captura, 'preview' => $captura], $capturas),
    ];
@endphp

<x-layouts.app>
    <div
        class="mx-auto flex w-full max-w-6xl flex-col gap-8"
        x-data="{
            items: @js($midias),
            index: null,
            get current() { return this.index === null ? null : this.items[this.index]; },
            prev() { if (this.index > 0) this.index--; },
            next() { if (this.index < this.items.length - 1) this.index++; },
        }"
    >
        <a href="{{ route('games.index') }}" class="text-sm text-neutral-500 hover:text-sky-500">← Voltar para jogos</a>

        <div class="grid gap-8 md:grid-cols-[16rem_1fr]">
            <div class="flex flex-col gap-4">
                <img src="{{ $capa }}" alt="{{ $game['nome'] }}" class="aspect-[2/3] w-full rounded-md object-cover shadow-lg">

                <div class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $game['preco'] }}</div>

                <button type="button" class="flex w-full items-center justify-center gap-2 rounded-md bg-violet-600 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-violet-500">
                    <flux:icon.heart class="size-5" />
                    Adicionar à lista de desejos
                </button>
            </div>

            <div class="flex flex-col gap-6">
                <div>
                    <div class="text-xs uppercase tracking-wide text-neutral-500">{{ implode(' · ', $game['plataformas']) }}</div>
                    <h1 class="mt-1 text-3xl font-bold text-neutral-900 md:text-4xl dark:text-white">{{ $game['nome'] }}</h1>

                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                        <span class="rounded bg-emerald-600 px-2 py-1 font-semibold text-white">{{ $game['score'] }}%</span>
                        <span class="text-neutral-500">Avaliação dos jogadores</span>
                    </div>
                </div>

                <dl class="grid grid-cols-[8rem_1fr] gap-x-4 gap-y-2 rounded-md border border-neutral-200 p-4 text-sm dark:border-neutral-700">
                    <dt class="text-neutral-500">Desenvolvedora</dt>
                    <dd class="text-sky-600 dark:text-sky-400">{{ implode(', ', $game['desenvolvedoras']) }}</dd>
                    <dt class="text-neutral-500">Distribuidora</dt>
                    <dd class="text-sky-600 dark:text-sky-400">{{ implode(', ', $game['distribuidoras']) }}</dd>
                    <dt class="text-neutral-500">Lançamento</dt>
                    <dd>{{ $lancamento }}</dd>
                    <dt class="text-neutral-500">Plataformas</dt>
                    <dd>{{ implode(', ', $game['plataformas']) }}</dd>
                    <dt class="text-neutral-500">Gêneros</dt>
                    <dd class="flex flex-wrap gap-1">
                        @foreach ($game['generos'] as $genero)
                            <span class="rounded-full bg-sky-500 px-2 py-0.5 text-xs font-medium text-white">{{ $genero }}</span>
                        @endforeach
                    </dd>
                    <dt class="text-neutral-500">Idiomas</dt>
                    <dd>{{ implode(', ', $game['idiomas']) }}</dd>
                </dl>
            </div>
        </div>

        @if ($midias !== [])
            <section
                class="flex flex-col gap-3"
                x-data="{
                    atStart: true,
                    atEnd: false,
                    update() {
                        const el = this.$refs.track;
                        this.atStart = el.scrollLeft <= 1;
                        this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 1;
                    },
                    scrollBy(direction) {
                        this.$refs.track.scrollBy({ left: direction * this.$refs.track.clientWidth * 0.8, behavior: 'smooth' });
                    },
                }"
                x-init="$nextTick(() => update())"
                x-on:resize.window="update()"
            >
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Vídeos e capturas de tela</h2>

                <div class="relative">
                    <div x-ref="track" x-on:scroll.passive="update()" class="flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($midias as $i => $midia)
                            <button type="button" x-on:click="index = {{ $i }}" class="group relative w-64 shrink-0 snap-start overflow-hidden rounded-md" aria-label="{{ $midia['type'] === 'video' ? 'Reproduzir vídeo' : 'Ampliar captura de tela' }}">
                                <img src="{{ $midia['preview'] }}" alt="{{ $midia['type'] === 'video' ? 'Prévia do vídeo' : 'Captura de tela' }} de {{ $game['nome'] }}" loading="lazy" class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @if ($midia['type'] === 'video')
                                    <span class="absolute inset-0 flex items-center justify-center bg-black/30 transition group-hover:bg-black/50">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-white/90 text-neutral-900">
                                            <flux:icon.play variant="solid" class="size-6" />
                                        </span>
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <button type="button" x-show="!atStart" x-on:click="scrollBy(-1)" aria-label="Anterior" class="absolute left-2 top-1/2 flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80">
                        <flux:icon.chevron-left class="size-5" />
                    </button>
                    <button type="button" x-show="!atEnd" x-on:click="scrollBy(1)" aria-label="Próximo" class="absolute right-2 top-1/2 flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80">
                        <flux:icon.chevron-right class="size-5" />
                    </button>
                </div>
            </section>
        @endif

        <section class="flex flex-col gap-3">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Sobre o jogo</h2>
            <p class="whitespace-pre-line leading-relaxed text-neutral-600 dark:text-neutral-300">{{ $game['descricao'] }}</p>
        </section>

        <section class="flex flex-col gap-3">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Requisitos mínimos</h2>
            <div class="rounded-md border border-neutral-200 p-4 dark:border-neutral-700">
                <p class="whitespace-pre-line text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">{{ $game['requisitos_minimos'] }}</p>
            </div>
        </section>

        <div
            x-cloak
            x-show="current"
            x-on:click="index = null"
            x-on:keydown.escape.window="index = null"
            x-on:keydown.arrow-left.window="index !== null && prev()"
            x-on:keydown.arrow-right.window="index !== null && next()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-6"
        >
            <template x-if="current && current.type === 'video'">
                <video x-bind:key="index" x-bind:src="current.src" controls autoplay x-on:click.stop class="max-h-full w-full max-w-4xl rounded-md bg-black"></video>
            </template>
            <template x-if="current && current.type === 'image'">
                <img x-bind:src="current.src" alt="" x-on:click.stop class="max-h-full max-w-full rounded-md">
            </template>

            <button type="button" x-show="index > 0" x-on:click.stop="prev()" aria-label="Anterior" class="absolute left-4 top-1/2 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/30">
                <flux:icon.chevron-left class="size-6" />
            </button>
            <button type="button" x-show="index < items.length - 1" x-on:click.stop="next()" aria-label="Próximo" class="absolute right-4 top-1/2 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/30">
                <flux:icon.chevron-right class="size-6" />
            </button>
        </div>
    </div>
</x-layouts.app>
