# Giorno 10 – Esercizi del pomeriggio (controller e Blade)

Si lavora nel progetto `bookshelf`.

---

## 10.1 – Controller (30 minuti)
1. Apri `/books?q=calvino&page=2`: cosa mostra? E `/books?page=3`? Perché?
2. Modifica `index` perché ordini i libri dal più recente (`orderByDesc('year')`), poi rimetti l'ordine per titolo.
3. Apri `/authors/1` e `/authors/abc`: che risposte ottieni? Quale riga di codice le produce?

## 10.2 – Blade nel dettaglio (40 minuti) – `books/show.blade.php`
1. Mostra "Libro lungo" se il libro ha più di 400 pagine (usa l'accessor `is_long` di ieri, se l'hai fatto).
2. Se il libro non ha autori, scrivi "Autore sconosciuto".
3. Sotto la tabella, il link "Altri libri del genere Giallo" (con il nome del genere vero)
   che porta all'elenco filtrato: per ora un link a `/books?q=` va bene, il filtro per genere è il 10.4.

## 10.3 – Layout (30 minuti) – `components/layout.blade.php`
1. Aggiungi un piè di pagina con `Bookshelf - {{ date('Y') }}`.
2. Il link del menu della pagina in cui ti trovi è in grassetto: `request()->routeIs('books.*')`
   restituisce `true` nelle pagine dei libri.

## 10.4 – Filtro per genere (60 minuti)
1. In `BookController@index` leggi anche `$request->query('genre')` (un id di genere):
   se c'è, aggiungi alla query `->where('genre_id', ...)`.
2. Passa alla vista l'elenco dei generi (`Genre::orderBy('name')->get()`) e aggiungi
   al form di ricerca una `select` con i generi e l'opzione "Tutti". Il genere scelto resta
   selezionato dopo la ricerca (`@selected`).
3. Verifica che la paginazione mantenga sia `q` sia `genre` (`withQueryString`).

## 10.5 – La pagina dei generi (40 minuti)
1. `php artisan make:controller GenreController` con `index` e `show`, e le loro rotte
   (`genres.index`, `genres.show`).
2. `index`: i generi con il numero dei libri (`withCount('books')`).
3. `show`: il nome del genere e i suoi libri ordinati per titolo, con il link al dettaglio.
4. Aggiungi "Generi" al menu del layout.

## Extra – Il problema N+1
In `app/Providers/AppServiceProvider.php`, metodo `boot()`, aggiungi
`\Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());`
poi togli `with(['authors', 'genre'])` da `BookController@index`. Cosa succede?
Leggi il messaggio di errore e rimetti `with`.
