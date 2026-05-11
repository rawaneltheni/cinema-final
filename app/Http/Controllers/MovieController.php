<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $movies = Movie::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('movies.index', [
            'movies' => $movies,
            'search' => $search,
            'username' => $request->session()->get('username'),
        ]);
    }

    public function create(): View
    {
        return view('movies.form', [
            'movie' => new Movie(),
            'username' => session('username'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Movie::create($this->validatedMovie($request));

        return redirect()->route('movies.index')->with('status', 'Movie added successfully.');
    }

    public function edit(Movie $movie): View
    {
        return view('movies.form', [
            'movie' => $movie,
            'username' => session('username'),
        ]);
    }

    public function update(Request $request, Movie $movie): RedirectResponse
    {
        $movie->update($this->validatedMovie($request));

        return redirect()->route('movies.index')->with('status', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        $movie->delete();

        return redirect()->route('movies.index')->with('status', 'Movie deleted successfully.');
    }

    private function validatedMovie(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'director' => ['required', 'string', 'max:255'],
            'release_year' => ['required', 'integer', 'between:1888,2100'],
            'genre' => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'between:1,600'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
