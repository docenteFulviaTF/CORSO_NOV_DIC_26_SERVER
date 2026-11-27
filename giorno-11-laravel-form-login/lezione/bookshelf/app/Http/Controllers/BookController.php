<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * | Verbo     | URI                | Metodo  | Nome rotta    |
 * |-----------|--------------------|---------|---------------|
 * | GET       | /books             | index   | books.index   |
 * | GET       | /books/create      | create  | books.create  |
 * | POST      | /books             | store   | books.store   |
 * | GET       | /books/{book}      | show    | books.show    |
 * | GET       | /books/{book}/edit | edit    | books.edit    |
 * | PUT/PATCH | /books/{book}      | update  | books.update  |
 * | DELETE    | /books/{book}      | destroy | books.destroy |
 */
class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::query()
            ->with(['authors', 'genre'])
            ->search($request->query('q'))
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('books.index', [
            'books' => $books,
            'q'     => $request->query('q'),
        ]);
    }

    public function create(): View
    {
        return view('books.create', [
            'book'    => new Book(),                       // libro vuoto: il form e' lo stesso della modifica
            'genres'  => Genre::orderBy('name')->get(),
            'authors' => Author::orderBy('last_name')->get(),
        ]);
    }

    // BookRequest valida PRIMA che il metodo inizi: se qualcosa non va, si torna al form
    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->validated();                     // solo i campi validati, mai $request->all()

        $book = new Book($data);                           // assegna solo le colonne fillable
        $book->user_id = $request->user()->id;             // il libro appartiene a chi lo inserisce
        $book->save();
        $book->authors()->sync($data['author_ids']);       // scrive nella pivot author_book

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'Libro creato.');            // messaggio flash: Post/Redirect/Get
    }

    public function show(Book $book): View
    {
        $book->load(['authors', 'genre', 'user']);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $this->authorizeOwner($book);

        return view('books.edit', [
            'book'    => $book->load('authors'),
            'genres'  => Genre::orderBy('name')->get(),
            'authors' => Author::orderBy('last_name')->get(),
        ]);
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $this->authorizeOwner($book);

        $data = $request->validated();
        $book->fill($data);
        $book->save();
        $book->authors()->sync($data['author_ids']);

        return redirect()->route('books.show', $book)->with('success', 'Libro aggiornato.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorizeOwner($book);

        $book->delete();   // le righe in author_book spariscono per il cascadeOnDelete

        return redirect()->route('books.index')->with('success', 'Libro eliminato.');
    }

    // Solo chi ha inserito il libro puo' modificarlo o eliminarlo
    private function authorizeOwner(Book $book): void
    {
        abort_if(
            (int) $book->user_id !== (int) auth()->id(),
            403,
            'Non puoi modificare un libro inserito da un altro utente.'
        );
    }
}
