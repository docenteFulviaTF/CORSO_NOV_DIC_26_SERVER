<x-layout :title="'Modifica: ' . $book->title">
    <form method="POST" action="{{ route('books.update', $book) }}" class="stack">
        @method('PUT')   {{-- i form conoscono solo GET e POST: aggiunge il campo nascosto _method=PUT --}}
        @include('books._form')
        <button type="submit">Aggiorna</button>
    </form>
</x-layout>
