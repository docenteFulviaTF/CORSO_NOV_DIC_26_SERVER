# Giorno 10 – Esercizi del pomeriggio (controller e Blade) – versione spiegata

Oggi lavori nel progetto `bookshelf` su **controller** e **viste Blade**.
Modifichi le pagine dei libri fatte stamattina e crei le pagine dei generi.

Ogni esercizio ha questa forma:

- la richiesta;
- una spiegazione di cosa serve, con un esempio;
- i passi da seguire uno alla volta;
- le **trappole**, cioè gli errori più comuni;
- il **Controllo**, cioè come capire se funziona.

> **Importante: gli esempi mostrano la forma, non sono la soluzione.**
> Gli esempi parlano di un'applicazione diversa, inventata: un'azienda con i model `Employee` (dipendente) e `Department` (reparto), e un `EmployeeController`.
> Devi adattare la forma ai libri, agli autori e ai generi.

---

## Prima di iniziare

### 1. Server e dati

- Terminale 1: `php artisan serve`.
- Terminale 2: per gli altri comandi.

Se i dati sono strani, riparti da quelli originali:

```bash
php artisan migrate:fresh --seed
```

Ci sono 30 libri: 15 reali e 15 finti.

### 2. Il percorso di una richiesta

Quando apri `/books`:

1. Laravel cerca la rotta in `routes/web.php`.
2. La rotta chiama un metodo di un **controller**, per esempio `BookController@index` (il metodo `index` della classe `BookController`).
3. Il metodo legge i dati con Eloquent e restituisce una vista con `view(...)`.
4. La vista Blade produce l'HTML.

| Cosa | Dove |
|---|---|
| Rotte | `routes/web.php` |
| Controller | `app/Http/Controllers/` |
| Viste dei libri | `resources/views/books/` |
| Viste degli autori | `resources/views/authors/` |
| Layout | `resources/views/components/layout.blade.php` |

### 3. Controllare le rotte

```bash
php artisan route:list --path=books
```

Mostra solo le rotte che contengono `books`.

---

## Promemoria Blade

| Scrittura | Cosa fa |
|---|---|
| `{{ $valore }}` | stampa il valore, con la protezione di `htmlspecialchars` |
| `@if (...)` ... `@elseif (...)` ... `@else` ... `@endif` | condizione |
| `@foreach ($lista as $elem)` ... `@endforeach` | ciclo |
| `@forelse ($lista as $elem)` ... `@empty` ... `@endforelse` | ciclo, con un blocco per la lista vuota |
| `{{ route('nome', $modello) }}` | l'indirizzo di una rotta con nome |
| `{{-- commento --}}` | commento Blade: non compare nell'HTML |
| `<x-layout title="...">` ... `</x-layout>` | usa il componente layout |
| `$oggetto?->proprieta` | se l'oggetto è `null`, restituisce `null` invece di dare errore |

---

## 10.1 – Controller (30 minuti)

### La richiesta

1. Apri `/books?q=calvino&page=2`: cosa mostra? E `/books?page=3`? Perché?
2. Modifica `index` perché ordini i libri dal più recente (`orderByDesc('year')`), poi rimetti l'ordine per titolo.
3. Apri `/authors/1` e `/authors/abc`: che risposte ottieni? Quale riga di codice le produce?

### Cosa ti serve

**La query string.**
La parte dopo `?` nell'indirizzo si chiama **query string**.
Contiene coppie `nome=valore`, separate da `&`.
In `/books?q=calvino&page=2` ci sono due valori: `q` e `page`.

Nel controller, `$request->query('q')` legge il valore di `q`.

**La paginazione.**
`->paginate(10)` divide i risultati in pagine da 10.
Legge da solo il valore `page` dalla query string.

**Route model binding.**
Se il parametro della rotta ha lo stesso nome del parametro del metodo, e il metodo dichiara il tipo del model, Laravel cerca da solo la riga con quell'id.

```php
// rotta: /employees/{employee}
public function show(Employee $employee): View
```

### Passi

1. Apri le due pagine del punto 1. Per rispondere al "perché", conta i libri trovati e guarda `paginate(...)` in `BookController@index`.
2. Apri `app/Http/Controllers/BookController.php`. Nel metodo `index` cambia `orderBy('title')` come chiesto. Ricarica `/books`. Rimetti com'era.
3. Apri le due pagine del punto 3.
4. Apri `AuthorController.php` e la rotta degli autori in `routes/web.php`. Cerca da dove può arrivare ciascuna risposta.

### Trappole

- Al punto 2, se i libri finti hanno anni a caso, i primi libri possono essere finti. È normale.
- Al punto 3 la risposta a "quale riga" non è dentro il metodo `show`: guarda la **firma** del metodo, cioè la riga con il nome e i parametri.

### Controllo

- Sai dire quanti libri trova la ricerca "calvino" e quante pagine servono.
- Sai dire quante pagine ha l'elenco completo.
- Dopo il punto 2, l'elenco è di nuovo in ordine di titolo.
- Sai dire quale codice HTTP restituisce `/authors/abc` (F12 > Network).

---

## 10.2 – Blade nel dettaglio (40 minuti) – `books/show.blade.php`

### La richiesta

1. Mostra "Libro lungo" se il libro ha più di 400 pagine (usa l'accessor `is_long` di ieri, se l'hai fatto).
2. Se il libro non ha autori, scrivi "Autore sconosciuto".
3. Sotto la tabella, il link "Altri libri del genere Giallo" (con il nome del genere vero) che porta all'elenco filtrato: per ora un link a `/books?q=` va bene, il filtro per genere è il 10.4.

### Cosa ti serve

**`@if` in Blade.**

```blade
@if ($employee->is_senior)
    <p>Dipendente con più di 15 anni di servizio</p>
@endif
```

**Una lista vuota.**
Puoi controllare se una collection è vuota con `->isEmpty()`.
Oppure puoi trasformare il `@foreach` in `@forelse` e usare il blocco `@empty`.
Guarda `authors/show.blade.php`: usa `@forelse`.

**Un link con la query string.**
`route()` accetta un array di parametri.
I parametri che la rotta non ha nell'indirizzo finiscono nella query string:

```blade
<a href="{{ route('employees.index', ['q' => 'rossi']) }}">Cerca Rossi</a>
```

Produce `/employees?q=rossi`.

### Passi

1. Apri `resources/views/books/show.blade.php`.
2. Aggiungi il punto 1. Se ieri non hai fatto l'accessor, puoi confrontare direttamente le pagine.
3. Trova il `@foreach` degli autori. Aggiungi il caso senza autori.
4. Sotto `</table>` aggiungi il link del punto 3.
5. Prova tutte le pagine del Controllo.

### Trappole

- Se l'accessor `is_long` non esiste, `$book->is_long` restituisce `null` senza errore: la scritta non compare mai.
- Un libro senza genere: `$book->genre->name` dà errore. Usa `?->` come nella riga del genere già presente, oppure mostra il link solo se il genere c'è.
- Se trasformi `@foreach` in `@forelse`, cambia anche la chiusura in `@endforelse`.
- La virgola tra gli autori usa `$loop->last`: non toglierla.

### Controllo

- `/books/4` (660 pagine): compare "Libro lungo".
- `/books/5` (400 pagine): non compare.
- Per vedere "Autore sconosciuto" crea in tinker un libro senza autori: `Book::create(['title' => 'Prova'])`. Apri il suo dettaglio. Cancellalo dopo.
- `/books/8`: il link dice "Altri libri del genere Giallo".
- `/books/1`: il link dice "Romanzo".

---

## 10.3 – Layout (30 minuti) – `components/layout.blade.php`

### La richiesta

1. Aggiungi un piè di pagina con `Bookshelf - {{ date('Y') }}`.
2. Il link del menu della pagina in cui ti trovi è in grassetto: `request()->routeIs('books.*')` restituisce `true` nelle pagine dei libri.

### Cosa ti serve

**Il componente layout.**
`layout.blade.php` contiene la struttura comune a tutte le pagine.
Ogni pagina lo usa con `<x-layout>`.
Il contenuto della pagina finisce al posto di `{{ $slot }}`.
Una modifica al layout vale per tutte le pagine.

**`routeIs`.**
`request()->routeIs('employees.*')` restituisce `true` se la rotta della pagina corrente ha un nome che comincia con `employees.`.
L'asterisco `*` vuol dire "qualsiasi cosa".

**Aggiungere una classe o uno stile solo se una condizione è vera.**

Con `@class`:

```blade
<a href="{{ route('employees.index') }}" @class(['attivo' => request()->routeIs('employees.*')])>Dipendenti</a>
```

`@class` aggiunge la classe `attivo` solo se la condizione è vera.
Poi nello `<style>` del layout scrivi la regola per la classe, per esempio `font-weight: bold`.

### Passi

1. Apri il layout.
2. Aggiungi `<footer>` dopo `</main>`.
3. Nel `<nav>` aggiungi la condizione al link "Libri". Prova su `/books` e su `/authors`.
4. Fai lo stesso per il link "Autori".

### Trappole

- Il nome delle rotte degli autori comincia con `authors.`: guarda `routes/web.php`.
- Il link "Libri" deve essere in grassetto anche nella pagina di dettaglio di un libro. `books.*` copre sia `books.index` sia `books.show`.
- La regola CSS va nello `<style>` del layout. Se il grassetto non si vede, controlla con F12 che la classe sia presente sull'elemento.

### Controllo

- Il piè di pagina compare in tutte le pagine.
- Su `/books` e `/books/1`: "Libri" in grassetto, "Autori" no.
- Su `/authors` e `/authors/1`: il contrario.

---

## 10.4 – Filtro per genere (60 minuti)

### La richiesta

1. In `BookController@index` leggi anche `$request->query('genre')` (un id di genere): se c'è, aggiungi alla query `->where('genre_id', ...)`.
2. Passa alla vista l'elenco dei generi (`Genre::orderBy('name')->get()`) e aggiungi al form di ricerca una `select` con i generi e l'opzione "Tutti". Il genere scelto resta selezionato dopo la ricerca (`@selected`).
3. Verifica che la paginazione mantenga sia `q` sia `genre` (`withQueryString`).

### Cosa ti serve

**Una condizione solo se c'è un valore.**
La query di `index` è una catena di metodi.
Per aggiungere un `where` solo in certi casi, puoi spezzare la catena:

```php
$query = Employee::query()->orderBy('last_name');

if ($request->filled('department')) {
    $query->where('department_id', $request->query('department'));
}

$employees = $query->paginate(10);
```

`$request->filled('department')` restituisce `true` se il valore c'è e non è vuoto.

**La `select` nel form.**
Il form è in `GET`: i campi finiscono nella query string.
Il `name` della `select` diventa il nome del parametro.

```blade
<select name="department">
    <option value="">Tutti</option>
    @foreach ($departments as $d)
        <option value="{{ $d->id }}" @selected($department == $d->id)>{{ $d->name }}</option>
    @endforeach
</select>
```

`@selected(condizione)` scrive `selected` solo se la condizione è vera.

**Passare più valori alla vista.**
Guarda come `index` passa già `books` e `q`: aggiungi le altre chiavi nello stesso array.

### Passi

1. In `index`, prima della paginazione, aggiungi il filtro sul genere. Prova scrivendo a mano `/books?genre=4`.
2. Aggiungi `use App\Models\Genre;` in cima al controller. Leggi i generi.
3. Passa alla vista i generi e il genere scelto.
4. In `books/index.blade.php` aggiungi la `select` dentro il `<form>`, prima del pulsante.
5. Prova: Giallo, poi Tutti, poi Giallo con una parola nella ricerca.
6. Prova la paginazione con un filtro che dia più di 10 libri.
7. Torna al link della 10.2: ora puoi farlo puntare all'elenco filtrato per genere.

### Trappole

- L'opzione "Tutti" ha valore vuoto. Se controlli solo che `genre` esista, il valore vuoto aggiunge `where genre_id = ''` e non trovi niente. Per questo serve `filled`.
- `paginate(...)` e `withQueryString()` devono restare **alla fine** della catena, dopo tutti i `where`.
- Il valore dalla query string è un testo (`"4"`), l'id del genere è un numero (`4`). Con `===` il confronto è falso. In `@selected` usa `==`.
- Se la vista usa `$genres` ma il controller non lo passa: errore `Undefined variable $genres`.
- Lo scope `search` e il filtro per genere devono funzionare insieme. Se il filtro sul genere "esce" dalla ricerca, controlla l'ordine: lo scope `search` racchiude già le sue condizioni tra parentesi.

### Controllo

- `/books?genre=4`: solo gialli.
- Scegli Giallo e premi Cerca: l'indirizzo contiene `genre=...` e la `select` mostra ancora Giallo.
- "Tutti": tutti i libri.
- Giallo e "domenica": un libro solo.
- Con un filtro che dà più pagine, il link "Successiva" contiene ancora `genre=...` e `q=...`.

---

## 10.5 – La pagina dei generi (40 minuti)

### La richiesta

1. `php artisan make:controller GenreController` con `index` e `show`, e le loro rotte (`genres.index`, `genres.show`).
2. `index`: i generi con il numero dei libri (`withCount('books')`).
3. `show`: il nome del genere e i suoi libri ordinati per titolo, con il link al dettaglio.
4. Aggiungi "Generi" al menu del layout.

### Cosa ti serve

Il modello è `AuthorController` con le sue due viste.
Leggili prima di iniziare.

**`withCount`.**
`Department::withCount('employees')->get()` aggiunge a ogni reparto l'attributo `employees_count`.
Il nome è sempre: nome della relazione + `_count`.

**Caricare una relazione con un ordine.**
`AuthorController@show` usa `load` con una funzione che ordina i libri. Usa la stessa forma.

**Le viste.**
Crea la cartella `resources/views/genres/` con due file: `index.blade.php` e `show.blade.php`.
Puoi usare `php artisan make:view genres.index`: il punto diventa una cartella.

### Passi

1. `php artisan make:controller GenreController`.
2. Scrivi i due metodi copiando la forma di `AuthorController`. Aggiungi i `use` necessari.
3. Aggiungi le due rotte in `routes/web.php`, con i nomi richiesti. Aggiungi il `use` del controller in cima.
4. Controlla con `php artisan route:list --path=genres`.
5. Crea le due viste.
6. Aggiungi il link nel layout, con il grassetto della 10.3.

### Trappole

- Il parametro della rotta e il parametro del metodo devono avere lo stesso nome: `{genre}` e `Genre $genre`. Se sono diversi, il route model binding non funziona e ricevi un genere vuoto.
- La relazione `books()` del model `Genre` esiste già (giorno 9). Con `withCount('books')` l'attributo si chiama `books_count`.
- `make:view` crea un file quasi vuoto: aggiungi tu `<x-layout>`.
- Senza il `use App\Http\Controllers\GenreController;` in `web.php`: errore `Target class [GenreController] does not exist`.

### Controllo

- `/genres`: 5 generi con il loro numero di libri. Tra i libri reali Poesia non ne ha nessuno; i libri finti hanno un genere a caso, quindi il numero può essere diverso da 0. Per vedere uno 0, crea in tinker un genere nuovo.
- La somma dei numeri è uguale al numero totale dei libri con un genere.
- `/genres/4`: "Giallo" e i suoi libri in ordine di titolo. Ogni titolo porta al dettaglio.
- `/genres/999`: 404.
- Il link "Generi" compare nel menu ed è in grassetto nelle pagine dei generi.

---

## Extra – Il problema N+1

### La richiesta

In `app/Providers/AppServiceProvider.php`, metodo `boot()`, aggiungi

```php
\Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());
```

poi togli `with(['authors', 'genre'])` da `BookController@index`.
Cosa succede?
Leggi il messaggio di errore e rimetti `with`.

### Cosa ti serve

**Cos'è il problema N+1.**
La vista dell'elenco mostra, per ogni libro, gli autori e il genere.
Senza `with`, Eloquent fa:

- 1 query per i 10 libri della pagina;
- poi, per ogni libro, 1 query per gli autori e 1 per il genere.

Totale: 1 + 10 + 10 = 21 query invece di 3.
Con 1000 righe le query sarebbero migliaia.

Caricare una relazione solo quando la usi si chiama **lazy loading** (caricamento pigro).
Caricarla prima, con `with`, si chiama **eager loading** (caricamento anticipato).

`preventLazyLoading` trasforma il caricamento pigro in un errore, così te ne accorgi.
`! app()->isProduction()` vuol dire: solo durante lo sviluppo, non sul sito pubblicato.

### Dove guardare

- Il messaggio dice quale relazione e quale model.
- Cerca nella vista `books/index.blade.php` la riga che usa quella relazione.

### Trappole

- Le altre pagine possono dare lo stesso errore, se usano relazioni senza `with` o `load`. Controlla anche il dettaglio e le pagine dei generi.
- Se lasci `preventLazyLoading`, ricordati di rimettere `with`.

### Controllo

- Senza `with`: errore `Attempted to lazy load [...] on model [App\Models\Book] but lazy loading is disabled`.
- Con `with`: la pagina funziona di nuovo.

---

## Errori frequenti

| Messaggio o sintomo | Causa probabile |
|---|---|
| `Target class [XxxController] does not exist` | Manca il `use` del controller in `routes/web.php`, oppure il nome è scritto male. |
| `Call to undefined method App\Http\Controllers\XxxController::show()` | Il metodo non esiste nel controller, o ha un nome diverso da quello nella rotta. |
| `Route [genres.index] not defined` | La rotta non ha il nome, oppure il nome è diverso. Controlla con `php artisan route:list`. |
| `View [genres.index] not found` | Il file non è in `resources/views/genres/index.blade.php`. |
| `Undefined variable $xxx` | Il controller non passa quella chiave alla vista. |
| `Attempt to read property "name" on null` | Il libro non ha genere. Usa `?->`. |
| `Undefined property: ...::$books_count` | Manca `withCount('books')` nella query. |
| `404 Not Found` su `/genres/1` | Il nome del parametro della rotta e quello del metodo sono diversi, oppure manca la rotta. |
| `Unable to locate a class or view for component [layout]` | Il file `components/layout.blade.php` non esiste o ha un altro nome. |
| `syntax error, unexpected end of file` in una vista | Manca una chiusura: `@endif`, `@endforeach`, `@endforelse`. |
| La `select` torna sempre su "Tutti" | Il controller non passa il genere scelto, oppure `@selected` confronta con `===`. |
| Il filtro funziona, ma passando a pagina 2 si perde | Manca `withQueryString()` dopo `paginate()`. |
| `Attempted to lazy load [...] but lazy loading is disabled` | È l'errore dell'Extra: manca `with(...)` o `load(...)`. |
