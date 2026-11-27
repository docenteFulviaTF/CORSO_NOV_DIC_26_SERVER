<x-layout :title="$book->title">
    <table>
    <tr><th>Autori</th>
        <td>
        @if ($book->authors->isEmpty())
            Autore sconosciuto
        @endif
        @foreach ($book->authors as $author)
            <a href="{{ route('authors.show', $author) }}">
            {{ $author->full_name }}</a>@if (! $loop->last), @endif
        @endforeach
        </td>
    </tr>
    <tr><th>Genere</th><td>{{ $book->genre?->name ?? '-' }}</td></tr>
    <tr><th>Anno</th><td>{{ $book->year ?? '-' }}</td></tr>
    <tr><th>Pagine</th><td>{{ $book->pages ?? '-' }}</td></tr>
    <tr><th>ISBN</th><td>{{ $book->isbn ?? '-' }}</td></tr>
    <tr><th>Inserito da</th><td>{{ $book->user?->name ?? '-' }}</td></tr>
    </table>

    @if ($book->pages > 400)
        <p><strong>Libro lungo</strong></p>
    @endif

    @if ($book->genre)
        <p><a href="{{ route('books.index', ['genre' => $book->genre_id]) }}">Altri libri del genere {{ $book->genre->name }}</a></p>
    @endif

    <p><a href="{{ route('books.index') }}">Torna all'elenco</a></p>
</x-layout>
