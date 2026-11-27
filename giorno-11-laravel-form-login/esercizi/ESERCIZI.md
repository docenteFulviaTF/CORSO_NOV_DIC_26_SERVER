# Giorno 11 – Esercizi del pomeriggio

Si lavora sul progetto `bookshelf` come è alla fine della mattina.
Chi è rimasto indietro copia i file di `lezione/bookshelf/` nel proprio progetto
e lancia `php artisan migrate:fresh --seed`.

## 11.1 – Il form completo (30 minuti)

In `resources/views/books/_form.blade.php`:
1. aggiungi i campi Anno, Pagine e ISBN, ognuno con `old()` e `@error`
   (Anno e Pagine con `type="number"`);
2. sopra i campi, un riepilogo di tutti gli errori con `$errors->any()` e `$errors->all()`.

Prova: crea un libro con anno, pagine e ISBN, poi aprilo in modifica: i tre campi devono
essere già compilati.

## 11.2 – Mettere alla prova la validazione (30 minuti)

Rispondi per iscritto.
1. Invia il form con un ISBN di 12 cifre. Quale messaggio compare?
2. Con gli strumenti per sviluppatori (F12, scheda Elementi) cambia il `value` di
   un'opzione del menu Genere in `999`, sceglila e invia. Quale messaggio compare?
   Perché il controllo sul server serve anche se il menu offre solo generi esistenti?
3. Crea due libri con lo stesso ISBN. Poi modifica il primo senza cambiare l'ISBN:
   perché in modifica non compare l'errore? Quale riga di `BookRequest` lo evita?
4. Togli `@csrf` dal form e invialo. Quale codice di errore ottieni? Rimetti `@csrf`.

## 11.3 – Messaggi in italiano (30 minuti)

In `BookRequest`:
1. aggiungi in `messages()` i messaggi per `isbn.digits`, `year.max` e `genre_id.exists`;
2. aggiungi il metodo `attributes()` che traduce i nomi dei campi:
   `title` → titolo, `year` → anno, `pages` → pagine, `genre_id` → genere.

Prova scrivendo `abc` nelle pagine: il messaggio diventa "The pagine field must be an integer.".
Il nome del campo è tradotto, il resto no: per tradurre tutti i messaggi di Laravel servono
i file di lingua, che non facciamo.

## 11.4 – Il middleware su tutte le pagine (20 minuti)

Registra `AddResponseTime` sul gruppo `web` in `bootstrap/app.php` (paragrafo 10.2) e togli
`->middleware('tempo')` dalla rotta `/books`. Verifica nella scheda Rete che l'intestazione
`X-Response-Time` compaia anche su `/authors` e su `/login`.

## 11.5 – Il middleware notturno (40 minuti)

Crea con `php artisan make:middleware BlockNight` un middleware che risponde 403 con il
messaggio "Di notte non si inseriscono libri." tra le 2:00 e le 4:59.
- L'ora corrente: `now()->hour`.
- Registralo con l'alias `notte` e applicalo **solo** alla creazione dei libri
  (`create` e `store`). Suggerimento: dopo `Route::resource(...)->except(...)` si può
  aggiungere `->middlewareFor(['create', 'store'], 'notte')`.
- Per provarlo senza aspettare la notte, cambia temporaneamente le ore nella condizione.
- Domanda: l'ora è quella giusta? Guarda `'timezone'` in `config/app.php`.

## 11.6 – Domande sull'autenticazione (20 minuti)

Rispondi per iscritto, dopo aver provato.
1. Da utente non autenticato apri `/books/create`: dove vieni mandato? Dopo il login,
   dove arrivi? Quale metodo di `AuthController` lo decide?
2. Registra un secondo utente e apri `/books/1/edit`: che risposta ottieni? Da quale riga
   di codice viene?
3. Sempre con il secondo utente, nella pagina del libro 1 non ci sono i pulsanti Modifica
   ed Elimina. Basta questo per proteggere il libro? Perché?
4. Da utente autenticato apri `/login`: cosa succede, e quale middleware lo fa?

## Extra – La copertina (capitolo 11 della dispensa)

Per chi ha finito. Segui il capitolo 11:
1. `php artisan storage:link`;
2. il campo `cover` in `_form`, `enctype="multipart/form-data"` nei form di creazione e
   modifica, la regola `cover` in `BookRequest`;
3. salvataggio in `store`, sostituzione in `update`, cancellazione in `destroy`;
4. il metodo `coverUrl()` nel model `Book`, l'immagine nella pagina del libro e
   nell'elenco.

Poi le prove del paragrafo 11.5: un file di testo rinominato in `.png` deve essere rifiutato,
e eliminando un libro deve sparire anche la sua copertina da `storage/app/public/covers`.
Se le immagini non si vedono: esiste `public/storage`? `APP_URL` nel file `.env` corrisponde
all'indirizzo usato nel browser?
