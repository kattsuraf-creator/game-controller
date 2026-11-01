<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPageOptions = [10, 20, 30, 50];
        $perPage = (int) $request->query('per_page', 10);

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $allGames = $this->mockGames();

        $games = new LengthAwarePaginator(
            $allGames->forPage(Paginator::resolveCurrentPage(), $perPage)->values(),
            $allGames->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('games.index', compact('games', 'perPage', 'perPageOptions'));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function mockGames(): Collection
    {
        return collect([
            ['title' => 'The Legend of Zelda: Breath of the Wild', 'platform' => 'Nintendo Switch', 'genre' => 'Aventura', 'year' => 2017, 'status' => 'Zerado'],
            ['title' => 'God of War Ragnarök', 'platform' => 'PlayStation 5', 'genre' => 'Ação', 'year' => 2022, 'status' => 'Jogando'],
            ['title' => 'Elden Ring', 'platform' => 'PC', 'genre' => 'RPG', 'year' => 2022, 'status' => 'Jogando'],
            ['title' => 'Forza Horizon 5', 'platform' => 'Xbox Series X', 'genre' => 'Corrida', 'year' => 2021, 'status' => 'Na fila'],
            ['title' => 'Hades', 'platform' => 'PC', 'genre' => 'Roguelike', 'year' => 2020, 'status' => 'Zerado'],
            ['title' => 'Super Mario Odyssey', 'platform' => 'Nintendo Switch', 'genre' => 'Plataforma', 'year' => 2017, 'status' => 'Na fila'],
            ...$this->generatedGames(),
        ])->map(fn (array $game, int $index): array => [
            'id' => $index + 1,
            'nome' => $game['title'],
            'preco' => $index % 7 === 0 ? 'Gratuito' : 'R$ '.number_format(19.99 + (($index * 11) % 80), 2, ',', '.'),
            'imagens' => [
                'https://picsum.photos/seed/game-'.($index + 1).'/400/600',
                ...array_map(
                    fn (int $n): string => 'https://picsum.photos/seed/game-'.($index + 1).'-shot-'.$n.'/800/450',
                    range(1, 4),
                ),
            ],
            'videos' => ['https://www.w3schools.com/html/mov_bbb.mp4'],
            'score' => (int) round((6 + (($index * 7) % 40) / 10) * 10),
            'plataformas' => $game['platform'] === 'PC' ? ['Windows', 'Linux'] : [$game['platform']],
            'requisitos_minimos' => "Sistema Operativo: Windows 10 64-bit\nProcessador: Intel Core i5-4460\nMemória: 8 GB de RAM\nPlaca de vídeo: GTX 960\nArmazenamento: 50 GB de espaço disponível",
            'data_lancamento' => sprintf('%d-%02d-%02d', $game['year'], ($index % 12) + 1, ($index % 28) + 1),
            'idiomas' => array_slice(['Inglês', 'Português (Brasil)', 'Espanhol', 'Francês', 'Alemão', 'Japonês'], 0, 2 + ($index % 5)),
            'generos' => array_values(array_unique([$game['genre'], ['Singleplayer', 'Multiplayer', 'Co-op', 'Mundo aberto'][$index % 4]])),
            'descricao' => 'Um jogo de '.mb_strtolower($game['genre']).' lançado em '.$game['year'].' para '.$game['platform'].', com campanha envolvente e muitas horas de diversão.'
                ."\n\nExplore mundos cuidadosamente construídos, enfrente desafios que testam suas habilidades e descubra uma história que se desenrola conforme você avança."
                ."\n\nCom gráficos refinados, trilha sonora marcante e jogabilidade fluida, este título é uma experiência pensada tanto para novos jogadores quanto para veteranos do gênero.",
            'desenvolvedoras' => [['Studio Alpha', 'Nova Games', 'Pixel Forge', 'Iron Peak', 'Blue Orbit'][$index % 5]],
            'distribuidoras' => [['Orbit Publishing', 'Nova Games', 'Iron Peak', 'Alpha Interactive'][$index % 4]],
        ]);
    }

    /**
     * @return array<int, array{title: string, platform: string, genre: string, year: int, status: string}>
     */
    private function generatedGames(): array
    {
        $titles = [
            'Red Dead Redemption 2', 'The Witcher 3: Wild Hunt', 'Cyberpunk 2077', 'Hollow Knight', 'Celeste',
            'Stardew Valley', 'Dark Souls III', 'Bloodborne', 'Sekiro: Shadows Die Twice', 'Horizon Zero Dawn',
            'Ghost of Tsushima', 'Spider-Man: Miles Morales', 'Uncharted 4', 'The Last of Us Part II', 'Persona 5 Royal',
            'Final Fantasy VII Remake', 'Resident Evil 4', 'Doom Eternal', 'Halo Infinite', 'Gears 5',
            'Mario Kart 8 Deluxe', 'Animal Crossing: New Horizons', 'Metroid Dread', 'Fire Emblem: Three Houses', 'Kirby and the Forgotten Land',
            'Minecraft', 'Terraria', 'Cuphead', 'Ori and the Will of the Wisps', 'Disco Elysium',
            'Baldur\'s Gate 3', 'Divinity: Original Sin 2', 'Hades II', 'Dead Cells', 'Hotline Miami',
            'Portal 2', 'Half-Life: Alyx', 'Civilization VI', 'Age of Empires IV', 'Starfield',
            'Assassin\'s Creed Valhalla', 'Far Cry 6', 'FIFA 23', 'Gran Turismo 7', 'Need for Speed Heat',
            'Sea of Thieves', 'Control', 'Alan Wake 2', 'Death Stranding', 'Returnal',
        ];

        $platforms = ['PC', 'PlayStation 5', 'Xbox Series X', 'Nintendo Switch', 'PlayStation 4'];
        $genres = ['Ação', 'RPG', 'Aventura', 'Estratégia', 'Corrida', 'Plataforma', 'Roguelike', 'Tiro'];
        $statuses = ['Zerado', 'Jogando', 'Na fila', 'Abandonado'];

        return array_map(fn (string $title, int $index): array => [
            'title' => $title,
            'platform' => $platforms[$index % count($platforms)],
            'genre' => $genres[$index % count($genres)],
            'year' => 2014 + ($index % 10),
            'status' => $statuses[$index % count($statuses)],
        ], $titles, array_keys($titles));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $game = $this->mockGames()->firstWhere('id', (int) $id);

        abort_if($game === null, 404);

        return view('games.show', compact('game'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
