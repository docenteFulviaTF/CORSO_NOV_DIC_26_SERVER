<x-layout title="Accedi">
    <form method="POST" action="{{ route('login') }}" class="stack">
        @csrf
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <label style="display:flex; gap:.5rem; font-weight:400">
            <input type="checkbox" name="remember"> Ricordami
        </label>
        <button type="submit">Accedi</button>
    </form>
    <p>Utente di prova: demo@example.com / password</p>
</x-layout>
