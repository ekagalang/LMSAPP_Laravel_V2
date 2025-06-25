<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat datang, Admin!") }}
                    {{-- Di sini nanti akan ada statistik Admin, daftar pengguna, dll. --}}
                    <p>Ringkasan statistik LMS:</p>
                    <ul>
                        <li>Jumlah Total Pengguna: **[Nanti dari database]**</li>
                        <li>Jumlah Kursus Aktif: **[Nanti dari database]**</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>