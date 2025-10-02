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
                    {{ __('Edit Kursus') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $course->status === 'published' ? '✅ Published' : '📝 Draft' }}
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
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center">
                    <div class="flex items-center text-indigo-600">
                        <div class="flex items-center justify-center w-8 h-8 bg-indigo-600 text-white rounded-full text-sm font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium">Edit Informasi</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm">
                            2
                        </div>
                        <span class="ml-2 text-sm">Kelas & Timeline</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm">
                            3
                        </div>
                        <span class="ml-2 text-sm">Update Konten</span>
                    </div>
                </div>
            </div>

            <!-- Token Management Link -->
            <a href="{{ route('courses.tokens', $course) }}" class="block mb-6">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 overflow-hidden shadow-lg sm:rounded-xl hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-white">Kelola Token Enrollment</h3>
                                <p class="text-purple-100 text-sm mt-1">Generate dan kelola token untuk course dan semua kelas</p>
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
                    <h3 class="text-lg font-semibold text-white">Update Kursus</h3>
                    <p class="text-indigo-100 text-sm mt-1">Perbarui informasi kursus dan kelola periode pembelajaran</p>
                </div>

                <div class="p-8" x-data="{
                    enablePeriods: {{ $course->periods->count() > 0 ? 'true' : 'false' }},
                    createDefaultPeriod: false,
                    customPeriods: @js($course->periods->map(function($period) {
                        return [
                            'id' => $period->id,
                            'name' => $period->name,
                            'start_date' => $period->start_date ? $period->start_date->format('Y-m-d') : '',
                            'end_date' => $period->end_date ? $period->end_date->format('Y-m-d') : '',
                            'description' => $period->description,
                            'max_participants' => $period->max_participants,
                            'status' => $period->status
                        ];
                    })->toArray()),
                    periodsToDelete: [],
                    addPeriod() {
                        console.log('addPeriod() called!');
                        this.customPeriods.push({
                            id: null,
                            name: '',
                            start_date: '',
                            end_date: '',
                            description: '',
                            max_participants: '',
                            status: 'upcoming'
                        });
                        console.log('Current periods:', this.customPeriods);
                    },
                    removePeriod(index) {
                        console.log('removePeriod() called for index:', index);
                        const period = this.customPeriods[index];
                        if (period.id) {
                            // Mark existing period for deletion
                            this.periodsToDelete.push(period.id);
                        }
                        this.customPeriods.splice(index, 1);
                        console.log('Remaining periods:', this.customPeriods);
                        console.log('Periods to delete:', this.periodsToDelete);
                    }
                }">
                    <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- ===================================== -->
                        <!-- SECTION 1: BASIC COURSE INFO -->
                        <!-- ===================================== -->
                        <div class="border-b border-gray-200 pb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Informasi Dasar Kursus
                            </h3>

                            <!-- Grid Layout -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <!-- Left Column -->
                                <div class="lg:col-span-2 space-y-6">
                                    <!-- Judul Kursus -->
                                    <div class="group">
                                        <label for="title" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            Judul Kursus *
                                        </label>
                                        <input type="text"
                                               name="title"
                                               id="title"
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400"
                                               value="{{ old('title', $course->title) }}"
                                               required
                                               autofocus
                                               placeholder="Masukkan judul kursus yang menarik...">
                                        @error('title')
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
                                            Deskripsi
                                        </label>
                                        <textarea name="description"
                                                  id="description"
                                                  rows="5"
                                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400 resize-none"
                                                  placeholder="Jelaskan tentang kursus Anda, apa yang akan dipelajari siswa...">{{ old('description', $course->description) }}</textarea>
                                        <div class="flex justify-between items-center mt-1">
                                            @error('description')
                                                <p class="text-red-500 text-sm flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @else
                                                <p class="text-gray-400 text-xs">Update deskripsi untuk memberikan info lebih jelas</p>
                                            @enderror
                                            <p class="text-gray-400 text-xs" id="char-count">0 karakter</p>
                                        </div>
                                    </div>

                                    <!-- Objectives -->
                                    <div class="group">
                                        <label for="objectives" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                            Tujuan Pembelajaran
                                        </label>
                                        <textarea name="objectives"
                                                  id="objectives"
                                                  rows="3"
                                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400 resize-none"
                                                  placeholder="Setelah menyelesaikan kursus ini, peserta akan mampu...">{{ old('objectives', $course->objectives) }}</textarea>
                                        @error('objectives')
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
                                    <!-- Thumbnail Upload dengan Preview -->
                                    <div x-data="{ imagePreview: '{{ $course->thumbnail ? Storage::url($course->thumbnail) : '' }}' }" class="group">
                                        <label for="thumbnail" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Gambar Sampul
                                        </label>

                                        <!-- Image Preview -->
                                        <div x-show="imagePreview" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                            <img :src="imagePreview" alt="Current Thumbnail" class="w-full h-32 object-cover rounded-lg shadow-md mb-3">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="clear_thumbnail" id="clear_thumbnail"
                                                       @change="if ($event.target.checked) imagePreview = ''"
                                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                                <label for="clear_thumbnail" class="ml-2 text-sm text-red-600 hover:text-red-700 cursor-pointer">
                                                    🗑️ Hapus Gambar Saat Ini
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors duration-200">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="thumbnail" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                        <span>Ganti gambar</span>
                                                        <input id="thumbnail" name="thumbnail" type="file" class="sr-only" accept="image/*" @change="imagePreview = URL.createObjectURL($event.target.files[0]); document.getElementById('clear_thumbnail').checked = false;">
                                                    </label>
                                                    <p class="pl-1">atau drag & drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                            </div>
                                        </div>
                                        @error('thumbnail')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Status Kursus -->
                                    <div class="group">
                                        <label for="status" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Status Publikasi
                                        </label>
                                        <select name="status"
                                                id="status"
                                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400">
                                            <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>
                                                📝 Draft
                                            </option>
                                            <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>
                                                ✅ Published
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

                                    <!-- Template Sertifikat -->
                                    <div class="group">
                                        <label for="certificate_template_id" class="block text-sm font-medium text-gray-700">
                                            Certificate Template (Optional)
                                        </label>
                                        <select name="certificate_template_id" id="certificate_template_id"
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            <option value="">No Certificate</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}"
                                                        @if(old('certificate_template_id', $course->certificate_template_id ?? '') == $template->id) selected @endif>
                                                    {{ $template->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Course Stats -->
                                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-lg p-4">
                                        <h4 class="text-indigo-800 font-medium text-sm mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Statistik Kursus
                                        </h4>
                                        <div class="grid grid-cols-2 gap-3 text-center">
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <div class="text-lg font-bold text-gray-800">{{ $course->created_at->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-600">Dibuat</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <div class="text-lg font-bold text-gray-800">{{ $course->updated_at->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-600">Diperbarui</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================== -->
                        <!-- SECTION 2: COURSE PERIODS MANAGEMENT -->
                        <!-- ===================================== -->
                        <div class="border-b border-gray-200 pb-8">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Manajemen Kelas Kursus
                                    <span class="ml-2 text-sm font-normal text-gray-500">(Opsional)</span>
                                </h3>

                                <!-- Enable Periods Checkbox -->
                                <div class="flex items-center">
                                    <!-- Hidden input untuk memastikan false value dikirim -->
                                    <input type="hidden" name="enable_periods" value="0">
                                    <input type="checkbox"
                                           id="enable_periods"
                                           name="enable_periods"
                                           value="1"
                                           x-model="enablePeriods"
                                           @change="console.log('Periods enabled:', enablePeriods)"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <label for="enable_periods" class="ml-2 text-sm font-medium text-gray-700">
                                        Aktifkan Manajemen Kelas
                                    </label>
                                    <!-- Debug info -->
                                    <span x-text="enablePeriods ? ' ✓ AKTIF' : ' ✗ NONAKTIF'"
                                          class="ml-2 text-xs"
                                          :class="enablePeriods ? 'text-green-600' : 'text-gray-400'"></span>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800 mb-1">Tentang Kelas Kursus</h4>
                                        <p class="text-sm text-blue-700">
                                            Kelas kursus berguna untuk mengelola batch/angkatan yang berbeda dari kursus yang sama.
                                            Setiap kelas memiliki jadwal dan peserta yang terpisah. Fitur chat juga akan terbatas per kelas.
                                        </p>
                                        <p class="text-xs text-blue-600 mt-2">
                                            💡 Jika tidak diaktifkan, sistem akan menghapus semua kelas yang ada.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Periods Section (conditionally shown) -->
                            <div x-show="enablePeriods" x-transition class="space-y-6">
                                <!-- Existing and Custom Periods Section -->
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-medium text-gray-900">
                                            Kelas Kursus
                                            <span x-text="`(${customPeriods.length} kelas)`" class="text-sm text-gray-500"></span>
                                        </h4>
                                        <button type="button"
                                                @click="addPeriod(); console.log('Button clicked!');"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Tambah Kelas
                                        </button>
                                    </div>

                                    <!-- Hidden input for periods to delete -->
                                    <template x-for="(periodId, index) in periodsToDelete" :key="'delete_' + index">
                                        <input type="hidden" :name="`periods_to_delete[${index}]`" :value="periodId">
                                    </template>

                                    <div id="periods-container" class="space-y-4">
                                        <template x-for="(period, index) in customPeriods" :key="period.id || 'new_' + index">
                                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <h5 class="text-sm font-medium text-gray-900 flex items-center">
                                                        <span x-show="period.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                            Tersimpan
                                                        </span>
                                                        <span x-show="!period.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                            Baru
                                                        </span>
                                                        Kelas #<span x-text="index + 1"></span>
                                                    </h5>
                                                    <button type="button"
                                                            @click="removePeriod(index)"
                                                            class="text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Hidden field for existing period ID -->
                                                <input type="hidden" :name="`periods[${index}][id]`" :value="period.id">

                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Nama Kelas *
                                                        </label>
                                                        <input type="text"
                                                               :name="`periods[${index}][name]`"
                                                               x-model="period.name"
                                                               required
                                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="Contoh: Batch 1 - Januari 2025">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Tanggal Mulai
                                                        </label>
                                                        <input type="date"
                                                               :name="`periods[${index}][start_date]`"
                                                               x-model="period.start_date"
                                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               min="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Tanggal Selesai
                                                        </label>
                                                        <input type="date"
                                                               :name="`periods[${index}][end_date]`"
                                                               x-model="period.end_date"
                                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    </div>
                                                </div>

                                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Deskripsi
                                                        </label>
                                                        <textarea :name="`periods[${index}][description]`"
                                                                  x-model="period.description"
                                                                  rows="2"
                                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                  placeholder="Deskripsi kelas..."></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Maksimal Peserta
                                                        </label>
                                                        <input type="number"
                                                               :name="`periods[${index}][max_participants]`"
                                                               x-model="period.max_participants"
                                                               min="1"
                                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                               placeholder="Contoh: 50">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            Status *
                                                        </label>
                                                        <select :name="`periods[${index}][status]`"
                                                                x-model="period.status"
                                                                @change="console.log('Status changed for period', index, 'to:', period.status)"
                                                                required
                                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="upcoming">🔵 Akan Datang</option>
                                                            <option value="active">🟢 Aktif</option>
                                                            <option value="completed">✅ Selesai</option>
                                                            <option value="cancelled">❌ Dibatalkan</option>
                                                        </select>
                                                        <!-- ✅ DEBUG: Show current value -->
                                                        <p x-text="`Selected: ${period.status}`" class="text-xs text-gray-500 mt-1"></p>

                                                        <!-- ✅ BACKUP: Hidden input untuk memastikan data terkirim -->
                                                        <input type="hidden" :name="`periods[${index}][status_backup]`" :value="period.status">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div x-show="customPeriods.length === 0" class="text-center py-8 text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-sm">Belum ada kelas. Klik "Tambah Kelas" untuk menambahkan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('courses.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </a>

                                <div class="flex space-x-3">
                                    <button type="submit"
                                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('Perbarui Kursus') }}
                                    </button>
                                </div>
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
        // Debug function untuk memastikan Alpine.js bekerja
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized successfully!');
        });

        // Character counter for description
        const descriptionTextarea = document.getElementById('description');
        const charCountElement = document.getElementById('char-count');

        function updateCharCount() {
            const charCount = descriptionTextarea.value.length;
            charCountElement.textContent = charCount + ' karakter';
        }

        // Initialize counter
        updateCharCount();

        // Update counter on input
        descriptionTextarea.addEventListener('input', updateCharCount);

        // File upload preview
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('New file selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });

        // Clear thumbnail checkbox behavior
        const clearThumbnailCheckbox = document.getElementById('clear_thumbnail');
        if (clearThumbnailCheckbox) {
            clearThumbnailCheckbox.addEventListener('change', function(e) {
                if (e.target.checked) {
                    console.log('Thumbnail will be cleared on save');
                }
            });
        }

        // Date validation
        document.addEventListener('change', function(e) {
            if (e.target.name && e.target.name.includes('start_date')) {
                const startDate = e.target;
                const endDateName = startDate.name.replace('start_date', 'end_date');
                const endDate = document.querySelector(`[name="${endDateName}"]`);

                if (endDate) {
                    endDate.min = startDate.value;
                    if (endDate.value && endDate.value <= startDate.value) {
                        endDate.value = '';
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
