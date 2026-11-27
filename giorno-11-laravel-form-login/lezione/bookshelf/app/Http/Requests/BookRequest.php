<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Creato con: php artisan make:request BookRequest
// Le stesse regole servono per creare e per modificare un libro.
class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // il comando scrive false: con false ogni invio risponde 403
    }

    public function rules(): array
    {
        // In modifica la rotta contiene il libro: serve per ignorarlo nel controllo unique
        $book = $this->route('book');

        return [
            'title'        => ['required', 'string', 'max:200'],
            'isbn'         => [
                'nullable', 'digits:13',
                Rule::unique('books', 'isbn')->ignore($book?->id),
            ],
            'year'         => ['nullable', 'integer', 'min:1450', 'max:' . date('Y')],
            'pages'        => ['nullable', 'integer', 'min:1'],
            'genre_id'     => ['nullable', 'exists:genres,id'],
            'author_ids'   => ['required', 'array', 'min:1'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'      => 'Il titolo è obbligatorio.',
            'isbn.unique'         => 'ISBN già presente.',
            'author_ids.required' => 'Scegli almeno un autore.',
        ];
    }
}
