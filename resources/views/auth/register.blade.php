<x-guest-layout>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out;
        }

        .input-focus:focus {
            border-color: #DA1E1E;
            box-shadow: 0 0 0 3px rgba(218, 30, 30, 0.1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #DA1E1E 0%, #B01818 100%);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(218, 30, 30, 0.15);
        }
    </style>

    <div x-data="{
        step: 1,
        formData: {
            name: '{{ old('name') }}',
            email: '{{ old('email') }}',
            date_of_birth: '{{ old('date_of_birth') }}',
            gender: '{{ old('gender') }}',
            institution_name: '{{ old('institution_name') }}',
            occupation: '{{ old('occupation') }}',
            password: '',
            password_confirmation: ''
        },
        nextStep() {
            if (this.validateCurrentStep()) {
                this.step++;
            } else {
                this.showValidationAlert();
            }
        },
        prevStep() {
            this.step--;
        },
        validateCurrentStep() {
            if (this.step === 1) {
                return this.formData.name && this.formData.email && this.formData.password && this.formData.password_confirmation;
            } else if (this.step === 2) {
                return this.formData.date_of_birth && this.formData.gender && this.formData.institution_name;
            } else if (this.step === 3) {
                return this.formData.occupation;
            }
            return true;
        },
        showValidationAlert() {
            // Validation handled silently for better browser compatibility
            // HTML5 required attributes will show native validation messages
        },
        handleKeyPress(event) {
            if (event.key === 'Enter' && this.step < 3) {
                event.preventDefault();
                this.nextStep();
            }
        }
    }"
    @keydown.enter="handleKeyPress($event)"
    class="w-full max-w-3xl mx-auto">

        <!-- Header Section -->
        <div class="text-center mb-8 animate-fadeInUp">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full gradient-bg mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Daftar Akun Baru</h2>
            <p class="text-gray-600">Lengkapi data diri Anda untuk memulai pembelajaran</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-10">
            <div class="flex items-center justify-between relative">
                <!-- Progress Line Background -->
                <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 -z-10"></div>
                <div class="absolute top-5 left-0 h-1 bg-gradient-to-r from-[#DA1E1E] to-[#B01818] transition-all duration-500 -z-10"
                    :style="`width: ${((step - 1) / 2) * 100}%`"></div>

                <!-- Step 1 -->
                <div class="flex-1 flex flex-col items-center">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-500 shadow-lg mb-2"
                        :class="step >= 1 ? 'gradient-bg text-white scale-110' : 'bg-white border-2 border-gray-300 text-gray-500'">
                        <svg x-show="step > 1" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-show="step <= 1" class="font-bold">1</span>
                    </div>
                    <p class="text-xs font-semibold text-center" :class="step >= 1 ? 'text-[#DA1E1E]' : 'text-gray-500'">
                        Akun
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="flex-1 flex flex-col items-center">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-500 shadow-lg mb-2"
                        :class="step >= 2 ? 'gradient-bg text-white scale-110' : 'bg-white border-2 border-gray-300 text-gray-500'">
                        <svg x-show="step > 2" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-show="step <= 2" class="font-bold">2</span>
                    </div>
                    <p class="text-xs font-semibold text-center" :class="step >= 2 ? 'text-[#DA1E1E]' : 'text-gray-500'">
                        Data Diri
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="flex-1 flex flex-col items-center">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-500 shadow-lg mb-2"
                        :class="step >= 3 ? 'gradient-bg text-white scale-110' : 'bg-white border-2 border-gray-300 text-gray-500'">
                        <span class="font-bold">3</span>
                    </div>
                    <p class="text-xs font-semibold text-center" :class="step >= 3 ? 'text-[#DA1E1E]' : 'text-gray-500'">
                        Pekerjaan
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="bg-white rounded-2xl shadow-xl p-8">
            @csrf

            <!-- Step 1: Account Information -->
            <div x-show="step === 1" x-transition class="space-y-5 animate-fadeInUp">
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" x-model="formData.name" required autofocus autocomplete="name"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                            placeholder="Masukkan nama lengkap">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" x-model="formData.email" required autocomplete="username"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                            placeholder="nama@email.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" x-model="formData.password" required autocomplete="new-password"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                            placeholder="Minimal 8 karakter">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" x-model="formData.password_confirmation" required autocomplete="new-password"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                            placeholder="Ulangi password">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <!-- Step 2: Personal Information -->
            <div x-show="step === 2" x-transition class="space-y-5 animate-fadeInUp">
                <div>
                    <x-input-label for="date_of_birth" :value="__('Tanggal Lahir')" class="text-gray-700 font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input id="date_of_birth" type="date" name="date_of_birth" x-model="formData.date_of_birth" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200">
                    </div>
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Jenis Kelamin')" class="text-gray-700 font-semibold" />
                    <div class="mt-3 grid grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-center p-5 rounded-xl border-2 cursor-pointer transition-all duration-300 card-hover"
                            :class="formData.gender === 'male' ? 'border-[#DA1E1E] bg-red-50 ring-2 ring-[#DA1E1E] ring-opacity-20' : 'border-gray-300 hover:border-[#DA1E1E] hover:bg-red-50'">
                            <input type="radio" name="gender" value="male" x-model="formData.gender" class="sr-only" required>
                            <div class="text-center">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center transition-all duration-300"
                                    :class="formData.gender === 'male' ? 'bg-[#DA1E1E]' : 'bg-gray-200'">
                                    <svg class="w-6 h-6" :class="formData.gender === 'male' ? 'text-white' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-sm" :class="formData.gender === 'male' ? 'text-[#DA1E1E]' : 'text-gray-700'">Laki-laki</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-5 rounded-xl border-2 cursor-pointer transition-all duration-300 card-hover"
                            :class="formData.gender === 'female' ? 'border-[#DA1E1E] bg-red-50 ring-2 ring-[#DA1E1E] ring-opacity-20' : 'border-gray-300 hover:border-[#DA1E1E] hover:bg-red-50'">
                            <input type="radio" name="gender" value="female" x-model="formData.gender" class="sr-only" required>
                            <div class="text-center">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center transition-all duration-300"
                                    :class="formData.gender === 'female' ? 'bg-[#DA1E1E]' : 'bg-gray-200'">
                                    <svg class="w-6 h-6" :class="formData.gender === 'female' ? 'text-white' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-sm" :class="formData.gender === 'female' ? 'text-[#DA1E1E]' : 'text-gray-700'">Perempuan</span>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="institution_name" :value="__('Nama Instansi/Sekolah')" class="text-gray-700 font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input id="institution_name" type="text" name="institution_name" x-model="formData.institution_name" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                            placeholder="Nama sekolah/universitas/perusahaan">
                    </div>
                    <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
                </div>
            </div>

            <!-- Step 3: Occupation -->
            <div x-show="step === 3" x-transition class="space-y-4 animate-fadeInUp">
                <div>
                    <x-input-label for="occupation" :value="__('Pekerjaan')" class="text-gray-700 font-semibold mb-4 block" />
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-300 card-hover"
                                :class="formData.occupation === '{{ $job }}' ? 'border-[#DA1E1E] bg-red-50 ring-2 ring-[#DA1E1E] ring-opacity-20' : 'border-gray-300 hover:border-[#DA1E1E] hover:bg-red-50'">
                                <input type="radio" name="occupation" value="{{ $job }}" x-model="formData.occupation" class="sr-only" {{ $occ === $job ? 'checked' : '' }} required>
                                <div class="flex items-center w-full">
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                        :class="formData.occupation === '{{ $job }}' ? 'border-[#DA1E1E]' : 'border-gray-400'">
                                        <div class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                            :class="formData.occupation === '{{ $job }}' ? 'bg-[#DA1E1E]' : 'bg-transparent'"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-medium" :class="formData.occupation === '{{ $job }}' ? 'text-[#DA1E1E]' : 'text-gray-700'">{{ $job }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                <button type="button" @click="prevStep()" x-show="step > 1"
                    class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </button>

                <div class="flex items-center gap-4" :class="step === 1 ? 'w-full justify-end' : ''">
                    <a x-show="step === 1" class="text-sm text-gray-600 hover:text-[#DA1E1E] rounded-md transition-colors duration-300 font-medium" href="{{ route('login') }}">
                        Sudah punya akun? <span class="underline">Masuk di sini</span>
                    </a>

                    <button type="button" @click="nextStep()" x-show="step < 3"
                        class="px-8 py-3 gradient-bg text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold flex items-center gap-2">
                        Selanjutnya
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button type="submit" x-show="step === 3"
                        class="px-8 py-3 gradient-bg text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Daftar Sekarang
                    </button>
                </div>
            </div>
        </form>

        <!-- Footer Info -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Dengan mendaftar, Anda menyetujui
                <a href="#" class="text-[#DA1E1E] hover:underline font-medium">Syarat & Ketentuan</a>
                dan
                <a href="#" class="text-[#DA1E1E] hover:underline font-medium">Kebijakan Privasi</a>
            </p>
        </div>
    </div>
</x-guest-layout>