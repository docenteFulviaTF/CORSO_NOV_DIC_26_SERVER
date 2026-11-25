<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\View\View;

// Creato con: php artisan make:controller AuthorController
class AuthorController extends Controller
{
    public function index(): View
    {
        $authors = Author::withCount('books')
            ->orderBy('last_name')->get();

        return view('authors.index', compact('authors'));
    }

    public function show(Author $author): View
    {
        $author->load(['books' => fn ($q) => $q->orderBy('year')]);

        return view('authors.show', compact('author'));
    }
}
