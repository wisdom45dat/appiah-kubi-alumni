<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $graduationYears = range(1970, date('Y'));
        rsort($graduationYears);
        return view('auth.register', compact('graduationYears'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'graduation_year' => 'required|digits:4',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'graduation_year' => $validated['graduation_year'],
            'phone' => $validated['phone'] ?? null,
            'is_verified' => false,
        ]);

        // Assign alumni role
        $user->assignRole('alumni');

        Auth::login($user);

        return redirect('/dashboard');
    }
}
