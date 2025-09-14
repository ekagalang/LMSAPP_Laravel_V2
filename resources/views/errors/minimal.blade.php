<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'LMS Portal') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>
<body class="antialiased">
    @yield('content')

    <!-- Additional Scripts -->
    <script>
        // Prevent back button cache issues
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Console message for developers
        console.log('%c🛡️ LMS Portal - Error Handler', 'color: #6366f1; font-size: 14px; font-weight: bold;');
        console.log('%cError Code: @yield("code")', 'color: #ef4444; font-size: 12px;');
        console.log('%cMessage: @yield("message")', 'color: #6b7280; font-size: 12px;');
    </script>
</body>
</html>