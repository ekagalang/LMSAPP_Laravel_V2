<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'LMS App') }} @hasSection('title') - @yield('title') @endif</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <!-- Custom Navbar Styles -->
    <style>
        .nav-link-custom {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            padding-top: 0.25rem;
            border-bottom: 2px solid transparent;
            font-size: 1.125rem !important;
            font-weight: 600 !important;
            line-height: 1.25;
            transition: all 0.15s ease-in-out;
            color: #000000 !important;
            text-decoration: none;
        }

        .nav-link-custom:hover {
            color: #7f1d1d !important;
            border-bottom-color: #fca5a5 !important;
        }

        .nav-link-custom.active {
            color: #7f1d1d !important;
            border-bottom-color: #7f1d1d !important;
        }

        .dropdown-trigger-custom {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border: 1px solid transparent;
            font-size: 1rem !important;
            font-weight: 600 !important;
            border-radius: 0.375rem;
            color: #000000 !important;
            background-color: #ffffff;
            transition: all 0.15s ease-in-out;
        }

        .dropdown-trigger-custom:hover {
            color: #7f1d1d !important;
        }

        .dropdown-item-custom {
            display: block;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: #374151 !important;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .dropdown-item-custom:hover {
            background-color: #fef2f2 !important;
            color: #7f1d1d !important;
        }

        .responsive-nav-link-custom {
            display: block;
            padding-left: 0.75rem;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border-left: 4px solid transparent;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #000000 !important;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .responsive-nav-link-custom:hover {
            color: #7f1d1d !important;
            background-color: #fef2f2 !important;
            border-left-color: #fca5a5 !important;
        }

        .responsive-nav-link-custom.active {
            color: #7f1d1d !important;
            background-color: #fef2f2 !important;
            border-left-color: #7f1d1d !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ open: false }" class="min-h-screen">
        {{-- ✅ PEROMBAKAN TOTAL BAGIAN NAVIGASI --}}
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex items-center">
                            <a href="{{ route('dashboard') }}">
                                <img src="{{ asset('images/logo.png') }}" 
                                    alt="LMS APP Logo" 
                                    class="h-14 w-auto">
                            </a>
                        </div>

                        {{-- Links Navigasi Dinamis --}}
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

                            @auth
                            <a href="{{ route('dashboard') }}" 
                               class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                {{ __('Dashboard') }}
                            </a>
                            @endauth

                            {{-- Menu "Kelola Kursus" untuk Instructor (dan Admin) --}}
                            @can('view courses')
                                <a href="{{ route('courses.index') }}" 
                                   class="nav-link-custom {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                                    {{ __('Kelola Kursus') }}
                                </a>
                            @endcan

                            {{-- Menu "Pemantauan" untuk Event Organizer (dan Admin) --}}
                            @can('view progress reports')
                                <a href="{{ route('eo.courses.index') }}" 
                                class="nav-link-custom {{ request()->routeIs('eo.*') ? 'active' : '' }}">
                                    {{ __('Pemantauan Kursus') }}
                                </a>
                                <a href="{{ route('instructor-analytics.index') }}" 
                                class="nav-link-custom {{ request()->routeIs('instructor-analytics.*') ? 'active' : '' }}">
                                    {{ __('Analytics Instruktur') }}
                                </a>
                            @endcan

                            {{-- Menu Admin berbasis izin --}}
                            @canany(['manage users','manage roles','view certificate templates','view activity logs','view certificate analytics','view certificate management'])
                                <div class="hidden sm:flex sm:items-center" x-data="{ adminOpen: false }">
                                    <div class="relative">
                                        <button @click="adminOpen = ! adminOpen" 
                                                class="dropdown-trigger-custom">
                                            <span>Admin Menu</span>
                                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div x-show="adminOpen" 
                                             @click.away="adminOpen = false"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 z-50 mt-2 w-60 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                                             style="display: none;">
                                            <div class="py-1">
                                                <a href="{{ route('admin.users.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Manajemen Pengguna') }}
                                                </a>
                                                <a href="{{ route('admin.roles.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Manajemen Peran') }}
                                                </a>
                                                <a href="{{ route('admin.announcements.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Manajemen Pengumuman') }}
                                                </a>
                                                <a href="{{ route('admin.participants.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Analitik Peserta') }}
                                                </a>
                                                <div class="border-t my-1"></div>
                                                <a href="{{ route('admin.certificate-templates.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Certificate Template') }}
                                                </a>
                                                <a href="{{ route('certificate-management.index') }}" 
                                                    class="dropdown-item-custom">
                                                    {{ __('Manajemen Sertifikat') }}
                                                </a>
                                                <div class="border-t my-1"></div>
                                                <a href="{{ route('admin.auto-grade.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Penyelesaian Penilaian Otomatis') }}
                                                </a>
                                                <a href="{{ route('admin.force-complete.index') }}" 
                                                   class="dropdown-item-custom">
                                                    {{ __('Force Complete Konten') }}
                                                </a>
                                                <div class="border-t my-1"></div>
                                                <a href="{{ route('file-control.index') }}"
                                                    class="dropdown-item-custom">
                                                    {{ __('File Manager') }}
                                                </a>
                                                <a href="{{ route('activity-logs.index') }}"
                                                    class="dropdown-item-custom">
                                                    {{ __('Log') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcanany
                        </div>
                    </div>

                    {{-- Dropdown Pengaturan Pengguna --}}
                    <div class="hidden sm:flex sm:items-center sm:ml-6">

                        @auth
                            {{-- TAMPILKAN DROPDOWN HANYA JIKA PENGGUNA SUDAH LOGIN --}}
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            {{-- JIKA PENGGUNA ADALAH TAMU (TIDAK LOGIN), TAMPILKAN LINK LOGIN & REGISTER --}}
                            <div class="space-x-4">
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Log in</a>
                                <a href="{{ route('register') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Register</a>
                            </div>
                        @endauth

                    </div>

                    {{-- Tombol Hamburger untuk Mobile --}}
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Menu Navigasi Mobile --}}
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    @auth
                    <a href="{{ route('dashboard') }}" 
                       class="responsive-nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                    @endauth

                    @can('view courses')
                        <a href="{{ route('courses.index') }}" 
                           class="responsive-nav-link-custom {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                            {{ __('Kelola Kursus') }}
                        </a>
                    @endcan

                    {{-- Menu "Pemantauan" untuk Event Organizer (dan Admin) --}}
                    @can('view progress reports')
                        <a href="{{ route('eo.courses.index') }}" 
                           class="nav-link-custom {{ request()->routeIs('eo.*') ? 'active' : '' }}">
                            {{ __('Pemantauan Kursus') }}
                        </a>
                        <a href="{{ route('instructor-analytics.index') }}" 
                        class="nav-link-custom {{ request()->routeIs('instructor-analytics.*') ? 'active' : '' }}">
                            {{ __('Analytics Instruktur') }}
                        </a>
                    @endcan

                    @canany(['manage users','manage roles','view certificate templates','view activity logs','view certificate analytics','view certificate management'])
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="px-4">
                                <div class="font-semibold text-base text-gray-800">Admin Menu</div>
                            </div>
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('admin.users.index') }}" 
                                   class="responsive-nav-link-custom {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    {{ __('Manajemen Pengguna') }}
                                </a>
                                <a href="{{ route('admin.roles.index') }}" 
                                   class="responsive-nav-link-custom {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                    {{ __('Manajemen Peran') }}
                                </a>
                                <a href="{{ route('admin.announcements.index') }}" 
                                   class="responsive-nav-link-custom">
                                    {{ __('Manajemen Pengumuman') }}
                                </a>
                                <a href="{{ route('admin.certificate-templates.index') }}" 
                                   class="responsive-nav-link-custom">
                                    {{ __('Certificate Template') }}
                                </a>
                                <a href="{{ route('certificate-management.index') }}" 
                                    class="responsive-nav-link-custom">
                                    {{ __('Manajemen Sertifikat') }}
                                </a>
                                <a href="{{ route('admin.auto-grade.index') }}" 
                                   class="responsive-nav-link-custom">
                                    {{ __('Penyelesaian Penilaian Otomatis') }}
                                </a>
                                <a href="{{ route('admin.force-complete.index') }}" 
                                   class="responsive-nav-link-custom">
                                    {{ __('Force Complete Konten') }}
                                </a>
                                <a href="{{ route('file-control.index') }}"
                                    class="responsive-nav-link-custom">
                                    {{ __('File Manager') }}
                                </a>
                                <a href="{{ route('activity-logs.index') }}"
                                    class="responsive-nav-link-custom">
                                    {{ __('Log') }}
                                </a>
                            </div>
                        </div>
                    @endcanany
                </div>

                {{-- Mobile User Menu --}}
                @auth
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.edit') }}" 
                           class="responsive-nav-link-custom">
                            {{ __('Profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="responsive-nav-link-custom">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </nav>

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot }}
            @endif
        </main>
    </div>

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" xintegrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>

        <!-- Summernote JS -->
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        @stack('scripts')
</body>
</html>
