<x-layout title="Generi">
    <ul>
    @foreach ($genres as $genre)
        <li><a href="{{ route('genres.show', $genre) }}">{{ $genre->name }}</a> ({{ $genre->books_count }})</li>
    @endforeach
    </ul>
</x-layout>
