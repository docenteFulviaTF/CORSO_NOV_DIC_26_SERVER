<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Creato con: php artisan make:controller BookController --resource --model=Book
// Oggi si scrivono index e show; gli altri metodi restano vuoti fino a domani.
class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::query()
            ->with(['authors', 'genre'])       // eager loading: evita il problema N+1
            ->search($request->query('q'))     // lo scope di ieri
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();               // mantiene ?q= nei link di paginazione

        return view('books.index', [
            'books' => $books,
            'q'     => $request->query('q'),
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
