<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['first_name', 'last_name', 'nationality', 'birth_year'])]
class Author extends Model
{
    use HasFactory;

    // N-N: un autore scrive molti libri (tabella pivot author_book)
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    // Attributo calcolato: $author->full_name
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    // 9.2 scope: Author::italian()->count()
    #[Scope]
    protected function italian(Builder $query): void
    {
        $query->where('nationality', 'italiana');
    }
}
