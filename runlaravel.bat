@echo off
title Laravel Multi Service Runner
echo ================================
echo   Laravel Service Starter
echo ================================
echo.

:: Jalankan Laravel server
start cmd /k "title PHP Artisan Serve & php artisan serve"

:: Jalankan Laravel Reverb
start cmd /k "title PHP Artisan Reverb & php artisan reverb:start"

:: Jalankan Queue Worker
start cmd /k "title PHP Artisan Queue Worker & php artisan queue:work"

:: Jalankan NPM Dev
start cmd /k "title NPM Dev & npm run dev"

echo Semua service sudah dijalankan di jendela terpisah.
pause
