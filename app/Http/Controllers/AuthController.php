<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credenciales = $request->only('email', 'password');
        $claveLimite = Str::lower($credenciales['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($claveLimite, 5)) {
            $segundos = RateLimiter::availableIn($claveLimite);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos fallidos. Intenta nuevamente en {$segundos} segundos.",
            ]);
        }

        if (! Auth::attempt($credenciales, $request->boolean('remember'))) {
            RateLimiter::hit($claveLimite, 60);

            throw ValidationException::withMessages([
                'email' => 'El correo o la contraseña no son correctos.',
            ]);
        }

        RateLimiter::clear($claveLimite);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Bienvenido, '.Auth::user()->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Tu sesión se cerró correctamente.');
    }
}
