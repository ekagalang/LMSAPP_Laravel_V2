<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="javascript:void(0)" onclick="window.history.back()"
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium mb-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Kembali') }}
                </a>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Buat Kelas Baru') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Kelas Baru
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center">
                    <div class="flex items-center text-indigo-600">
                        <div class="flex items-center justify-center w-8 h-8 bg-indigo-600 text-white rounded-full text-sm font-semibold">
                            1
                        </div>
                        <span class="ml-2 text-sm font-medium">Detail Kelas</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm">
                            2
                        </div>
                        <span class="ml-2 text-sm">Jadwal & Status</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm">
                            3
                        </div>
                        <span class="ml-2 text-sm">Konfirmasi</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <!-- Header Form -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Buat Kelas Kursus</h3>
                    <p class="text-indigo-100 text-sm mt-1">Tentukan detail kelas untuk batch/angkatan baru</p>
                </div>

                <div class="p-8" x-data="{
                    formData: {
                        name: '',
                        start_date: '',
                        end_date: '',
                        description: '',
                        max_participants: '',
                        status: 'upcoming'
                    },
                    errors: {},

                    validateDates() {
                        if (this.formData.start_date && this.formData.end_date) {
                            if (new Date(this.formData.start_date) >= new Date(this.formData.end_date)) {
                                this.errors.end_date = 'Tanggal selesai harus setelah tanggal mulai';
                            } else {
                                delete this.errors.end_date;
                            }
                        }
                    },

                    generatePeriodName() {
                        if (this.formData.start_date) {
                            const date = new Date(this.formData.start_date);
                            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            const month = monthNames[date.getMonth()];
                            const year = date.getFullYear();
                            this.formData.name = `Batch ${month} ${year}`;
                        }
                    }
                }">
                    <form action="{{ route('course-periods.store', $course) }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Course Info Banner -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $course->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $course->description ? Str::limit($course->description, 100) : 'Membuat kelas baru untuk kursus ini' }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $course->status === 'published' ? '✅ Published' : '📝 Draft' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Nama Kelas -->
                                <div class="group">
                                    <label for="name" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Nama Kelas *
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           x-model="formData.name"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400"
                                           value="{{ old('name') }}"
                                           required
                                           placeholder="Contoh: Batch Januari 2025">

                                    <button type="button"
                                            @click="generatePeriodName()"
                                            class="mt-2 text-xs text-indigo-600 hover:text-indigo-800">
                                        🎯 Generate nama otomatis berdasarkan tanggal mulai
                                    </button>

                                    @error('name')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="group">
                                    <label for="description" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                        </svg>
                                        Deskripsi Kelas
                                    </label>
                                    <textarea name="description"
                                              id="description"
                                              x-model="formData.description"
                                              rows="4"
                                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400 resize-none"
                                              placeholder="Deskripsi khusus untuk kelas ini (opsional)...">{{ old('description') }}</textarea>

                                    <div class="flex justify-between items-center mt-1">
                                        @error('description')
                                            <p class="text-red-500 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @else
                                            <p class="text-gray-400 text-xs">Jelaskan keunikan kelas ini</p>
                                        @enderror
                                        <p class="text-gray-400 text-xs" x-text="`${formData.description.length} karakter`"></p>
                                    </div>
                                </div>

                                <!-- Maksimal Peserta -->
                                <div class="group">
                                    <label for="max_participants" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Maksimal Peserta
                                    </label>
                                    <input type="number"
                                           name="max_participants"
                                           id="max_participants"
                                           x-model="formData.max_participants"
                                           min="1"
                                           max="1000"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400"
                                           value="{{ old('max_participants') }}"
                                           placeholder="Contoh: 50">

                                    <p class="text-gray-400 text-xs mt-1">Kosongkan jika tidak ada batasan peserta</p>

                                    @error('max_participants')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Tanggal Mulai -->
                                <div class="group">
                                    <label for="start_date" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Tanggal Mulai
                                    </label>
                                    <input type="date"
                                           name="start_date"
                                           id="start_date"
                                           x-model="formData.start_date"
                                           @change="validateDates(); generatePeriodName();"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400"
                                           value="{{ old('start_date') }}"
                                           min="{{ date('Y-m-d') }}">

                                    @error('start_date')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Tanggal Selesai -->
                                <div class="group">
                                    <label for="end_date" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Tanggal Selesai
                                    </label>
                                    <input type="date"
                                           name="end_date"
                                           id="end_date"
                                           x-model="formData.end_date"
                                           @change="validateDates()"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400"
                                           value="{{ old('end_date') }}">

                                    <div x-show="errors.end_date" class="text-red-500 text-sm mt-2">
                                        <span x-text="errors.end_date"></span>
                                    </div>

                                    @error('end_date')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="group">
                                    <label for="status" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Status Kelas *
                                    </label>
                                    <select name="status"
                                            id="status"
                                            x-model="formData.status"
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400">
                                        <option value="upcoming" {{ old('status', 'upcoming') == 'upcoming' ? 'selected' : '' }}>
                                            🔵 Akan Datang
                                        </option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                            🟢 Aktif
                                        </option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                            ✅ Selesai
                                        </option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                            ❌ Dibatalkan
                                        </option>
                                    </select>

                                    @error('status')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Preview Card -->
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-4">
                                    <h4 class="text-indigo-800 font-medium text-sm mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Preview Kelas
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nama:</span>
                                            <span class="text-gray-900 font-medium" x-text="formData.name || 'Belum diisi'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Jadwal:</span>
                                            <span class="text-gray-900" x-text="formData.start_date && formData.end_date ? `${formData.start_date} - ${formData.end_date}` : 'Belum diisi'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="text-gray-900">
                                                <span x-show="formData.status === 'upcoming'">🔵 Akan Datang</span>
                                                <span x-show="formData.status === 'active'">🟢 Aktif</span>
                                                <span x-show="formData.status === 'completed'">✅ Selesai</span>
                                                <span x-show="formData.status === 'cancelled'">❌ Dibatalkan</span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Max Peserta:</span>
                                            <span class="text-gray-900" x-text="formData.max_participants || 'Tidak terbatas'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('courses.show', $course) }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </a>

                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Buat Kelas
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized for course period create!');
        });

        // Auto-update end date min when start date changes
        document.addEventListener('change', function(e) {
            if (e.target.name === 'start_date') {
                const startDate = e.target.value;
                const endDateInput = document.querySelector('[name="end_date"]');
                if (endDateInput && startDate) {
                    // Set minimum end date to day after start date
                    const nextDay = new Date(startDate);
                    nextDay.setDate(nextDay.getDate() + 1);
                    endDateInput.min = nextDay.toISOString().split('T')[0];

                    // Clear end date if it's before new start date
                    if (endDateInput.value && endDateInput.value <= startDate) {
                        endDateInput.value = '';
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
