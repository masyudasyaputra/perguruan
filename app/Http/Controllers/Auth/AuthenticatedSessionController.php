<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $passwordInput = $request->password;

        // 1. Tentukan field: Email atau WhatsApp
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'whatsapp';

        // 2. Normalisasi nomor WhatsApp
        if ($fieldType == 'whatsapp') {
            $login = preg_replace('/[^0-9]/', '', $login);
            if (str_starts_with($login, '62')) {
                $login = '0' . substr($login, 2);
            }
        }

        // 3. Coba login standar (Password Akun)
        if (Auth::attempt([$fieldType => $login, 'password' => $passwordInput], $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Ganti '/dashboard' sesuai dengan rute setelah login Anda
            return redirect()->intended('/dashboard'); 
        }

        // 4. Cek Password Default Dojo jika login standar gagal
        $user = User::where($fieldType, $login)->first();

        if ($user && $user->dojo) {
            $defaultDojoPassword = $user->dojo->default_password; 

            if ($defaultDojoPassword && $passwordInput === $defaultDojoPassword) {
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }

        throw ValidationException::withMessages([
            'login' => __('auth.failed'),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}