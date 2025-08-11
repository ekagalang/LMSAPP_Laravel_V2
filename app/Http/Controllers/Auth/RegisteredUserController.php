<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Ensure participant role exists before assigning
            $participantRole = Role::firstOrCreate([
                'name' => 'participant',
                'guard_name' => 'web'
            ]);

            $user->assignRole('participant');

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['registration' => 'Registration failed. Please try again.']);
        }
    }
}