@props(['title' => 'Bookshelf'])

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Bookshelf</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; margin: 0; color: #222; background: #f6f7f9; }
        header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;
                 gap: 1rem; padding: .8rem 1.2rem; background: #0b2e63; color: #fff; }
        header a, header span { color: #fff; text-decoration: none; margin-right: .8rem; }
        main { max-width: 900px; margin: 1.5rem auto; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: .5rem; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top; }
        form.stack { display: grid; gap: .8rem; max-width: 520px; }
        form.inline { display: inline; }
        label { display: grid; gap: .25rem; font-weight: 600; }
        input, select, button { font: inherit; padding: .45rem .6rem; }
        button, .btn { cursor: pointer; background: #1c4e8a; color: #fff; border: 0; border-radius: 4px;
                       padding: .45rem .9rem; text-decoration: none; display: inline-block; }
        .btn-danger { background: #b00020; }
        .error { color: #b00020; font-weight: 400; margin: 0; }
        .flash { padding: .6rem .8rem; border-radius: 4px; background: #e3f2e6; }
        .cover { width: 120px; }
        .mini { width: 40px; height: 56px; object-fit: cover; }
        nav[role=navigation] { margin-top: 1rem; }
    </style>
</head>
<body>
<header>
    <nav>
        <a href="{{ route('books.index') }}">Libri</a>
        <a href="{{ route('authors.index') }}">Autori</a>
    </nav>
</header>

<main>
    @if (session('success'))
        <p class="flash">{{ session('success') }}</p>
    @endif

    <h1>{{ $title }}</h1>
    {{ $slot }}
</main>
</body>
</html>
