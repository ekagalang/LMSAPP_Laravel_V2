<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Tugas Baru') }}
            </h2>
            <a href="{{ route('assignments.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Assignment Form -->
            <div class="bg-white shadow-xl rounded-2xl p-8">
                <form method="POST" action="{{ route('assignments.store') }}" class="space-y-8">
                    @csrf

                    <div class="space-y-8">
                        <!-- Header Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Detail Tugas
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Lengkapi informasi tugas yang akan diberikan kepada mahasiswa
                            </p>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Judul Tugas -->
                                <div class="group">
                                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            Judul Tugas
                                            <span class="text-red-500 ml-1">*</span>
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300"
                                               placeholder="Masukkan judul tugas..."
                                               required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('title')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Tipe Pengumpulan -->
                                <div class="group">
                                    <label for="submission_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Tipe Pengumpulan
                                            <span class="text-red-500 ml-1">*</span>
                                        </span>
                                    </label>
                                    <select name="submission_type" id="submission_type"
                                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300">
                                        <option value="file" {{ old('submission_type') == 'file' ? 'selected' : '' }}>📁 File Upload</option>
                                        <option value="link" {{ old('submission_type') == 'link' ? 'selected' : '' }}>🔗 Link/URL</option>
                                        <option value="both" {{ old('submission_type') == 'both' ? 'selected' : '' }}>📁🔗 File dan Link</option>
                                    </select>
                                    @error('submission_type')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Max Points -->
                                <div class="group">
                                    <label for="max_points" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            Poin Maksimal
                                            <span class="text-red-500 ml-1">*</span>
                                        </span>
                                    </label>
                                    <input type="number" name="max_points" id="max_points" value="{{ old('max_points', 100) }}"
                                           min="1" max="1000"
                                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300"
                                           placeholder="100"
                                           required>
                                    @error('max_points')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Due Date -->
                                <div class="group">
                                    <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Tenggat Waktu
                                        </span>
                                    </label>
                                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300">
                                    @error('due_date')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- File Settings -->
                                <div id="file-settings" class="group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Pengaturan File
                                        </span>
                                    </label>

                                    <div class="space-y-4 p-4 bg-gray-50 rounded-lg">
                                        <div class="flex space-x-4">
                                            <div class="flex-1">
                                                <label for="max_files" class="block text-xs font-medium text-gray-600 mb-1">Maksimal File</label>
                                                <input type="number" name="max_files" id="max_files" value="{{ old('max_files', 1) }}"
                                                       min="1" max="10"
                                                       class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                            </div>
                                            <div class="flex-1">
                                                <label for="max_file_size_mb" class="block text-xs font-medium text-gray-600 mb-1">Ukuran Max (MB)</label>
                                                <input type="number" name="max_file_size_mb" id="max_file_size_mb" value="{{ old('max_file_size_mb', 10) }}"
                                                       min="1" max="100"
                                                       class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-2">Jenis File yang Diizinkan</label>
                                            <div class="grid grid-cols-4 gap-2 text-xs">
                                                @php
                                                    $allFileTypes = [
                                                        'pdf' => '📄 PDF',
                                                        'doc' => '📝 DOC',
                                                        'docx' => '📝 DOCX',
                                                        'xls' => '📊 XLS',
                                                        'xlsx' => '📊 XLSX',
                                                        'ppt' => '📽️ PPT',
                                                        'pptx' => '📽️ PPTX',
                                                        'txt' => '📄 TXT',
                                                        'jpg' => '🖼️ JPG',
                                                        'jpeg' => '🖼️ JPEG',
                                                        'png' => '🖼️ PNG',
                                                        'gif' => '🎞️ GIF',
                                                        'mp4' => '🎬 MP4',
                                                        'mov' => '🎬 MOV',
                                                        'avi' => '🎬 AVI',
                                                        'mkv' => '🎬 MKV',
                                                        'mp3' => '🎵 MP3',
                                                        'wav' => '🎵 WAV',
                                                        'zip' => '📦 ZIP',
                                                        'rar' => '📦 RAR'
                                                    ];
                                                @endphp
                                                @foreach($allFileTypes as $ext => $name)
                                                    <label class="flex items-center p-1 hover:bg-blue-50 rounded">
                                                        <input type="checkbox" name="allowed_file_types[]" value="{{ $ext }}"
                                                               {{ in_array($ext, old('allowed_file_types', ['pdf', 'doc', 'docx'])) ? 'checked' : '' }}
                                                               class="rounded border-gray-300 text-blue-600 mr-1 w-3 h-3">
                                                        <span class="text-xs">{{ $name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Description -->
                                <div class="group">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                            </svg>
                                            Deskripsi Singkat
                                        </span>
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                              class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300"
                                              placeholder="Deskripsi singkat tentang tugas ini...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Instructions -->
                                <div class="group">
                                    <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Instruksi Lengkap
                                        </span>
                                    </label>
                                    <textarea name="instructions" id="instructions" rows="8"
                                              class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 hover:border-gray-300"
                                              placeholder="Berikan instruksi lengkap untuk mengerjakan tugas ini...">{{ old('instructions') }}</textarea>
                                    @error('instructions')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section -->
                        <div class="col-span-1 lg:col-span-2 space-y-6">
                            <!-- Settings -->
                            <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Pengaturan Tugas
                                </h4>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">Tampilkan kepada mahasiswa</span>
                                                <p class="text-xs text-gray-500">Tugas akan terlihat oleh mahasiswa</p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_to_students" value="1"
                                                   {{ old('show_to_students', true) ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('assignments.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Buat Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="fixed top-4 right-4 z-50 max-w-sm w-full bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <p class="font-medium">Terdapat kesalahan:</p>
                <ul class="mt-1 text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed.top-4.right-4')?.remove();
        }, 8000);
    </script>
    @endif

    @push('scripts')
    <script>
        // Show/hide file settings based on submission type
        document.getElementById('submission_type').addEventListener('change', function() {
            const fileSettings = document.getElementById('file-settings');
            if (this.value === 'file' || this.value === 'both') {
                fileSettings.style.display = 'block';
            } else {
                fileSettings.style.display = 'none';
            }
        });

        // Initialize display based on current values
        document.addEventListener('DOMContentLoaded', function() {
            const submissionType = document.getElementById('submission_type').value;
            const fileSettings = document.getElementById('file-settings');
            if (submissionType !== 'file' && submissionType !== 'both') {
                fileSettings.style.display = 'none';
            }

            // Convert MB to bytes for max_file_size
            document.querySelector('form').addEventListener('submit', function() {
                const mbInput = document.getElementById('max_file_size_mb');
                const bytesInput = document.createElement('input');
                bytesInput.type = 'hidden';
                bytesInput.name = 'max_file_size';
                bytesInput.value = mbInput.value * 1024 * 1024;
                this.appendChild(bytesInput);
            });
        });
    </script>
    @endpush
</x-app-layout>