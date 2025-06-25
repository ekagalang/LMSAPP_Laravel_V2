<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Instruktur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat datang, Instruktur!") }}
                    {{-- Di sini nanti akan ada daftar kursus yang diajar Instruktur, dll. --}}
                    <p>Kursus yang Anda kelola:</p>
                    <ul>
                        <li>Pengantar Laravel (50% Selesai)</li>
                        <li>Dasar-dasar Database (20 Peserta)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>