<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Mass assignment: solo questi campi si possono riempire con
 * Book::create($array) o $book->update($array).
 * user_id e cover_path NON ci sono: li impostiamo noi nel controller.
 * (Forma equivalente, molto diffusa nei tutorial:  protected $fillable = [...];)
 */
#[Fillable(['title', 'isbn', 'year', 'pages', 'genre_id'])]
class Book extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'year'  => 'integer',
            'pages' => 'integer',
        ];
    }

    // ---------------- Relazioni ----------------

    // 1-N, lato "molti": il libro appartiene a un genere
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // N-N
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    // ---------------- Query scope ----------------

    /**
     * Uso: Book::search('calvino')->get()
     * Cerca nel titolo e nel cognome degli autori.
     * (Forma equivalente nelle versioni precedenti: public function scopeSearch(...))
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhereHas('authors', fn (Builder $a) => $a->where('last_name', 'like', "%{$term}%"));
        });
    }

    // ---------------- Esercizi del giorno 9 ----------------

    // 9.2 accessor: $book->is_long
    protected function isLong(): Attribute
    {
        return Attribute::make(get: fn () => $this->pages > 400);
    }

    // 9.2 scope: Book::publishedBetween(1960, 1970)
    #[Scope]
    protected function publishedBetween(Builder $query, int $from, int $to): void
    {
        $query->whereBetween('year', [$from, $to]);
    }

    // 9.4
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
