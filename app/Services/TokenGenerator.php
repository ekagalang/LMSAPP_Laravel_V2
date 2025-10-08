<?php

namespace App\Services;

use Illuminate\Support\Str;

class TokenGenerator
{
    /**
     * Generate random token
     *
     * @param int $length
     * @param string $format (alphanumeric|numeric|alpha)
     * @return string
     */
    public static function generateRandom(int $length = 8, string $format = 'alphanumeric'): string
    {
        switch ($format) {
            case 'numeric':
                return self::generateNumeric($length);
            case 'alpha':
                return strtoupper(Str::random($length));
            case 'alphanumeric':
            default:
                return strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, $length));
        }
    }

    /**
     * Generate numeric only token
     */
    private static function generateNumeric(int $length): string
    {
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= random_int(0, 9);
        }
        return $token;
    }

    /**
     * Validate custom token format
     *
     * @param string $token
     * @param int $minLength
     * @param int $maxLength
     * @return bool
     */
    public static function validateCustomToken(string $token, int $minLength = 4, int $maxLength = 20): bool
    {
        $token = strtoupper(trim($token));
        $length = strlen($token);

        // Check length
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }

        // Allow alphanumeric and dash only
        if (!preg_match('/^[A-Z0-9\-]+$/', $token)) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize custom token
     */
    public static function sanitizeCustomToken(string $token): string
    {
        // Convert to uppercase and trim
        $token = strtoupper(trim($token));

        // Remove invalid characters (only keep alphanumeric and dash)
        $token = preg_replace('/[^A-Z0-9\-]/', '', $token);

        return $token;
    }

    /**
     * Generate token with prefix
     *
     * @param string $prefix
     * @param int $randomLength
     * @return string
     */
    public static function generateWithPrefix(string $prefix, int $randomLength = 6): string
    {
        $random = self::generateRandom($randomLength);
        return strtoupper($prefix) . '-' . $random;
    }

    /**
     * Generate token with date pattern
     *
     * @param string $prefix
     * @param string $dateFormat (Y|Ym|Ymd)
     * @param int $randomLength
     * @return string
     */
    public static function generateWithDate(string $prefix, string $dateFormat = 'Y', int $randomLength = 4): string
    {
        $datePart = match($dateFormat) {
            'Ym' => date('Ym'),
            'Ymd' => date('Ymd'),
            default => date('Y'),
        };

        $random = self::generateRandom($randomLength);
        return strtoupper($prefix) . '-' . $datePart . '-' . $random;
    }

    /**
     * Check if token exists in model
     *
     * @param string $modelClass
     * @param string $token
     * @param string $column
     * @return bool
     */
    public static function tokenExists(string $modelClass, string $token, string $column = 'enrollment_token'): bool
    {
        return $modelClass::where($column, $token)->exists();
    }

    /**
     * Generate unique random token for model
     *
     * @param string $modelClass
     * @param int $length
     * @param string $format
     * @param string $column
     * @param int $maxAttempts
     * @return string
     * @throws \Exception
     */
    public static function generateUniqueRandom(
        string $modelClass,
        int $length = 8,
        string $format = 'alphanumeric',
        string $column = 'enrollment_token',
        int $maxAttempts = 10
    ): string {
        $attempts = 0;

        do {
            $token = self::generateRandom($length, $format);
            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \Exception('Unable to generate unique token after ' . $maxAttempts . ' attempts');
            }
        } while (self::tokenExists($modelClass, $token, $column));

        return $token;
    }

    /**
     * Validate and ensure unique custom token
     *
     * @param string $modelClass
     * @param string $customToken
     * @param string $column
     * @param int|null $excludeId
     * @return array ['valid' => bool, 'message' => string, 'token' => string]
     */
    public static function validateUniqueCustomToken(
        string $modelClass,
        string $customToken,
        string $column = 'enrollment_token',
        ?int $excludeId = null
    ): array {
        $sanitized = self::sanitizeCustomToken($customToken);

        // Validate format
        if (!self::validateCustomToken($sanitized)) {
            return [
                'valid' => false,
                'message' => 'Token harus 4-20 karakter, hanya huruf, angka, dan dash (-)',
                'token' => $sanitized
            ];
        }

        // Check uniqueness
        $query = $modelClass::where($column, $sanitized);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            return [
                'valid' => false,
                'message' => 'Token sudah digunakan, silakan gunakan token lain',
                'token' => $sanitized
            ];
        }

        return [
            'valid' => true,
            'message' => 'Token valid dan tersedia',
            'token' => $sanitized
        ];
    }
}
