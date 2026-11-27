<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Creato con: php artisan make:controller BookController --resource --model=Book
// Soluzione del 10.4: when() aggiunge la condizione solo se il parametro c'è.
// (Equivale a: if ($request->query('genre')) { $query->where(...); })
class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::query()
            ->with(['authors', 'genre'])       // eager loading: evita il problema N+1
            ->search($request->query('q'))     // lo scope di ieri
            ->when($request->query('genre'), fn ($q, $genre) => $q->where('genre_id', $genre))   // 10.4
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();               // mantiene ?q= nei link di paginazione

        return view('books.index', [
            'books' => $books,
            'q'     => $request->query('q'),
            'genre' => $request->query('genre'),
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    // Route model binding: Laravel cerca il libro con l'id dell'indirizzo, o risponde 404
    public function show(Book $book): View
    {
        $book->load(['authors', 'genre', 'user']);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        //
    }

    public function update(Request $request, Book $book)
    {
        //
    }

    public function destroy(Book $book)
    {
        //
    }
}
