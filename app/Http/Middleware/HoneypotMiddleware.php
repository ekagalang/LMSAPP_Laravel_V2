<?php
// app/Http/Middleware/HoneypotMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HoneypotMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek honeypot fields - jika ada yang terisi, kemungkinan bot
        $honeypotFields = ['website', 'url', 'company_url', 'phone_number'];
        
        foreach ($honeypotFields as $field) {
            if (!empty($request->input($field))) {
                // Log bot activity
                \Log::warning('Honeypot triggered', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'field' => $field,
                    'value' => $request->input($field),
                    'url' => $request->url()
                ]);
                
                // Redirect ke halaman 404 untuk menyembunyikan bahwa kita mendeteksi bot
                abort(404);
            }
        }
        
        return $next($request);
    }
}