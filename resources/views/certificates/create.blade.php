<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Lengkapi Data untuk Sertifikat') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Isi data yang diperlukan untuk menerbitkan sertifikat Anda
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Banner -->
            <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-700 rounded-2xl p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-2">
                            Selamat! Anda Berhasil Menyelesaikan Kursus
                        </h3>
                        <p class="text-green-700 dark:text-green-400 text-base mb-3">
                            <strong>{{ $course->title }}</strong>
                        </p>
                        <p class="text-green-600 dark:text-green-400 text-sm">
                            Anda telah memenuhi semua syarat untuk mendapatkan sertifikat. Lengkapi data di bawah ini untuk menerbitkan sertifikat resmi Anda.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <form method="POST" action="{{ route('certificates.store') }}" class="space-y-8">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">

                        <!-- Personal Information Section -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Informasi Pribadi
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Data pribadi yang akan dicantumkan dalam sertifikat
                                </p>
                            </div>

                            <!-- Email -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                        </div>
                                        <x-text-input id="email" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" type="email" name="email" :value="old('email', auth()->user()->email)" required />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Gender -->
                                <div>
                                    <x-input-label for="gender" :value="__('Jenis Kelamin')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                                    <div class="mt-3 space-y-3">
                                        <label class="relative flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                            <input type="radio" name="gender" value="male" class="sr-only" {{ old('gender') === 'male' ? 'checked' : '' }}>
                                            <div class="flex items-center">
                                                <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-500 rounded-full flex items-center justify-center mr-3 radio-custom">
                                                    <div class="w-2.5 h-2.5 bg-blue-600 rounded-full opacity-0 radio-dot transition duration-200"></div>
                                                </div>
                                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7c0-1.3 1.3-2 2-2s2 .7 2 2-1.3 2-2 2-2-.7-2-2zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"></path>
                                                </svg>
                                                <span class="text-gray-900 dark:text-white font-medium">Laki-laki</span>
                                            </div>
                                        </label>
                                        <label class="relative flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                            <input type="radio" name="gender" value="female" class="sr-only" {{ old('gender') === 'female' ? 'checked' : '' }}>
                                            <div class="flex items-center">
                                                <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-500 rounded-full flex items-center justify-center mr-3 radio-custom">
                                                    <div class="w-2.5 h-2.5 bg-blue-600 rounded-full opacity-0 radio-dot transition duration-200"></div>
                                                </div>
                                                <svg class="w-5 h-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7c0-1.3 1.3-2 2-2s2 .7 2 2-1.3 2-2 2-2-.7-2-2zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"></path>
                                                </svg>
                                                <span class="text-gray-900 dark:text-white font-medium">Perempuan</span>
                                            </div>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <x-input-label for="date_of_birth" :value="__('Tanggal Lahir')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                                    <div class="relative mt-2">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <x-text-input id="date_of_birth" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
                                    </div>
                                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Section -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Informasi Profesional
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Informasi pekerjaan dan institusi Anda
                                </p>
                            </div>

                            <!-- Institution Name -->
                            <div>
                                <x-input-label for="institution_name" :value="__('Nama Instansi/Sekolah')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <x-text-input id="institution_name" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" type="text" name="institution_name" :value="old('institution_name')" placeholder="Contoh: Universitas Indonesia, PT. Teknologi Maju, dll." required />
                                </div>
                                <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
                            </div>

                            <!-- Occupation -->
                            <div>
                                <x-input-label for="occupation" :value="__('Pekerjaan')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @php($occ = old('occupation'))
                                    @foreach ([
                                        'Pelajar/Mahasiswa',
                                        'PNS/ASN',
                                        'TNI/Polri',
                                        'Karyawan Swasta',
                                        'Pegawai BUMN',
                                        'Wiraswasta',
                                        'Buruh/Tenaga Harian Lepas',
                                        'Pedagang',
                                        'Sopir/Pengemudi',
                                        'Ibu Rumah Tangga',
                                        'Pensiunan',
                                        'Tidak Bekerja',
                                        'Lainnya'
                                    ] as $job)
                                        <label class="relative flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 occupation-option">
                                            <input type="radio" name="occupation" value="{{ $job }}" class="sr-only" {{ $occ === $job ? 'checked' : '' }}>
                                            <div class="flex items-center w-full">
                                                <div class="w-4 h-4 border-2 border-gray-300 dark:border-gray-500 rounded-full flex items-center justify-center mr-3 radio-custom">
                                                    <div class="w-2 h-2 bg-blue-600 rounded-full opacity-0 radio-dot transition duration-200"></div>
                                                </div>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $job }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Pastikan semua data sudah benar sebelum menerbitkan sertifikat
                                </p>
                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-200 transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ __('Simpan dan Cetak Sertifikat') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Radio Button Styling */
        input[type="radio"]:checked + .radio-custom {
            border-color: #3B82F6;
        }
        
        input[type="radio"]:checked + .radio-custom .radio-dot {
            opacity: 1;
        }
        
        input[type="radio"]:checked ~ .occupation-option {
            border-color: #3B82F6;
            background-color: #EFF6FF;
        }
        
        .dark input[type="radio"]:checked ~ .occupation-option {
            background-color: #1E3A8A;
        }
        
        /* Input focus animations */
        input:focus {
            transform: translateY(-1px);
        }
        
        /* Hover animations */
        .occupation-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-app-layout>