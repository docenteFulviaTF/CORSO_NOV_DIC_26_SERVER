{{-- Campi comuni a create ed edit, inclusi con @include('books._form') --}}
@csrf

<label>Titolo
    <input type="text" name="title" value="{{ old('title', $book->title) }}">
    @error('title') <span class="error">{{ $message }}</span> @enderror
</label>

<label>Autori (Ctrl/Cmd per sceglierne piu' di uno)
    @php
        $selected = old('author_ids', $book->exists ? $book->authors->pluck('id')->all() : []);
    @endphp
    <select name="author_ids[]" multiple size="6">
        @foreach ($authors as $author)
            <option value="{{ $author->id }}" @selected(in_array($author->id, $selected))>
                {{ $author->last_name }}, {{ $author->first_name }}
            </option>
        @endforeach
    </select>
    @error('author_ids') <span class="error">{{ $message }}</span> @enderror
</label>

<label>Genere
    <select name="genre_id">
        <option value="">-- nessuno --</option>
        @foreach ($genres as $genre)
            <option value="{{ $genre->id }}" @selected(old('genre_id', $book->genre_id) == $genre->id)>
                {{ $genre->name }}
            </option>
        @endforeach
    </select>
    @error('genre_id') <span class="error">{{ $message }}</span> @enderror
</label>

{{-- Anno, pagine e ISBN: esercizio 11.1 --}}
