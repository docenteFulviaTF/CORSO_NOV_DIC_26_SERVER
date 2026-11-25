<x-layout :title="$book->title">
    <table>
    <tr><th>Autori</th>
        <td>
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

    <p><a href="{{ route('books.index') }}">Torna all'elenco</a></p>
</x-layout>
