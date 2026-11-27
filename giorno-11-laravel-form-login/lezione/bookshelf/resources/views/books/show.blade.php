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

    {{-- Pulsanti visibili solo al proprietario. Il controllo vero e' nel controller:
         nascondere un pulsante non protegge niente. --}}
    @auth
        @if ((int) $book->user_id === (int) auth()->id())
            <p style="margin-top:1rem">
                <a class="btn" href="{{ route('books.edit', $book) }}">Modifica</a>

                {{-- Eliminare con un form, non con un link: una GET non deve modificare dati --}}
                <form method="POST" action="{{ route('books.destroy', $book) }}" class="inline"
                      onsubmit="return confirm('Eliminare il libro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Elimina</button>
                </form>
            </p>
        @endif
    @endauth

    <p><a href="{{ route('books.index') }}">Torna all'elenco</a></p>
</x-layout>
