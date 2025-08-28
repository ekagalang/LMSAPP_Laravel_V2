<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            // 🍯 Honeypot validations - harus kosong
            'website' => ['nullable', 'max:0'],
            'company_url' => ['nullable', 'max:0'],
            'phone_number' => ['nullable', 'max:0'],
            'username' => ['nullable', 'max:0'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // 🍯 Cek honeypot terlebih dahulu
        $this->checkHoneypot();
        
        // 🤖 Cek user agent yang mencurigakan
        $this->checkSuspiciousUserAgent();
        
        // ⏰ Cek rate limiting (tetap ada tapi lebih longgar)
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * 🍯 Check honeypot fields - method utama untuk deteksi bot
     */
    protected function checkHoneypot(): void
    {
        $honeypotFields = ['website', 'company_url', 'phone_number', 'username'];
        
        foreach ($honeypotFields as $field) {
            if (!empty($this->input($field))) {
                Log::warning('🍯 Honeypot triggered pada login', [
                    'ip' => $this->ip(),
                    'user_agent' => $this->header('User-Agent'),
                    'field' => $field,
                    'value' => $this->input($field),
                    'email' => $this->input('email'),
                    'timestamp' => now()
                ]);
                
                // Berikan response normal untuk menyembunyikan deteksi bot
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }
        }
    }

    /**
     * 🤖 Check for suspicious user agents
     */
    protected function checkSuspiciousUserAgent(): void
    {
        $userAgent = $this->header('User-Agent', '');
        
        // Daftar user agent yang mencurigakan
        $suspiciousAgents = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
            'python-requests', 'go-http-client', 'okhttp', 'postman',
            'insomnia', 'httpie', 'axios', 'node-fetch'
        ];
        
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                Log::warning('🤖 Suspicious user agent detected pada login', [
                    'ip' => $this->ip(),
                    'user_agent' => $userAgent,
                    'email' => $this->input('email'),
                    'timestamp' => now()
                ]);
                
                // Berikan response normal untuk menyembunyikan deteksi
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }
        }
        
        // Cek jika user agent kosong atau terlalu pendek (bot sering begini)
        if (empty($userAgent) || strlen($userAgent) < 10) {
            Log::warning('🚫 Empty atau short user agent pada login', [
                'ip' => $this->ip(),
                'user_agent' => $userAgent,
                'email' => $this->input('email'),
                'timestamp' => now()
            ]);
            
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     * Rate limiting tetap ada tapi lebih longgar untuk user normal
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Naikkan limit dari 5 ke 10 attempts untuk mengakomodasi sosialisasi
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}