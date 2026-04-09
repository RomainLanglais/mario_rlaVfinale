<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Services\ToadFilmService;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    private ToadFilmService $filmService;

    public function __construct(ToadFilmService $filmService)
    {
        $this->middleware('auth');
        $this->filmService = $filmService;
    }

    public function index()
    {
        $films = Film::orderBy('film_id')->get()->map(function ($film) {
            return [
                'filmId'             => $film->film_id,
                'title'              => $film->title,
                'description'        => $film->description,
                'releaseYear'        => $film->release_year,
                'originalLanguageId' => $film->original_language_id,
                'rentalDuration'     => $film->rental_duration,
                'rentalRate'         => $film->rental_rate,
                'length'             => $film->length,
                'replacementCost'    => $film->replacement_cost,
                'rating'             => $film->rating,
            ];
        })->toArray();

        return view('films.index', ['films' => $films]);
    }

    public function create()
    {
        return view('films.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'releaseYear'        => 'nullable|integer|min:1888|max:2100',
            'originalLanguageId' => 'required|integer',
            'rentalDuration'     => 'required|integer|min:1',
            'length'             => 'nullable|integer|min:1',
            'rating'             => 'nullable|string|in:G,PG,PG-13,R,NC-17',
        ]);
        $data['rentalRate']      = 0;
        $data['replacementCost'] = 0;

        $film = $this->filmService->createFilm($data);

        if (!$film) {
            return back()->withInput()->with('error', 'Erreur lors de la création du film via l\'API.');
        }

        return redirect()->route('films.index')->with('success', 'Film ajouté avec succès !');
    }

    public function edit($id)
    {
        $film = $this->filmService->getFilmById($id);

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        return view('films.edit', ['film' => $film]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'releaseYear'        => 'nullable|integer|min:1888|max:2100',
            'originalLanguageId' => 'required|integer',
            'rentalDuration'     => 'required|integer|min:1',
            'length'             => 'nullable|integer|min:1',
            'rating'             => 'nullable|string|in:G,PG,PG-13,R,NC-17',
        ]);
        $data['rentalRate']      = 0;
        $data['replacementCost'] = 0;

        $film = $this->filmService->updateFilm((int) $id, $data);

        if (!$film) {
            return back()->withInput()->with('error', 'Erreur lors de la modification du film.');
        }

        return redirect()->route('films.index')->with('success', 'Film modifié avec succès !');
    }

    public function destroy($id)
    {
        $success = $this->filmService->deleteFilm((int) $id);

        if (!$success) {
            return back()->with('error', 'Erreur lors de la suppression du film.');
        }

        return redirect()->route('films.index')->with('success', 'Film supprimé avec succès.');
    }

    public function show($id)
    {
        $film = $this->filmService->getFilmById($id);

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        return view('films.show', [
            'film' => $film
        ]);
    }
}