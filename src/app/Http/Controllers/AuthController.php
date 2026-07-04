<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->assignRole('user');

        return redirect('/login')->with('success', 'Register berhasil');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id')) || config('services.google.client_id') === 'dummy-client-id') {
            return redirect()->route('auth.google.mock');
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('mock') && $request->mock === 'true') {
            $email = $request->email;
            $name = $request->name;
            $googleId = 'mock_' . md5($email);
            $avatarUrl = $request->avatar_url ?? null;
        } else {
            try {
                $googleUser = Socialite::driver('google')->user();
                $email = $googleUser->getEmail();
                $name = $googleUser->getName();
                $googleId = $googleUser->getId();
                $avatarUrl = $googleUser->getAvatar();
            } catch (\Exception $e) {
                return redirect('/login')->withErrors([
                    'email' => 'Failed to connect to Google: ' . $e->getMessage(),
                ]);
            }
        }

        // Check if user exists
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleId,
                ]);
            }
        } else {
            // Generate a unique username
            $baseUsername = strtolower(str_replace(' ', '', $name));
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'google_id' => $googleId,
                'avatar_url' => $avatarUrl,
            ]);

            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
            $user->assignRole('user');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return view('auth.google_callback_success');
    }
}