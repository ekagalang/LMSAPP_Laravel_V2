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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
        // 🍯 Cek honeypot terlebih dahulu sebelum validasi lainnya
        $this->checkHoneypot($request);
        
        // 🤖 Cek user agent yang mencurigakan
        $this->checkSuspiciousUserAgent($request);

        // Validasi normal + honeypot fields
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'], // Jika menggunakan terms checkbox
            // 🍯 Honeypot validations - harus kosong
            'website' => ['nullable', 'max:0'],
            'company_url' => ['nullable', 'max:0'],
            'phone_number' => ['nullable', 'max:0'],
            'username' => ['nullable', 'max:0'],
        ]);

        // Jika sampai di sini, berarti lolos semua validasi dan honeypot
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role participant seperti yang sudah ada
        $user->assignRole('participant');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * 🍯 Check honeypot fields untuk mendeteksi bot
     */
    protected function checkHoneypot(Request $request): void
    {
        $honeypotFields = ['website', 'company_url', 'phone_number', 'username'];
        
        foreach ($honeypotFields as $field) {
            if (!empty($request->input($field))) {
                Log::warning('🍯 Honeypot triggered pada register', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'field' => $field,
                    'value' => $request->input($field),
                    'email' => $request->input('email'),
                    'name' => $request->input('name'),
                    'timestamp' => now()
                ]);
                
                // Berikan response normal untuk menyembunyikan deteksi bot
                throw ValidationException::withMessages([
                    'email' => 'The email address is already taken.',
                ]);
            }
        }
    }

    /**
     * 🤖 Check for suspicious user agents
     */
    protected function checkSuspiciousUserAgent(Request $request): void
    {
        $userAgent = $request->header('User-Agent', '');
        
        $suspiciousAgents = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
            'python-requests', 'go-http-client', 'okhttp', 'postman',
            'insomnia', 'httpie', 'axios', 'node-fetch'
        ];
        
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                Log::warning('🤖 Suspicious user agent detected pada register', [
                    'ip' => $request->ip(),
                    'user_agent' => $userAgent,
                    'email' => $request->input('email'),
                    'name' => $request->input('name'),
                    'timestamp' => now()
                ]);
                
                throw ValidationException::withMessages([
                    'email' => 'The email address is already taken.',
                ]);
            }
        }
        
        // Cek user agent kosong atau terlalu pendek (bot sering begini)
        if (empty($userAgent) || strlen($userAgent) < 10) {
            Log::warning('🚫 Empty atau short user agent pada register', [
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'timestamp' => now()
            ]);
            
            throw ValidationException::withMessages([
                'email' => 'The email address is already taken.',
            ]);
        }
    }
}