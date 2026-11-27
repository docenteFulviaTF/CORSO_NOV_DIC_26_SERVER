<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('books.index'));

// Pagine pubbliche
Route::get('/books', [BookController::class, 'index'])
    ->name('books.index')
    ->middleware('tempo');                   // il middleware di prova (bootstrap/app.php)
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

// Solo per chi NON e' loggato
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');   // il nome "login" serve al middleware auth
    Route::post('/login', [AuthController::class, 'login']);
});

// Solo per chi e' loggato
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Le 5 rotte del resource che modificano dati: create, store, edit, update, destroy
    Route::resource('books', BookController::class)->except(['index', 'show']);
});

// Dichiarata DOPO books/create, altrimenti "create" verrebbe letto come {book}
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
