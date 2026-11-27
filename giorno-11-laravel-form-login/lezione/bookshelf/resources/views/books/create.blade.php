<x-layout title="Nuovo libro">
    <form method="POST" action="{{ route('books.store') }}" class="stack">
        @include('books._form')
        <button type="submit">Salva</button>
    </form>
</x-layout>
