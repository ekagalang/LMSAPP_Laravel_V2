<?php
// app/Http/Requests/Auth/RegisterRequest.php
// Jika belum ada, buat file ini. Jika sudah ada, update dengan honeypot

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
            // 🍯 Honeypot validations
            'website' => ['nullable', 'max:0'],
            'company_url' => ['nullable', 'max:0'],
            'phone_number' => ['nullable', 'max:0'],
            'username' => ['nullable', 'max:0'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 🍯 Cek honeypot
            $this->checkHoneypot($validator);
            
            // 🤖 Cek user agent mencurigakan
            $this->checkSuspiciousUserAgent($validator);
        });
    }

    /**
     * 🍯 Check honeypot fields
     */
    protected function checkHoneypot($validator): void
    {
        $honeypotFields = ['website', 'company_url', 'phone_number', 'username'];
        
        foreach ($honeypotFields as $field) {
            if (!empty($this->input($field))) {
                Log::warning('🍯 Honeypot triggered pada register', [
                    'ip' => $this->ip(),
                    'user_agent' => $this->header('User-Agent'),
                    'field' => $field,
                    'value' => $this->input($field),
                    'email' => $this->input('email'),
                    'name' => $this->input('name'),
                    'timestamp' => now()
                ]);
                
                // Tambahkan error ke field email agar tidak mencurigakan
                $validator->errors()->add('email', 'The email address is already taken.');
                break;
            }
        }
    }

    /**
     * 🤖 Check for suspicious user agents
     */
    protected function checkSuspiciousUserAgent($validator): void
    {
        $userAgent = $this->header('User-Agent', '');
        
        $suspiciousAgents = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
            'python-requests', 'go-http-client', 'okhttp', 'postman',
            'insomnia', 'httpie', 'axios', 'node-fetch'
        ];
        
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                Log::warning('🤖 Suspicious user agent detected pada register', [
                    'ip' => $this->ip(),
                    'user_agent' => $userAgent,
                    'email' => $this->input('email'),
                    'name' => $this->input('name'),
                    'timestamp' => now()
                ]);
                
                $validator->errors()->add('email', 'The email address is already taken.');
                break;
            }
        }
        
        // Cek user agent kosong atau terlalu pendek
        if (empty($userAgent) || strlen($userAgent) < 10) {
            Log::warning('🚫 Empty atau short user agent pada register', [
                'ip' => $this->ip(),
                'user_agent' => $userAgent,
                'email' => $this->input('email'),
                'name' => $this->input('name'),
                'timestamp' => now()
            ]);
            
            $validator->errors()->add('email', 'The email address is already taken.');
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.unique' => 'Email sudah terdaftar.',
        ];
    }
}