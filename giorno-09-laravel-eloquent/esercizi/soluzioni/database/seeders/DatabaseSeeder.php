<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Utente di prova per il login (password: password)
        User::factory()->create([
            'name'  => 'Utente Demo',
            'email' => 'demo@example.com',
        ]);

        // Dati reali
        $this->call(BookshelfSeeder::class);

        // Dati finti generati dalle factory, utili per vedere la paginazione.
        // Commentare questo blocco se si vogliono solo i dati reali.
        Author::factory(5)
            ->has(Book::factory(3))   // ogni autore con 3 libri (riempie la pivot)
            ->create();

        // Esercizio 9.5
        Loan::factory(20)->create();
    }
}
