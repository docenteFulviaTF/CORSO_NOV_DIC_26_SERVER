<x-layout title="Libri">
    <form method="GET" action="{{ route('books.index') }}">
        <input type="search" name="q" value="{{ $q }}"
               placeholder="Titolo o cognome autore">
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
