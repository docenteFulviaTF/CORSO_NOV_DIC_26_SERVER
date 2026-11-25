<x-layout title="Autori">
    <table>
    @foreach ($authors as $author)
        <tr>
            <td><a href="{{ route('authors.show', $author) }}">
                {{ $author->full_name }}</a></td>
            <td>{{ $author->nationality }}</td>
            <td>{{ $author->books_count }}</td>
        </tr>
    @endforeach
    </table>
</x-layout>
