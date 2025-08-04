<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lengkapi Data untuk Sertifikat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Kursus: {{ $course->title }}
                    </h3>
                    <p class="mb-6">
                        Selamat! Anda telah memenuhi semua syarat untuk mendapatkan sertifikat. Silakan lengkapi data di bawah ini untuk dicantumkan pada sertifikat Anda.
                    </p>

                    <form method="POST" action="{{ route('certificates.store') }}">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">

                        <!-- Tempat Lahir -->
                        <div class="mt-4">
                            <x-input-label for="place_of_birth" :value="__('Tempat Lahir')" />
                            <x-text-input id="place_of_birth" class="block mt-1 w-full" type="text" name="place_of_birth" :value="old('place_of_birth')" required autofocus />
                            <x-input-error :messages="$errors->get('place_of_birth')" class="mt-2" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="mt-4">
                            <x-input-label for="date_of_birth" :value="__('Tanggal Lahir')" />
                            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <!-- Nomor Identitas (NIK/NIP/NISN) -->
                        <div class="mt-4">
                            <x-input-label for="identity_number" :value="__('Nomor Identitas (NIK/NIP/NISN)')" />
                            <x-text-input id="identity_number" class="block mt-1 w-full" type="text" name="identity_number" :value="old('identity_number')" required />
                            <x-input-error :messages="$errors->get('identity_number')" class="mt-2" />
                        </div>
                        
                        <!-- Nama Instansi/Sekolah -->
                        <div class="mt-4">
                            <x-input-label for="institution_name" :value="__('Nama Instansi/Sekolah')" />
                            <x-text-input id="institution_name" class="block mt-1 w-full" type="text" name="institution_name" :value="old('institution_name')" required />
                            <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Simpan dan Cetak Sertifikat') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
