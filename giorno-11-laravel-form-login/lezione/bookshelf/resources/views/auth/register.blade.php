<x-layout title="Registrati">
    <form method="POST" action="{{ route('register') }}" class="stack">
        @csrf
        <label>Nome
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Password (almeno 8 caratteri)
            <input type="password" name="password" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </label>
        {{-- La regola "confirmed" cerca un campo chiamato password_confirmation --}}
        <label>Conferma password
            <input type="password" name="password_confirmation" required>
        </label>
        <button type="submit">Registrati</button>
    </form>
</x-layout>
