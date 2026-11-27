<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Autenticazione scritta a mano, senza starter kit:
 * piu' corta e leggibile per chi inizia. Confrontala con
 * 02-php-puro/public/login.php: i passaggi sono gli stessi.
 */
class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],   // confirmed = password_confirmation
        ]);

        // Il cast 'password' => 'hashed' nel model User applica l'hash automaticamente
        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('books.index')->with('success', 'Registrazione completata.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt cerca l'utente per email e fa password_verify sull'hash
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();     // contro il session fixation

            return redirect()->intended(route('books.index'));
        }

        return back()
            ->withErrors(['email' => 'Credenziali non valide.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();    // nuovo token CSRF

        return redirect()->route('books.index');
    }
}
