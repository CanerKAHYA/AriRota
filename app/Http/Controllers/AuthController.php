<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function createLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'E-posta veya şifre hatalı.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function createRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['nullable', 'in:beekeeper,admin'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'beekeeper',
            'city' => $data['city'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('profile.edit')->with('success', 'Hesabınız oluşturuldu. Profilinizi tamamlayabilirsiniz.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('dashboard');
    }
}
