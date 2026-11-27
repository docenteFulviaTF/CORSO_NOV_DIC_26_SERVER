# Giorno 11 – Esercizi del pomeriggio – versione spiegata

Oggi lavori nel progetto `bookshelf` come è alla fine della mattina: form dei libri, validazione, login, middleware.
Completi il form, metti alla prova la validazione, scrivi due middleware e rispondi a domande sull'autenticazione.

Ogni esercizio ha questa forma:

- la richiesta;
- una spiegazione di cosa serve, con un esempio;
- i passi da seguire uno alla volta;
- le **trappole**, cioè gli errori più comuni;
- il **Controllo**, cioè come capire se funziona.

> **Importante: gli esempi mostrano la forma, non sono la soluzione.**
> Gli esempi parlano di un'applicazione diversa, inventata: un'azienda con dipendenti (`Employee`), il form dei dipendenti e la classe `EmployeeRequest`.
> Devi adattare la forma ai libri.

---

## Prima di iniziare

### 1. Il progetto della mattina

Se sei rimasto indietro:

1. Copia i file della cartella `lezione/bookshelf/` del corso nel tuo progetto, rispettando le cartelle.
2. Nel terminale: `php artisan migrate:fresh --seed`.

Il secondo comando cancella e ricrea tutte le tabelle. Crea l'utente demo: `demo@example.com`, password `password`.

### 2. Server

- Terminale 1: `php artisan serve`.
- Terminale 2: per gli altri comandi.

### 3. Dove stanno i file di oggi

| Cosa | File |
|---|---|
| Rotte | `routes/web.php` |
| Controller dei libri | `app/Http/Controllers/BookController.php` |
| Controller del login | `app/Http/Controllers/AuthController.php` |
| Regole di validazione | `app/Http/Requests/BookRequest.php` |
| Middleware | `app/Http/Middleware/` |
| Registrazione dei middleware | `bootstrap/app.php` |
| Form dei libri | `resources/views/books/_form.blade.php` (incluso da `create` ed `edit`) |

### 4. La scheda Rete (Network) del browser

Ti serve in tre esercizi.

1. Premi **F12** e apri la scheda **Rete** (Network).
2. Ricarica la pagina.
3. Clicca sulla prima riga (la pagina).
4. A destra, sezione **Intestazioni** (Headers): vedi il codice di stato e le **intestazioni della risposta** (Response Headers).

Un'**intestazione** è un'informazione che il server manda insieme alla pagina, in forma `Nome: valore`.

---

## 11.1 – Il form completo (30 minuti)

### La richiesta

In `resources/views/books/_form.blade.php`:

1. aggiungi i campi Anno, Pagine e ISBN, ognuno con `old()` e `@error` (Anno e Pagine con `type="number"`);
2. sopra i campi, un riepilogo di tutti gli errori con `$errors->any()` e `$errors->all()`.

Prova: crea un libro con anno, pagine e ISBN, poi aprilo in modifica: i tre campi devono essere già compilati.

### Cosa ti serve

**Il modello.**
Il campo Titolo, già presente in `_form.blade.php`, ha la forma completa.
Leggilo riga per riga.

**`old()`.**
`old('campo', $valore)` restituisce:

- il valore inviato con il form, se il form è tornato indietro con un errore;
- altrimenti, il secondo argomento: il valore attuale del libro.

In creazione il libro è vuoto (`new Book()`), quindi il campo è vuoto.
In modifica il libro ha i suoi dati, quindi il campo è compilato.

**`@error`.**
`@error('campo')` ... `@enderror` mostra il blocco solo se quel campo ha un errore.
Dentro il blocco, `$message` contiene il testo dell'errore.

**Il riepilogo.**
`$errors` è disponibile in tutte le viste.

```blade
@if ($errors->any())
    <ul class="error">
        @foreach ($errors->all() as $messaggio)
            <li>{{ $messaggio }}</li>
        @endforeach
    </ul>
@endif
```

- `$errors->any()`: `true` se c'è almeno un errore.
- `$errors->all()`: tutti i messaggi, in una lista.

**I nomi dei campi.**
Il `name` di ogni campo deve essere il nome della colonna e della regola in `BookRequest`.
Apri `BookRequest.php` e leggi i nomi in `rules()`.

### Passi

1. Apri `_form.blade.php`. Trova il commento `{{-- Anno, pagine e ISBN: esercizio 11.1 --}}`.
2. Copia il blocco `<label>` del titolo tre volte, sotto il commento.
3. In ogni copia cambia il testo, il `name`, l'argomento di `old()`, il campo in `@error` e il `type`.
4. Aggiungi il riepilogo dopo `@csrf`, prima del primo campo.
5. Fai login con l'utente demo. Prova il form vuoto. Poi crea un libro con tutti i campi. Poi aprilo in modifica.

### Trappole

- Il nome in `old('...')` deve essere uguale al `name` del campo. Se è diverso, dopo un errore il campo resta vuoto.
- Il secondo argomento di `old()` deve essere la proprietà del libro con il nome della colonna: `$book->year`, non `$book->anno`.
- `type="number"` blocca le lettere nel browser. La validazione sul server serve lo stesso: nella 11.2 vedrai perché.
- Il riepilogo va **dentro** `_form.blade.php`, così compare sia in creazione sia in modifica.

### Controllo

- Form inviato vuoto: il riepilogo in alto e gli errori accanto ai campi.
- Dopo l'errore, i campi che avevi compilato contengono ancora quello che avevi scritto.
- Libro creato con anno, pagine e ISBN: la pagina del libro li mostra.
- In modifica i tre campi sono compilati.

---

## 11.2 – Mettere alla prova la validazione (30 minuti)

### La richiesta

Rispondi per iscritto.

1. Invia il form con un ISBN di 12 cifre. Quale messaggio compare?
2. Con gli strumenti per sviluppatori (F12, scheda Elementi) cambia il `value` di un'opzione del menu Genere in `999`, sceglila e invia. Quale messaggio compare?
   Perché il controllo sul server serve anche se il menu offre solo generi esistenti?
3. Crea due libri con lo stesso ISBN. Poi modifica il primo senza cambiare l'ISBN: perché in modifica non compare l'errore? Quale riga di `BookRequest` lo evita?
4. Togli `@csrf` dal form e invialo. Quale codice di errore ottieni? Rimetti `@csrf`.

### Cosa ti serve

**Leggere una regola.**
In `BookRequest::rules()` ogni campo ha una lista di regole.

| Regola | Cosa controlla |
|---|---|
| `nullable` | il campo può essere vuoto |
| `required` | il campo è obbligatorio |
| `integer` | numero intero |
| `min:...`, `max:...` | valore minimo e massimo (per i numeri) o lunghezza (per i testi) |
| `digits:N` | esattamente N cifre |
| `exists:tabella,colonna` | il valore deve esistere in quella colonna del database |
| `Rule::unique(...)` | il valore non deve esistere già nella tabella |

**Come cambiare un valore con F12.**
1. Clic destro sul menu Genere > **Ispeziona**.
2. Nella scheda Elementi apri il `<select>` (triangolino a sinistra).
3. Doppio clic sul valore di `value="..."` di un'opzione. Scrivi il nuovo valore. Invio.
4. Nella pagina scegli quell'opzione e invia.

### Domande: dove guardare

- **Punto 1**: leggi il messaggio, poi trova in `rules()` la regola che lo produce.
- **Punto 2**: per il messaggio, trova la regola del genere. Per il "perché", ripensa al giorno 6: chi può inviare dati al server senza usare il tuo form?
- **Punto 3**: nella regola dell'ISBN c'è un metodo dopo `Rule::unique(...)`. Leggi anche la riga con `$this->route('book')` e il suo commento.
- **Punto 4**: confronta con l'esercizio 7.3 del giorno 7. Il codice si legge nella scheda Rete.

### Trappole

- Al punto 3 il secondo libro deve avere lo stesso ISBN del primo: devi vedere l'errore "ISBN già presente." in creazione, prima di provare la modifica.
- Al punto 3 i libri devono essere tuoi: se provi a modificare un libro di un altro utente ricevi 403 per un altro motivo (11.6).
- Al punto 4 la modifica va fatta nel file `_form.blade.php`. Ricordati di rimettere `@csrf` subito dopo.

### Controllo

Hai quattro risposte scritte, ognuna con il messaggio o il codice visto e la riga di codice che lo produce.

---

## 11.3 – Messaggi in italiano (30 minuti)

### La richiesta

In `BookRequest`:

1. aggiungi in `messages()` i messaggi per `isbn.digits`, `year.max` e `genre_id.exists`;
2. aggiungi il metodo `attributes()` che traduce i nomi dei campi: `title` → titolo, `year` → anno, `pages` → pagine, `genre_id` → genere.

Prova scrivendo `abc` nelle pagine: il messaggio diventa "The pagine field must be an integer.".
Il nome del campo è tradotto, il resto no: per tradurre tutti i messaggi di Laravel servono i file di lingua, che non facciamo.

### Cosa ti serve

**`messages()`.**
Restituisce un array.
La chiave è `campo.regola`. Il valore è il messaggio.
Il metodo esiste già in `BookRequest`, con tre messaggi: aggiungi i tuoi nello stesso array.

```php
public function messages(): array
{
    return [
        'salary.min'   => 'Lo stipendio è troppo basso.',
        'email.unique' => 'Questa email è già usata da un altro dipendente.',
    ];
}
```

**`attributes()`.**
Restituisce un array: nome del campo → nome da mostrare nei messaggi.

```php
public function attributes(): array
{
    return [
        'last_name'     => 'cognome',
        'department_id' => 'reparto',
    ];
}
```

**Il segnaposto `:max`.**
In un messaggio puoi scrivere `:max` (o `:min`, `:digits`): Laravel lo sostituisce con il valore della regola.
Esempio: `'salary.max' => 'Lo stipendio non può superare :max euro.'`.

### Passi

1. Apri `BookRequest.php`. Aggiungi i tre messaggi in `messages()`.
2. Prova ognuno: ISBN di 12 cifre, anno dell'anno prossimo, genere 999 (come nella 11.2).
3. Aggiungi il metodo `attributes()` dopo `messages()`.
4. Prova con `abc` nelle pagine. Per scriverlo, cambia con F12 il `type` del campo da `number` a `text`.

### Trappole

- `attributes()` è un metodo nuovo: va scritto **fuori** da `messages()`, dentro la classe.
- La chiave in `messages()` deve essere scritta esattamente come il nome del campo e della regola: `isbn.digits`, non `isbn.digit`.
- `type="number"` non lascia scrivere lettere: per la prova con `abc` devi cambiarlo con F12.

### Controllo

- I tre messaggi compaiono in italiano.
- Con `abc` nelle pagine il messaggio è quello indicato nella richiesta.
- I messaggi che non hai scritto tu sono in inglese, con il nome del campo in italiano.

---

## 11.4 – Il middleware su tutte le pagine (20 minuti)

### La richiesta

Registra `AddResponseTime` sul gruppo `web` in `bootstrap/app.php` (paragrafo 10.2) e togli `->middleware('tempo')` dalla rotta `/books`.
Verifica nella scheda Rete che l'intestazione `X-Response-Time` compaia anche su `/authors` e su `/login`.

### Cosa ti serve

**Cos'è un middleware.**
Un **middleware** è un pezzo di codice che viene eseguito prima e dopo il controller, per ogni richiesta che lo attraversa.
Leggi `app/Http/Middleware/AddResponseTime.php`: i commenti spiegano le due parti.

**Tre modi di usare un middleware.**

| Modo | Dove si scrive | Su quali pagine |
|---|---|---|
| Alias su una rotta | `->middleware('tempo')` in `web.php` | solo quella rotta |
| Alias su un gruppo di rotte | `Route::middleware(...)->group(...)` in `web.php` | le rotte del gruppo |
| Nel gruppo `web` | `bootstrap/app.php` | tutte le pagine di `web.php` |

**Il gruppo `web`.**
Tutte le rotte di `routes/web.php` passano dal gruppo di middleware `web`.
In `bootstrap/app.php`, dentro `withMiddleware(...)`, puoi aggiungere un middleware a quel gruppo.
La forma è nel paragrafo 10.2 della dispensa.

Esempio con un middleware inventato:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\LogVisits::class,
    ]);
})
```

### Passi

1. Apri `bootstrap/app.php`. Dentro `withMiddleware(...)` c'è già l'alias `tempo`. Aggiungi sotto la registrazione sul gruppo `web`.
2. Apri `routes/web.php`. Togli `->middleware('tempo')` dalla rotta `/books`.
3. Apri `/authors` con la scheda Rete aperta. Cerca l'intestazione.
4. Fai il logout e apri `/login`. Cerca l'intestazione.

### Trappole

- Il `;` alla fine della riga della rotta `/books`: quando togli `->middleware('tempo')`, controlla che la riga finisca ancora con `;`.
- L'alias `tempo` puoi lasciarlo: non dà fastidio.
- Se lasci anche `->middleware('tempo')` sulla rotta, il middleware gira due volte su `/books`.
- Errore di sintassi in `bootstrap/app.php`: nessuna pagina funziona. Controlla parentesi e virgole.

### Controllo

- `X-Response-Time` compare su `/books`, `/authors`, `/login`, e su tutte le altre pagine.
- Il valore è un numero seguito da `ms`.

---

## 11.5 – Il middleware notturno (40 minuti)

### La richiesta

Crea con `php artisan make:middleware BlockNight` un middleware che risponde 403 con il messaggio "Di notte non si inseriscono libri." tra le 2:00 e le 4:59.

- L'ora corrente: `now()->hour`.
- Registralo con l'alias `notte` e applicalo **solo** alla creazione dei libri (`create` e `store`). Suggerimento: dopo `Route::resource(...)->except(...)` si può aggiungere `->middlewareFor(['create', 'store'], 'notte')`.
- Per provarlo senza aspettare la notte, cambia temporaneamente le ore nella condizione.
- Domanda: l'ora è quella giusta? Guarda `'timezone'` in `config/app.php`.

### Cosa ti serve

**La struttura di un middleware che blocca.**
Un middleware può decidere di **non** chiamare `$next($request)`.
In quel caso la richiesta non arriva al controller.

```php
public function handle(Request $request, Closure $next): Response
{
    if (now()->isWeekend()) {
        abort(403, 'Il sabato e la domenica il magazzino è chiuso.');
    }

    return $next($request);
}
```

`abort(codice, messaggio)` interrompe tutto e risponde con quel codice e quel messaggio.

**Un intervallo di ore.**
`now()->hour` è un numero intero da 0 a 23.
Le 4:59 hanno `hour` uguale a 4.
Scrivi la condizione con due confronti uniti da `&&`.

**L'alias.**
L'alias si registra come `tempo`, nello stesso array di `bootstrap/app.php`.

**`middlewareFor`.**
Applica un middleware solo ad alcune azioni di un resource.

### Passi

1. `php artisan make:middleware BlockNight`. Apri il file creato.
2. Scrivi la condizione e `abort`.
3. Registra l'alias in `bootstrap/app.php`.
4. In `routes/web.php` aggiungi `middlewareFor` alla riga del resource.
5. Controlla con `php artisan route:list --path=books -v`: la colonna dei middleware mostra `notte` solo su due rotte.
6. Cambia temporaneamente le ore per includere l'ora attuale. Apri "Nuovo libro". Poi apri la modifica di un tuo libro.
7. Rimetti le ore giuste.
8. Rispondi alla domanda sull'ora.

### Trappole

- Se dimentichi `return $next($request);` alla fine, nessuna pagina che usa il middleware funziona.
- `now()->hour <= 5` include anche le 5:00-5:59. La richiesta dice fino alle 4:59.
- Il punto e virgola: `middlewareFor` va scritto prima del `;` della riga del resource.
- `middlewareFor` vuole il **nome dell'azione** (`create`, `store`), non il nome della rotta (`books.create`).

### Domanda: dove guardare

- Apri `config/app.php` e cerca `'timezone'`. Leggi il valore. Se legge una variabile del `.env`, controlla se nel `.env` c'è.
- In tinker scrivi `now()` e confronta con l'orologio del computer.

### Controllo

- Con le ore di prova: "Nuovo libro" risponde 403 con il messaggio.
- Con le ore di prova: la modifica di un libro funziona.
- Con le ore giuste: "Nuovo libro" funziona.

---

## 11.6 – Domande sull'autenticazione (20 minuti)

### La richiesta

Rispondi per iscritto, dopo aver provato.

1. Da utente non autenticato apri `/books/create`: dove vieni mandato? Dopo il login, dove arrivi? Quale metodo di `AuthController` lo decide?
2. Registra un secondo utente e apri `/books/1/edit`: che risposta ottieni? Da quale riga di codice viene?
3. Sempre con il secondo utente, nella pagina del libro 1 non ci sono i pulsanti Modifica ed Elimina. Basta questo per proteggere il libro? Perché?
4. Da utente autenticato apri `/login`: cosa succede, e quale middleware lo fa?

### Cosa ti serve

**I due middleware dell'autenticazione.**
- `auth`: lascia passare solo chi ha fatto il login.
- `guest`: lascia passare solo chi **non** ha fatto il login.

Guarda in `routes/web.php` quali rotte sono in ciascun gruppo.

**Per vedere tutte le pagine da ospite e da utente.**
Usa due browser, oppure una finestra normale e una in incognito.

### Domande: dove guardare

- **Punto 1**: guarda l'indirizzo nella barra del browser dopo ogni passaggio. In `AuthController` leggi il `return` del metodo che gestisce il login: c'è una parola che ha a che fare con "dove volevi andare".
- **Punto 2**: guarda il metodo `edit` di `BookController` e il metodo privato che chiama.
- **Punto 3**: rileggi il commento sopra `@auth` in `books/show.blade.php`. Ripensa alla 7.2 del giorno 7: cosa può scrivere un utente nella barra degli indirizzi?
- **Punto 4**: guarda in quale gruppo di `web.php` è la rotta `/login`.

### Trappole

- Al punto 2 il libro 1 appartiene all'utente demo (lo ha creato il seeder). Il secondo utente non è il proprietario.
- Se sei loggato come demo, al punto 2 il libro si apre: devi essere loggato come secondo utente.

### Controllo

Hai quattro risposte scritte, ognuna con quello che hai visto e il file o la riga che lo produce.

---

## Extra – La copertina (capitolo 11 della dispensa)

### La richiesta

Per chi ha finito. Segui il capitolo 11:

1. `php artisan storage:link`;
2. il campo `cover` in `_form`, `enctype="multipart/form-data"` nei form di creazione e modifica, la regola `cover` in `BookRequest`;
3. salvataggio in `store`, sostituzione in `update`, cancellazione in `destroy`;
4. il metodo `coverUrl()` nel model `Book`, l'immagine nella pagina del libro e nell'elenco.

Poi le prove del paragrafo 11.5: un file di testo rinominato in `.png` deve essere rifiutato, e eliminando un libro deve sparire anche la sua copertina da `storage/app/public/covers`.
Se le immagini non si vedono: esiste `public/storage`? `APP_URL` nel file `.env` corrisponde all'indirizzo usato nel browser?

### Cosa ti serve

**Dove finiscono i file.**
Laravel salva i file caricati in `storage/app/public/`.
Il browser però vede solo la cartella `public/`.
`php artisan storage:link` crea in `public/` un collegamento chiamato `storage` che punta a `storage/app/public/`.

**Confronto con il giorno 7.**
Sono le stesse quattro regole di `upload.php` (esercizio 7.5): esito, dimensione, tipo dal contenuto, nome casuale.
In Laravel le prime tre si scrivono come regole di validazione. La quarta la fa Laravel quando salva il file.

### Passi

Segui il capitolo 11 della dispensa, nell'ordine dei quattro punti.
Prova dopo ogni punto.

### Trappole

- Senza `enctype="multipart/form-data"` il file non arriva e la regola del file non scatta, se è facoltativa.
- La colonna nel database si chiama `cover_path` (guarda la migration dei libri del giorno 8). Non è nel `#[Fillable]` di `Book`: va impostata a mano, come `user_id`.
- Se `APP_URL` è `http://localhost` e nel browser usi `http://127.0.0.1:8000`, gli indirizzi delle immagini sono sbagliati.
- Se hai spostato il progetto dopo `storage:link`, il collegamento punta alla vecchia cartella: cancella `public/storage` e rifai il comando.

### Controllo

- La copertina compare nella pagina del libro e nell'elenco.
- Un file di testo rinominato `.png` viene rifiutato con un messaggio.
- Modificando la copertina, il file vecchio sparisce da `storage/app/public/covers`.
- Eliminando il libro, il suo file sparisce.

---

## Errori frequenti

| Messaggio o sintomo | Causa probabile |
|---|---|
| `419 Page Expired` | Manca `@csrf` nel form, oppure la sessione è scaduta. Ricarica la pagina del form. |
| `403 This action is unauthorized.` su ogni invio del form | `authorize()` in `BookRequest` restituisce `false`. |
| `403 Non puoi modificare un libro inserito da un altro utente.` | Sei loggato con un utente che non è il proprietario. È il comportamento atteso. |
| Dopo un errore di validazione i campi sono vuoti | `old('...')` ha un nome diverso dal `name` del campo. |
| L'errore non compare accanto al campo | `@error('...')` ha un nome diverso dal `name` del campo. |
| `Undefined variable $book` in `_form.blade.php` | Il controller non passa `book` alla vista `create` o `edit`. |
| `The xxx field must be ...` in inglese | Manca il messaggio in `messages()`. I file di lingua non sono installati. |
| `Route [login] not defined` | La rotta del login non ha il nome `login`. Il middleware `auth` lo cerca. |
| Dopo il login arrivi sempre all'elenco | Normale se prima non avevi chiesto una pagina riservata. |
| `Target class [notte] does not exist` | L'alias non è registrato in `bootstrap/app.php`, oppure è scritto diversamente. |
| Il middleware blocca tutte le pagine | Manca `return $next($request);`, oppure è stato registrato sul gruppo `web` invece che come alias. |
| `Call to undefined method ...::middlewareFor()` | La versione di Laravel non ha questo metodo, oppure è chiamato sull'oggetto sbagliato. Controlla `php artisan --version`. |
| L'ora del middleware è sbagliata di una o due ore | Il fuso orario dell'applicazione non è quello italiano. |
| L'immagine della copertina non si vede | Manca `public/storage` (`php artisan storage:link`), oppure `APP_URL` è diverso dall'indirizzo del browser. |
| Qualsiasi errore in `bootstrap/app.php` | Nessuna pagina funziona. Leggi il messaggio nel terminale del server. |
