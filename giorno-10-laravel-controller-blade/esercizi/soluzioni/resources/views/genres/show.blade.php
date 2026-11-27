<x-layout :title="$genre->name">
    <ul>
    @forelse ($genre->books as $book)
        <li><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></li>
    @empty
        <li>Nessun libro.</li>
    @endforelse
    </ul>
</x-layout>
