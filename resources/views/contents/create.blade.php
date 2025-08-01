<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="space-y-2">
                <a href="{{ route('courses.show', $lesson->course) }}"
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200 group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Kursus
                </a>
                <h1 class="text-3xl font-bold text-gray-900">
                    ✨ Buat Konten Baru
                </h1>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full font-medium">
                        {{ $lesson->title }}
                    </span>
                    <span class="text-gray-400">•</span>
                    <span>{{ $lesson->course->title }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                        <span class="ml-2 text-sm font-medium text-indigo-600">Pilih Tipe</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 rounded"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Isi Konten</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 rounded"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">3</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Simpan</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-6">
                    <h2 class="text-2xl font-bold text-white">Informasi Konten</h2>
                    <p class="text-indigo-100 mt-1">Isi detail konten pembelajaran yang akan dibuat</p>
                </div>

                <form id="contentForm" method="POST" action="{{ route('lessons.contents.store', $lesson) }}" enctype="multipart/form-data" class="p-8">
                    @csrf

                    <div class="space-y-6">
                        <div class="group">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                📝 Judul Konten
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-lg placeholder-gray-400"
                                   placeholder="Masukkan judul konten yang menarik..."
                                   value="{{ old('title') }}"
                                   required autofocus>
                            @error('title')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="group">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                📄 Deskripsi (Opsional)
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 placeholder-gray-400"
                                      placeholder="Berikan deskripsi singkat tentang konten ini...">{{ old('description') }}</textarea>
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-4">
                                🎯 Pilih Tipe Konten
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="text" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'text' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                📝
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Teks</h3>
                                            <p class="text-sm text-gray-500 mt-1">Konten berbasis teks dengan editor</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="video" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'video' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                🎥
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Video</h3>
                                            <p class="text-sm text-gray-500 mt-1">Video dari YouTube/Vimeo</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="document" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'document' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                📄
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Dokumen</h3>
                                            <p class="text-sm text-gray-500 mt-1">PDF, DOCX, PPTX</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="image" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'image' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                🖼️
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Gambar</h3>
                                            <p class="text-sm text-gray-500 mt-1">JPG, PNG, GIF</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="quiz" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'quiz' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                🧠
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Kuis</h3>
                                            <p class="text-sm text-gray-500 mt-1">Pertanyaan interaktif</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="essay" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'essay' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                ✍️
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Esai</h3>
                                            <p class="text-sm text-gray-500 mt-1">Tugas menulis</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 space-y-6">
                        <div id="body_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                                <label for="body_text" class="block text-sm font-semibold text-gray-700 mb-3">
                                    <span id="body_label">📝 Isi Konten</span>
                                </label>
                                <x-forms.summernote-editor id="body_text" name="body_text" value="{{ old('body_text') }}" />
                                <p class="text-sm text-gray-500 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span id="body_hint">Gunakan editor untuk memformat teks dengan rich content</span>
                                </p>
                            </div>
                        </div>

                        <div id="video_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-6 border border-red-100">
                                <label for="body_video" class="block text-sm font-semibold text-gray-700 mb-3">
                                    🎥 URL Video YouTube/Vimeo
                                </label>
                                <input type="url"
                                       name="body_video"
                                       id="body_video"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-300"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       value="{{ old('body_video') }}">
                                <p class="text-sm text-gray-500 mt-2">Masukkan URL lengkap video dari YouTube atau Vimeo</p>
                            </div>
                        </div>

                        <div id="file_upload_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                                <label for="file_upload" class="block text-sm font-semibold text-gray-700 mb-3">
                                    📁 Unggah File
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-400 transition-colors duration-300">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <input type="file"
                                           name="file_upload"
                                           id="file_upload"
                                           class="hidden"
                                           onchange="handleFileSelect(this)">
                                    <label for="file_upload" class="cursor-pointer">
                                        <span class="text-indigo-600 font-medium hover:text-indigo-500">Klik untuk memilih file</span>
                                        <span class="text-gray-500"> atau drag & drop</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-2">Maksimal 10MB (PDF, DOCX, PPTX, JPG, PNG)</p>
                                </div>
                                <div id="file_preview" class="hidden mt-4 p-4 bg-white rounded-lg border">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900" id="file_name"></p>
                                                <p class="text-sm text-gray-500" id="file_size"></p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="quiz_form_fields" class="content-field hidden">
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">🧠 Pengaturan Kuis</h3>
                                
                                <div class="mb-4">
                                    <label for="time_limit" class="block text-sm font-semibold text-gray-700 mb-2">
                                        ⏱️ Durasi Pengerjaan (Menit)
                                    </label>
                                    <input 
                                        type="text"                      {{-- 1. Ubah tipe --}}
                                        inputmode="numeric"              {{-- Keyboard numerik di mobile --}}
                                        name="time_limit"                {{-- 2. Ganti nama agar sesuai Controller --}}
                                        id="time_limit"                  {{-- Ganti id agar sesuai label --}}
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"  {{-- 3. Tambah filter JS --}}
                                        class="w-full max-w-xs px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                        placeholder="Contoh: 60"
                                        value="{{ old('time_limit') }}"> {{-- Sesuaikan juga 'old' helper --}}
                                    <p class="text-sm text-gray-500 mt-2">Biarkan kosong atau isi 0 jika tidak ada batas waktu.</p>
                                </div>

                                <p class="text-center text-gray-600 italic">Pengaturan pertanyaan lebih lanjut tersedia dalam mode edit.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between mt-10 pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <a href="{{ route('courses.show', $lesson->course) }}"
                           class="inline-flex items-center px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>

                        <button type="button"
                                onclick="submitCreateForm()"
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Konten
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .content-type-card input:checked + div {
            @apply border-indigo-500 bg-indigo-50 shadow-lg;
        }

        .content-field {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .group:hover label {
            @apply text-indigo-600;
        }

        .step-active {
            @apply bg-indigo-600 text-white;
        }

        .step-completed {
            @apply bg-green-500 text-white;
        }
    </style>

    <script>
        function submitCreateForm() {
            document.getElementById('contentForm').submit();
        }

        function updateProgressStep(step) {
            // Update progress visual feedback
            const steps = document.querySelectorAll('.progress-step');
            steps.forEach((s, index) => {
                if (index < step) {
                    s.classList.add('step-completed');
                    s.classList.remove('step-active');
                } else if (index === step - 1) {
                    s.classList.add('step-active');
                }
            });
        }

        function toggleContentTypeFields() {
            const type = document.querySelector('input[name="type"]:checked')?.value;
            const allFields = document.querySelectorAll('.content-field');

            // Hide all fields first
            allFields.forEach(field => {
                field.classList.add('hidden');
            });

            $('#body_text').summernote('destroy');

            updateProgressStep(2);

            if (type === 'text' || type === 'essay') {
                document.getElementById('body_field').classList.remove('hidden');

                // Update labels based on type
                if (type === 'essay') {
                    document.getElementById('body_label').textContent = '✍️ Pertanyaan Esai';
                    document.getElementById('body_hint').textContent = 'Tulis pertanyaan esai yang akan dijawab oleh peserta';
                } else {
                    document.getElementById('body_label').textContent = '📝 Isi Konten';
                    document.getElementById('body_hint').textContent = 'Gunakan editor untuk memformat teks dengan rich content';
                }

            } else if (type === 'video') {
                document.getElementById('video_field').classList.remove('hidden');
            } else if (type === 'document' || type === 'image') {
                document.getElementById('file_upload_field').classList.remove('hidden');
            } else if (type === 'quiz') {
                document.getElementById('quiz_form_fields').classList.remove('hidden');
                // Load quiz form if needed
            }
        }

        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                const preview = document.getElementById('file_preview');
                const fileName = document.getElementById('file_name');
                const fileSize = document.getElementById('file_size');

                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                preview.classList.remove('hidden');
            }
        }

        function clearFile() {
            const input = document.getElementById('file_upload');
            const preview = document.getElementById('file_preview');

            input.value = '';
            preview.classList.add('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set default selection if none selected
            const checkedInput = document.querySelector('input[name="type"]:checked');
            if (!checkedInput) {
                document.querySelector('input[name="type"][value="text"]').checked = true;
            }
            toggleContentTypeFields();
        });

        // Add event listeners for radio buttons
        document.querySelectorAll('input[name="type"]').forEach(input => {
            input.addEventListener('change', toggleContentTypeFields);
        });
    </script>
</x-app-layout>