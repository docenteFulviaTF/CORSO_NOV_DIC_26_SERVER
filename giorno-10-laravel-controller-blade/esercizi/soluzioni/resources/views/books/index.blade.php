<x-layout title="Libri">
    <form method="GET" action="{{ route('books.index') }}">
        <input type="search" name="q" value="{{ $q }}"
               placeholder="Titolo o cognome autore">
        <select name="genre" aria-label="Genere">
            <option value="">Tutti</option>
            @foreach ($genres as $g)
                <option value="{{ $g->id }}" @selected($genre == $g->id)>{{ $g->name }}</option>
            @endforeach
        </select>
        <button type="submit">Cerca</button>
    </form>

    <table>
        <thead>
            <tr><th>Titolo</th><th>Autori</th>
                <th>Genere</th><th>Anno</th></tr>
        </thead>
        <tbody>
        @forelse ($books as $book)
            <tr>
                <td>
                    <a href="{{ route('books.show', $book) }}">
                        {{ $book->title }}
                    </a>
                </td>
                <td>
                    {{ $book->authors->pluck('full_name')->join(', ') }}
                </td>
                <td>{{ $book->genre?->name }}</td>
                <td>{{ $book->year }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Nessun libro trovato.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $books->links('pagination.semplice') }}
</x-layout>
