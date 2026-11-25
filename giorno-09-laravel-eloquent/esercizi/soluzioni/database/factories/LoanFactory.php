<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Esercizio 9.5
 * @extends Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    public function definition(): array
    {
        $prestito = fake()->dateTimeBetween('-6 months');

        return [
            'user_id' => fn () => User::inRandomOrder()->value('id'),
            'book_id' => fn () => Book::inRandomOrder()->value('id'),
            'loaned_at' => $prestito,
            // nel 70% dei casi restituito entro 30 giorni dal prestito, altrimenti ancora aperto
            'returned_at' => fake()->boolean(70)
                ? fake()->dateTimeBetween($prestito, (clone $prestito)->modify('+30 days'))
                : null,
        ];
    }
}
