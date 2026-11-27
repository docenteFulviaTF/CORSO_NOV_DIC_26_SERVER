<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\View\View;

// Esercizio 10.5
class GenreController extends Controller
{
    public function index(): View
    {
        $genres = Genre::withCount('books')->orderBy('name')->get();

        return view('genres.index', compact('genres'));
    }

    public function show(Genre $genre): View
    {
        $genre->load(['books' => fn ($q) => $q->orderBy('title')]);

        return view('genres.show', compact('genre'));
    }
}
