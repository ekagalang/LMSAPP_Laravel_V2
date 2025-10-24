<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="space-y-2">
                <a href="javascript:void(0)" onclick="window.history.back()"
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200 group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
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

                        <div id="essay_questions_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100" x-data="essayQuestionsManager()">
                                <div class="flex items-start mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                            ✍️
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900">Essay Assignment</h3>
                                        <p class="text-sm text-gray-600 mt-1">Buat beberapa pertanyaan essay dengan bobot nilai berbeda</p>
                                    </div>
                                </div>

                                {{-- Questions Container --}}
                                <div class="space-y-4">
                                    <template x-for="(question, index) in questions" :key="index">
                                        <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                            <div class="flex justify-between items-center mb-4">
                                                <h5 class="font-medium text-gray-700 flex items-center">
                                                    <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs mr-2" 
                                                        x-text="index + 1"></span>
                                                    <span x-text="'Pertanyaan ' + (index + 1)"></span>
                                                </h5>
                                                <button type="button" 
                                                        @click="removeQuestion(index)"
                                                        x-show="questions.length > 1"
                                                        class="text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Pertanyaan <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea 
                                                        x-model="question.text"
                                                        :name="'questions[' + index + '][text]'"
                                                        rows="4"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                        placeholder="Tulis pertanyaan essay..."
                                                        required
                                                    ></textarea>
                                                </div>
                                                
                                                <div class="w-40">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Skor Maksimal <span class="text-red-500">*</span>
                                                    </label>
                                                    <input 
                                                        type="number" 
                                                        x-model="question.max_score"
                                                        :name="'questions[' + index + '][max_score]'"
                                                        min="1" 
                                                        max="1000"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                        <button type="button" 
                                                @click="addQuestion()"
                                                class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Tambah Pertanyaan
                                        </button>
                                        
                                        <div class="text-sm text-gray-600">
                                            Total: <span x-text="questions.length" class="font-semibold"></span> soal, 
                                            <span x-text="totalScore" class="font-semibold text-green-600"></span> poin
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    function essayQuestionsManager() {
                                        return {
                                            questions: [{ text: '', max_score: 100 }],
                                            
                                            get totalScore() {
                                                return this.questions.reduce((total, q) => total + parseInt(q.max_score || 0), 0);
                                            },
                                            
                                            addQuestion() {
                                                this.questions.push({ text: '', max_score: 100 });
                                            },
                                            
                                            removeQuestion(index) {
                                                if (this.questions.length > 1) {
                                                    this.questions.splice(index, 1);
                                                }
                                            }
                                        }
                                    }
                                </script>
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

                                <!-- Multiple Documents Uploader (untuk tipe document) -->
                                <div class="mt-6" x-show="document.querySelector('input[name=\"type\"]:checked')?.value === 'document'">
                                    <label for="documents" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Lampirkan Beberapa Dokumen (opsional)
                                    </label>
                                    <input type="file" name="documents[]" id="documents" multiple
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,application/rtf"
                                           class="block w-full text-sm text-gray-600">
                                    <p class="text-xs text-gray-500 mt-1">Maks 20 file, masing-masing hingga 100MB.</p>
                                    <div id="documents_preview" class="mt-3 space-y-2"></div>
                                </div>

                                <!-- Multiple Images Uploader (untuk tipe image) -->
                                <div class="mt-6">
                                    <label for="images" class="block text-sm font-semibold text-gray-700 mb-2">
                                        🖼️ Unggah Beberapa Gambar (opsional)
                                    </label>
                                    <input type="file" name="images[]" id="images" accept="image/*" multiple class="block w-full text-sm text-gray-600">
                                    <p class="text-xs text-gray-500 mt-1">Anda dapat memilih lebih dari satu gambar. Setiap gambar maksimal 10MB.</p>
                                    <div id="images_preview" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
                                </div>
                            </div>
                        </div>

                        <div id="quiz_form_fields" class="content-field hidden">
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">🧠 Pengaturan Kuis</h3>

                                <!-- Quiz Creation Method Toggle -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        📋 Cara Membuat Kuis
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="quiz_method" value="manual" class="sr-only" onchange="toggleQuizMethod()" checked>
                                            <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-300 text-center quiz-method-card">
                                                <div class="text-3xl mb-2">✍️</div>
                                                <h4 class="font-semibold text-gray-900">Manual</h4>
                                                <p class="text-xs text-gray-500 mt-1">Buat nanti di edit</p>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="quiz_method" value="import" class="sr-only" onchange="toggleQuizMethod()">
                                            <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-300 text-center quiz-method-card">
                                                <div class="text-3xl mb-2">📊</div>
                                                <h4 class="font-semibold text-gray-900">Import Excel</h4>
                                                <p class="text-xs text-gray-500 mt-1">Upload file Excel</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Manual Method Fields -->
                                <div id="manual_quiz_fields">
                                    <div class="mb-4">
                                        <label for="time_limit" class="block text-sm font-semibold text-gray-700 mb-2">
                                            ⏱️ Durasi Pengerjaan (Menit)
                                        </label>
                                        <input
                                            type="text"
                                            inputmode="numeric"
                                            name="time_limit"
                                            id="time_limit"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            class="w-full max-w-xs px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                            placeholder="Contoh: 60"
                                            value="{{ old('time_limit') }}">
                                        <p class="text-sm text-gray-500 mt-2">Biarkan kosong atau isi 0 jika tidak ada batas waktu.</p>
                                    </div>

                                    <p class="text-center text-gray-600 italic">Pengaturan pertanyaan lebih lanjut tersedia dalam mode edit.</p>
                                </div>

                                <!-- Import Method Fields -->
                                <div id="import_quiz_fields" class="hidden">
                                    <!-- Download Template Section -->
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 mb-4 text-white">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-white/20 p-3 rounded-lg">
                                                    <i class="fas fa-download text-xl"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold">Template Excel</h4>
                                                    <p class="text-xs text-green-100">Download template terlebih dahulu</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('quizzes.download-template') }}" target="_blank"
                                               class="bg-white text-green-600 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2">
                                                <i class="fas fa-file-excel"></i>
                                                <span>Download</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- File Upload Section -->
                                    <div class="mb-4">
                                        <label for="quiz_excel_file" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-file-upload mr-2 text-orange-600"></i>Upload File Excel
                                        </label>
                                        <div class="flex items-center justify-center w-full">
                                            <label for="quiz_excel_file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-all duration-200">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                                    <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                                    <p class="text-xs text-gray-500">File Excel (XLSX, XLS) maksimal 2MB</p>
                                                    <p class="text-xs text-gray-400 mt-2" id="quiz_file_name"></p>
                                                </div>
                                                <input id="quiz_excel_file" name="quiz_excel_file" type="file" class="hidden" accept=".xlsx,.xls" onchange="updateQuizFileName(this)" />
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-info-circle text-blue-500"></i>
                                            File Excel harus sesuai dengan format template yang telah didownload
                                        </p>
                                    </div>

                                    <!-- Instructions -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-blue-900 mb-2 flex items-center">
                                            <i class="fas fa-lightbulb mr-2"></i>Panduan Cepat
                                        </h5>
                                        <ul class="text-sm text-blue-800 space-y-1">
                                            <li>• Download template Excel terlebih dahulu</li>
                                            <li>• Isi data quiz sesuai format yang ada</li>
                                            <li>• Satu quiz bisa memiliki banyak pertanyaan</li>
                                            <li>• Upload file Excel yang sudah diisi</li>
                                        </ul>
                                    </div>
                                </div>
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
        // Util: compress image using Canvas
        async function compressImageFile(file, { maxWidth = 1600, quality = 0.8 } = {}) {
            if (!(file && file.type && file.type.startsWith('image/'))) return file;

            // Skip tiny files (< 300KB) to save time
            if (file.size < 300 * 1024) return file;

            const bitmap = await createImageBitmap(file).catch(() => null);
            if (!bitmap) return file;

            let { width, height } = bitmap;
            if (width > maxWidth) {
                const ratio = maxWidth / width;
                width = Math.round(width * ratio);
                height = Math.round(height * ratio);
            }

            const canvas = document.createElement('canvas');
            canvas.width = width; canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(bitmap, 0, 0, width, height);

            // Prefer JPEG for photos; keep PNG to preserve transparency
            const targetType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';

            const blob = await new Promise(resolve => canvas.toBlob(resolve, targetType, quality));
            if (!blob) return file;
            const newName = file.name.replace(/\.(png|jpg|jpeg|webp)$/i, targetType === 'image/png' ? '.png' : '.jpg');
            return new File([blob], newName, { type: targetType, lastModified: Date.now() });
        }

        async function compressImageInputsIfNeeded(form) {
            try {
                const selectedType = (form.querySelector('input[name="type"]:checked') || {}).value;
                if (selectedType !== 'image') return; // only for image content

                // Handle single file_upload if it's an image
                const single = form.querySelector('#file_upload');
                if (single && single.files && single.files[0] && single.files[0].type.startsWith('image/')) {
                    const compressed = await compressImageFile(single.files[0]);
                    const dt = new DataTransfer();
                    dt.items.add(compressed);
                    single.files = dt.files;
                }

                // Handle multiple images[]
                const multi = form.querySelector('#images');
                if (multi && multi.files && multi.files.length) {
                    const dt = new DataTransfer();
                    for (const f of Array.from(multi.files)) {
                        const cf = await compressImageFile(f);
                        dt.items.add(cf);
                    }
                    multi.files = dt.files;
                }
            } catch (e) {
                console.warn('Compression skipped due to error:', e);
            }
        }
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

            // Destroy existing Summernote editor
            if ($('#body_text').hasClass('note-editor')) {
                $('#body_text').summernote('destroy');
            }

            updateProgressStep(2);

            if (type === 'text') {
                document.getElementById('body_field').classList.remove('hidden');
                document.getElementById('body_label').textContent = '📝 Isi Konten';
                document.getElementById('body_hint').textContent = 'Gunakan editor untuk memformat teks dengan rich content';
                
                // Initialize summernote for text with File Manager
                setTimeout(() => {
                    initSummernoteWithFileManager('#body_text', {
                        height: 300,
                        placeholder: 'Tulis konten pembelajaran di sini...'
                    });
                }, 100);

            } else if (type === 'essay') {
                // Show essay questions field instead of body field
                document.getElementById('essay_questions_field').classList.remove('hidden');
                
            } else if (type === 'video') {
                document.getElementById('video_field').classList.remove('hidden');
            } else if (type === 'document' || type === 'image') {
                document.getElementById('file_upload_field').classList.remove('hidden');
            } else if (type === 'quiz') {
                document.getElementById('quiz_form_fields').classList.remove('hidden');
            } else if (type === 'zoom') {
                document.getElementById('zoom_field').classList.remove('hidden');
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

        // Preview untuk multiple images
        (function(){
            const input = document.getElementById('images');
            const container = document.getElementById('images_preview');
            if (input && container) {
                input.addEventListener('change', function() {
                    container.innerHTML = '';
                    const files = Array.from(this.files || []);
                    files.slice(0, 20).forEach(file => {
                        if (!file.type.startsWith('image/')) return;
                        const url = URL.createObjectURL(file);
                        const el = document.createElement('img');
                        el.src = url;
                        el.className = 'w-full h-32 object-cover rounded-lg border';
                        container.appendChild(el);
                    });
                });
            }
        })();
        // Preview untuk multiple documents (tampilkan nama file)
        (function(){
            const input = document.getElementById('documents');
            const container = document.getElementById('documents_preview');
            if (input && container) {
                input.addEventListener('change', function() {
                    container.innerHTML = '';
                    const files = Array.from(this.files || []);
                    files.slice(0, 20).forEach(file => {
                        const row = document.createElement('div');
                        row.className = 'flex items-center justify-between p-2 rounded border';
                        const name = document.createElement('span');
                        name.className = 'text-sm text-gray-700 truncate';
                        name.textContent = file.name + ` (${Math.round(file.size/1024)} KB)`;
                        row.appendChild(name);
                        container.appendChild(row);
                    });
                });
            }
        })();

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

            // Hook form submit for client-side compression
            const form = document.getElementById('contentForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await compressImageInputsIfNeeded(form);
                    form.submit();
                });
            }
        });

        // Add event listeners for radio buttons
        document.querySelectorAll('input[name="type"]').forEach(input => {
            input.addEventListener('change', toggleContentTypeFields);
        });

        // Toggle quiz method (manual vs import)
        function toggleQuizMethod() {
            const method = document.querySelector('input[name="quiz_method"]:checked').value;
            const manualFields = document.getElementById('manual_quiz_fields');
            const importFields = document.getElementById('import_quiz_fields');

            if (method === 'manual') {
                manualFields.classList.remove('hidden');
                importFields.classList.add('hidden');
                // Clear excel file input
                document.getElementById('quiz_excel_file').value = '';
                document.getElementById('quiz_file_name').textContent = '';
            } else {
                manualFields.classList.add('hidden');
                importFields.classList.remove('hidden');
            }

            // Update card styling
            document.querySelectorAll('.quiz-method-card').forEach(card => {
                card.classList.remove('border-orange-500', 'bg-orange-50', 'shadow-lg');
            });
            const selectedCard = document.querySelector('input[name="quiz_method"]:checked').parentElement.querySelector('.quiz-method-card');
            selectedCard.classList.add('border-orange-500', 'bg-orange-50', 'shadow-lg');
        }

        // Update quiz file name display
        function updateQuizFileName(input) {
            const fileName = input.files[0]?.name;
            const fileNameDisplay = document.getElementById('quiz_file_name');
            if (fileName) {
                fileNameDisplay.textContent = 'File terpilih: ' + fileName;
                fileNameDisplay.classList.remove('text-gray-400');
                fileNameDisplay.classList.add('text-orange-600', 'font-semibold');
            }
        }
    </script>

    <style>
        .quiz-method-card input:checked + div {
            @apply border-orange-500 bg-orange-50 shadow-lg;
        }
    </style>
</x-app-layout>
