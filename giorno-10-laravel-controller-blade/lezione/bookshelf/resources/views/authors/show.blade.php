<x-layout :title="$author->full_name">
    <ul>
    @forelse ($author->books as $book)
        <li><a href="{{ route('books.show', $book) }}">
            {{ $book->title }}</a> ({{ $book->year }})</li>
    @empty
        <li>Nessun libro.</li>
    @endforelse
    </ul>
</x-layout>
