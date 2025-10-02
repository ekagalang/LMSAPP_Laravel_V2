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
                    {{ __('Edit Kelas') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $period->name }} - {{ $course->title }}</p>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if($period->status === 'active') bg-green-100 text-green-800
                    @elseif($period->status === 'upcoming') bg-blue-100 text-blue-800
                    @elseif($period->status === 'completed') bg-gray-100 text-gray-800
                    @else bg-red-100 text-red-800 @endif">
                    @if($period->status === 'active') 🟢 Aktif
                    @elseif($period->status === 'upcoming') 🔵 Akan Datang
                    @elseif($period->status === 'completed') ✅ Selesai
                    @else ❌ Dibatalkan @endif
                </div>
                <div class="text-sm text-gray-500">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editing Mode
                </div>
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
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium">Edit Detail</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm">
                            2
                        </div>
                        <span class="ml-2 text-sm">Update Status</span>
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

            <!-- Token Management Link -->
            <a href="{{ route('courses.tokens', $course) }}" class="block mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-lg sm:rounded-xl hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-white">Kelola Token Enrollment</h3>
                                <p class="text-indigo-100 text-sm mt-1">Kelola token untuk course dan semua kelas termasuk kelas ini</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <!-- Header Form -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Edit Kelas Kursus</h3>
                    <p class="text-indigo-100 text-sm mt-1">Perbarui detail kelas untuk {{ $period->name }}</p>
                </div>

                <div class="p-8" x-data="{
                    formData: {
                        name: '{{ old('name', $period->name) }}',
                        start_date: '{{ old('start_date', $period->start_date ? $period->start_date->format('Y-m-d') : '') }}',
                        end_date: '{{ old('end_date', $period->end_date ? $period->end_date->format('Y-m-d') : '') }}',
                        description: '{{ old('description', $period->description) }}',
                        max_participants: '{{ old('max_participants', $period->max_participants) }}',
                        status: '{{ old('status', $period->status) }}'
                    },
                    errors: {},
                    showDeleteModal: false,

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
                    <!-- Course Info Banner -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $course->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $course->description ? Str::limit($course->description, 100) : 'Mengedit periode untuk kursus ini' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <!-- Enrolled Users Count -->
                                @php
                                    $enrolledCount = $period->enrolledUsers()->count();
                                @endphp
                                @if($enrolledCount > 0)
                                    <div class="flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        {{ $enrolledCount }} Peserta
                                    </div>
                                @endif

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $course->status === 'published' ? '✅ Published' : '📝 Draft' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('course-periods.update', [$course, $period]) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Nama Periode -->
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
                                              placeholder="Deskripsi khusus untuk kelas ini (opsional)..."></textarea>

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
                                           placeholder="Contoh: 50">

                                    <p class="text-gray-400 text-xs mt-1">
                                        Kosongkan jika tidak ada batasan peserta
                                        @if($enrolledCount > 0)
                                            <span class="font-medium text-blue-600">(Saat ini: {{ $enrolledCount }} peserta terdaftar)</span>
                                        @endif
                                    </p>

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
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400">

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
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400">

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
                                        <option value="upcoming">🔵 Akan Datang</option>
                                        <option value="active">🟢 Aktif</option>
                                        <option value="completed">✅ Selesai</option>
                                        <option value="cancelled">❌ Dibatalkan</option>
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

                                <!-- Preview Card & Actions -->
                                <div class="space-y-4">
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

                                    <!-- Quick Actions -->
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <h4 class="text-gray-800 font-medium text-sm mb-3">Aksi Cepat</h4>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('course-periods.duplicate', [$course, $period]) }}"
                                               onclick="return confirm('Yakin ingin menduplikasi kelas ini?')"
                                               class="inline-flex items-center px-3 py-1.5 text-xs bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Duplikasi
                                            </a>
                                            <button type="button"
                                                    @click="showDeleteModal = true"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </button>
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
                                    Perbarui Kelas
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Modal -->
                    <div x-show="showDeleteModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 overflow-y-auto"
                         style="display: none;">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Kelas</h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">
                                                    Apakah Anda yakin ingin menghapus kelas "{{ $period->name }}"?
                                                    @if($enrolledCount > 0)
                                                        <br><br>
                                                        <span class="font-medium text-red-600">Perhatian: Kelas ini memiliki {{ $enrolledCount }} peserta terdaftar. Kelas tidak dapat dihapus sampai semua peserta dipindahkan atau dihapus.</span>
                                                    @else
                                                        Tindakan ini tidak dapat dibatalkan.
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    @if($enrolledCount === 0)
                                        <form action="{{ route('course-periods.destroy', [$course, $period]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Hapus Kelas
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button"
                                            @click="showDeleteModal = false"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized for course period edit!');
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
