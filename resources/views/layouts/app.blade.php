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
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ open: false }" class="min-h-screen">
        {{-- ✅ PEROMBAKAN TOTAL BAGIAN NAVIGASI --}}
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600">LMS APP</a>
                        </div>

                        {{-- Links Navigasi Dinamis --}}
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            
                            {{-- Menu "Kelola Kursus" untuk Instructor (dan Admin) --}}
                            @can('view courses')
                                <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                                    {{ __('Kelola Kursus') }}
                                </x-nav-link>
                            @endcan

                            {{-- Menu "Pemantauan" untuk Event Organizer (dan Admin) --}}
                             @can('view progress reports')
                                <x-nav-link :href="route('eo.courses.index')" :active="request()->routeIs('eo.*')">
                                    {{ __('Pemantauan Kursus') }}
                                </x-nav-link>
                            @endcan
                            
                            {{-- Menu khusus Super Admin --}}
                            @role('super-admin')
                                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    {{ __('Manajemen Pengguna') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                                    {{ __('Manajemen Peran') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.announcements.index')">
                                    {{ __('Manajemen Pengumuman') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.certificate-templates.index')">
                                    {{ __('Certificate Templates') }}
                                </x-nav-link>
                            @endrole
                        </div>
                    </div>

                    {{-- Dropdown Pengaturan Pengguna --}}
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
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
                                <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
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
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    @can('view courses')
                        <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                            {{ __('Kelola Kursus') }}
                        </x-responsive-nav-link>
                    @endcan
                    @can('view progress reports')
                        <x-responsive-nav-link :href="route('eo.courses.index')" :active="request()->routeIs('eo.*')">
                            {{ __('Pemantauan Kursus') }}
                        </x-responsive-nav-link>
                    @endcan
                    @role('super-admin')
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Manajemen Pengguna') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                            {{ __('Manajemen Peran') }}
                        </x-responsive-nav-link>
                        <x-nav-link :href="route('admin.certificate-templates.index')">
                            {{ __('Certificate Templates') }}
                        </x-nav-link>
                    @endrole
                </div>
                <div class="pt-4 pb-1 border-t border-gray-200">
                    {{-- ... (sisa kode tidak berubah) ... --}}
                </div>
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
            {{ $slot }}
        </main>
    </div>

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" xintegrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        
        <!-- Summernote JS -->
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        @stack('scripts')
</body>
</html>