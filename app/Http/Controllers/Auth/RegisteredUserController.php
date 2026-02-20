<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $provinces = \App\Models\Province::all();
        $beltLevels = \App\Models\BeltLevel::all();

        return view('auth.register', compact('provinces', 'beltLevels'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'province_id' => ['required'],
            'city_id' => ['required'],
            'dojo_id' => ['required'],
            'belt_level_id' => ['required', 'exists:belt_levels,id'], // Pastikan tabel belt_levels ada
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'dojo_id' => $request->dojo_id,
            'belt_level_id' => $request->belt_level_id, // Gunakan ID dari dropdown
            'role' => 'member',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
