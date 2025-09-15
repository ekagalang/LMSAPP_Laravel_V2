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
                                            <p class="text-sm text-gray-500 mt-1">Upload file atau URL YouTube/Vimeo</p>
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
                                            <p class="text-sm text-gray-500 mt-1">PDF dengan preview, DOC, XLS, PPT</p>
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

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="zoom" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'zoom' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                📹
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Zoom Meeting</h3>
                                            <p class="text-sm text-gray-500 mt-1">Meeting online dengan jadwal</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="content-type-card cursor-pointer">
                                    <input type="radio" name="type" value="audio" class="sr-only" onchange="toggleContentTypeFields()" {{ old('type') == 'audio' ? 'checked' : '' }}>
                                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-indigo-300 transition-all duration-300 hover:shadow-lg group">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                                🎵
                                            </div>
                                            <h3 class="font-semibold text-gray-900">Audio Learning</h3>
                                            <p class="text-sm text-gray-500 mt-1">Audio dengan quiz interaktif</p>
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
                                <div class="mb-6">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="video_source" value="url" class="mr-2" onchange="toggleVideoSource()" checked>
                                            <span class="font-medium">🔗 URL Video</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="video_source" value="file" class="mr-2" onchange="toggleVideoSource()">
                                            <span class="font-medium">📁 Upload File</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="video_url_section">
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

                                <div id="video_file_section" class="hidden">
                                    <label for="video_file" class="block text-sm font-semibold text-gray-700 mb-3">
                                        🎬 Upload File Video
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-400 transition-colors duration-300"
                                         id="video_drop_zone"
                                         ondragover="handleDragOver(event)"
                                         ondragenter="handleDragEnter(event, 'video')"
                                         ondragleave="handleDragLeave(event, 'video')"
                                         ondrop="handleDrop(event, 'video_file')">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <input type="file"
                                               name="video_file"
                                               id="video_file"
                                               class="hidden"
                                               accept=".mp4,.mov,.avi,.mkv,.webm,.flv,.wmv,.3gp"
                                               onchange="handleVideoFileSelect(this)">
                                        <label for="video_file" class="cursor-pointer">
                                            <span class="text-red-600 font-medium hover:text-red-500">Klik untuk memilih video</span>
                                            <span class="text-gray-500"> atau drag & drop file di sini</span>
                                        </label>
                                        <p class="text-sm text-gray-500 mt-2">Format: MP4, MOV, AVI, MKV, WebM, FLV, WMV, 3GP</p>
                                        <p class="text-xs text-gray-400 mt-1">Maksimal 500MB</p>
                                    </div>
                                    <div id="video_upload_progress" class="hidden mt-4">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2">Uploading video...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="file_upload_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                                <label for="file_upload" class="block text-sm font-semibold text-gray-700 mb-3">
                                    📁 Unggah File
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-400 transition-colors duration-300"
                                     id="document_drop_zone"
                                     ondragover="handleDragOver(event)"
                                     ondragenter="handleDragEnter(event, 'document')"
                                     ondragleave="handleDragLeave(event, 'document')"
                                     ondrop="handleDrop(event, 'file_upload')">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <input type="file"
                                           name="file_upload"
                                           id="file_upload"
                                           class="hidden"
                                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,image/*"
                                           onchange="handleFileSelect(this)">
                                    <label for="file_upload" class="cursor-pointer">
                                        <span class="text-indigo-600 font-medium hover:text-indigo-500">Klik untuk memilih file</span>
                                        <span class="text-gray-500"> atau drag & drop file di sini</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-2">Maksimal 100MB - PDF (preview inline), DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT</p>
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

                        <div id="zoom_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-6 border border-indigo-100">
                                <div class="flex items-start mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                                            📹
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900">Zoom Meeting</h3>
                                        <p class="text-sm text-gray-600 mt-1">Konfigurasi meeting online dengan penjadwalan</p>
                                    </div>
                                </div>

                                <!-- Zoom Link -->
                                <div class="mb-6">
                                    <label for="zoom_link" class="block text-sm font-semibold text-gray-700 mb-3">
                                        🔗 Link Zoom Meeting
                                    </label>
                                    <input type="url" name="zoom_link" id="zoom_link"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                           placeholder="https://zoom.us/j/..."
                                           value="{{ old('zoom_link') }}">
                                </div>

                                <!-- Meeting ID -->
                                <div class="mb-6">
                                    <label for="zoom_meeting_id" class="block text-sm font-semibold text-gray-700 mb-3">
                                        🆔 Meeting ID
                                    </label>
                                    <input type="text" name="zoom_meeting_id" id="zoom_meeting_id"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                           placeholder="123 456 7890"
                                           value="{{ old('zoom_meeting_id') }}">
                                </div>

                                <!-- Password -->
                                <div class="mb-6">
                                    <label for="zoom_password" class="block text-sm font-semibold text-gray-700 mb-3">
                                        🔒 Password (Opsional)
                                    </label>
                                    <input type="text" name="zoom_password" id="zoom_password"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                           placeholder="Password meeting"
                                           value="{{ old('zoom_password') }}">
                                </div>

                                <!-- Schedule Settings -->
                                <div class="mb-6">
                                    <div class="flex items-center mb-3">
                                        <input type="checkbox" id="is_scheduled" name="is_scheduled" value="1"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               onchange="toggleScheduleFields()" {{ old('is_scheduled') ? 'checked' : '' }}>
                                        <label for="is_scheduled" class="ml-2 text-sm font-medium text-gray-700">
                                            📅 Jadwalkan Meeting
                                        </label>
                                    </div>

                                    <div id="schedule_fields" class="space-y-4" style="display: none;">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="scheduled_start" class="block text-sm font-medium text-gray-700 mb-2">
                                                    ⏰ Waktu Mulai
                                                </label>
                                                <input type="datetime-local" name="scheduled_start" id="scheduled_start"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                                       value="{{ old('scheduled_start') }}">
                                            </div>
                                            <div>
                                                <label for="scheduled_end" class="block text-sm font-medium text-gray-700 mb-2">
                                                    ⏰ Waktu Berakhir
                                                </label>
                                                <input type="datetime-local" name="scheduled_end" id="scheduled_end"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                                       value="{{ old('scheduled_end') }}">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                                🌍 Zona Waktu
                                            </label>
                                            <select name="timezone" id="timezone"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                                                <option value="Asia/Jakarta" {{ old('timezone', 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Kuala_Lumpur" {{ old('timezone') == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Asia/Kuala_Lumpur</option>
                                                <option value="Asia/Singapore" {{ old('timezone') == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore</option>
                                                <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-indigo-100 rounded-lg p-4">
                                    <p class="text-center text-indigo-800 text-sm">
                                        <strong>💡 Tips:</strong> Pastikan link dan meeting ID zoom sudah benar.
                                        Jika dijadwalkan, meeting hanya dapat diakses pada waktu yang ditentukan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div id="audio_field" class="content-field hidden">
                            <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl p-6 border border-teal-100">
                                <div class="flex items-start mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center">
                                            🎵
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900">Audio Learning</h3>
                                        <p class="text-sm text-gray-600 mt-1">Upload audio file dan buat soal interaktif untuk pembelajaran bahasa</p>
                                    </div>
                                </div>

                                <!-- Audio Upload -->
                                <div class="mb-6">
                                    <label for="audio_file" class="block text-sm font-semibold text-gray-700 mb-3">
                                        🎧 File Audio
                                    </label>
                                    <div class="border-2 border-dashed border-teal-300 rounded-xl p-8 text-center hover:border-teal-400 transition-colors duration-300"
                                         id="audio_drop_zone"
                                         ondragover="handleDragOver(event)"
                                         ondragenter="handleDragEnter(event, 'audio')"
                                         ondragleave="handleDragLeave(event, 'audio')"
                                         ondrop="handleDrop(event, 'audio_file')">
                                        <div class="mb-4">
                                            <svg class="mx-auto h-12 w-12 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
                                            </svg>
                                        </div>
                                        <div class="text-sm">
                                            <label for="audio_file" class="relative cursor-pointer bg-white rounded-md font-medium text-teal-600 hover:text-teal-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500">
                                                <span>Upload audio file</span>
                                                <input id="audio_file" name="audio_file" type="file" class="sr-only"
                                                       accept="audio/*" onchange="handleAudioSelect(this)">
                                            </label>
                                            <p class="pl-1">atau drag and drop file di sini</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">MP3, WAV, atau M4A up to 50MB</p>
                                    </div>

                                    <div id="audio_preview" class="mt-4 p-4 bg-teal-50 rounded-lg border border-teal-200" style="display: none;">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-8 w-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900" id="audio_name"></p>
                                                    <p class="text-sm text-gray-500" id="audio_size"></p>
                                                </div>
                                            </div>
                                            <audio id="audio_player" controls class="ml-4">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    </div>
                                </div>

                                <!-- Audio Transcript -->
                                <div class="mb-6">
                                    <label for="audio_transcript" class="block text-sm font-semibold text-gray-700 mb-3">
                                        📝 Transkrip Audio (Opsional)
                                    </label>
                                    <textarea name="audio_transcript" id="audio_transcript" rows="4"
                                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all duration-300 placeholder-gray-400"
                                              placeholder="Masukkan transkrip audio untuk membantu pembelajaran...">{{ old('audio_transcript') }}</textarea>
                                    <p class="text-sm text-gray-500 mt-2">Transkrip akan membantu learner memahami audio dengan lebih baik</p>
                                </div>

                                <!-- Difficulty Level -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        📊 Tingkat Kesulitan
                                    </label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="audio_difficulty" value="beginner"
                                                   class="form-radio h-4 w-4 text-teal-600" {{ old('audio_difficulty', 'beginner') == 'beginner' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">🟢 Beginner</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="audio_difficulty" value="intermediate"
                                                   class="form-radio h-4 w-4 text-teal-600" {{ old('audio_difficulty') == 'intermediate' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">🟡 Intermediate</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="audio_difficulty" value="advanced"
                                                   class="form-radio h-4 w-4 text-teal-600" {{ old('audio_difficulty') == 'advanced' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">🔴 Advanced</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Audio Learning Type Selection -->
                                <div class="mb-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-4">🎯 Tipe Audio Learning</h4>

                                    <div class="space-y-3">
                                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl hover:border-teal-300 transition-colors cursor-pointer">
                                            <input type="radio" name="audio_type" value="simple" class="mt-1 mr-3 text-teal-600"
                                                   onchange="toggleAudioTypeFields()" checked>
                                            <div>
                                                <div class="font-medium text-gray-800">📚 Audio Content Sederhana</div>
                                                <div class="text-sm text-gray-600 mt-1">Audio dengan quiz sederhana sebagai bagian course</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl hover:border-teal-300 transition-colors cursor-pointer">
                                            <input type="radio" name="audio_type" value="existing_lesson" class="mt-1 mr-3 text-teal-600"
                                                   onchange="toggleAudioTypeFields()">
                                            <div>
                                                <div class="font-medium text-gray-800">🔗 Link ke Audio Learning</div>
                                                <div class="text-sm text-gray-600 mt-1">Hubungkan dengan audio learning yang sudah ada</div>
                                            </div>
                                        </label>

                                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl hover:border-teal-300 transition-colors cursor-pointer">
                                            <input type="radio" name="audio_type" value="new_lesson" class="mt-1 mr-3 text-teal-600"
                                                   onchange="toggleAudioTypeFields()">
                                            <div>
                                                <div class="font-medium text-gray-800">✨ Buat Audio Learning Baru</div>
                                                <div class="text-sm text-gray-600 mt-1">Buat audio learning lengkap yang juga muncul di halaman Audio Learning</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Existing Audio Lesson Selection -->
                                <div id="existing_lesson_section" class="mb-6" style="display: none;">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        🎵 Pilih Audio Learning
                                    </label>
                                    <select name="audio_lesson_id" id="audio_lesson_id"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all duration-300">
                                        <option value="">Pilih audio learning yang sudah ada...</option>
                                        @foreach(App\Models\AudioLesson::active()->availableForCourses()->get() as $audioLesson)
                                            <option value="{{ $audioLesson->id }}" {{ old('audio_lesson_id') == $audioLesson->id ? 'selected' : '' }}>
                                                {{ $audioLesson->title }} ({{ ucfirst($audioLesson->difficulty_level) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- New Audio Learning Creation -->
                                <div id="new_lesson_section" class="mb-6" style="display: none;">
                                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200">
                                        <h4 class="font-semibold text-gray-800 mb-4">✨ Buat Audio Learning Baru</h4>
                                        <p class="text-sm text-gray-600 mb-4">Audio learning ini akan muncul di halaman Audio Learning dan dapat digunakan di course lain</p>

                                        <!-- Audio Learning Title -->
                                        <div class="mb-4">
                                            <label for="new_lesson_title" class="block text-sm font-medium text-gray-700 mb-2">
                                                📝 Judul Audio Learning
                                            </label>
                                            <input type="text" name="new_lesson_title" id="new_lesson_title"
                                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300"
                                                   placeholder="Contoh: Basic English Conversation - Greetings"
                                                   value="{{ old('new_lesson_title') }}">
                                        </div>

                                        <!-- Audio Learning Description -->
                                        <div class="mb-4">
                                            <label for="new_lesson_description" class="block text-sm font-medium text-gray-700 mb-2">
                                                📄 Deskripsi Audio Learning
                                            </label>
                                            <textarea name="new_lesson_description" id="new_lesson_description" rows="3"
                                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300"
                                                      placeholder="Deskripsi singkat tentang apa yang akan dipelajari...">{{ old('new_lesson_description') }}</textarea>
                                        </div>

                                        <!-- Audio Learning Category -->
                                        <div class="mb-4">
                                            <label for="new_lesson_category" class="block text-sm font-medium text-gray-700 mb-2">
                                                🏷️ Kategori
                                            </label>
                                            <select name="new_lesson_category" id="new_lesson_category"
                                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300">
                                                <option value="conversation">💬 Conversation</option>
                                                <option value="listening">👂 Listening</option>
                                                <option value="pronunciation">🗣️ Pronunciation</option>
                                                <option value="grammar">📚 Grammar</option>
                                                <option value="vocabulary">📝 Vocabulary</option>
                                                <option value="business">💼 Business English</option>
                                                <option value="academic">🎓 Academic</option>
                                                <option value="general">🌟 General</option>
                                            </select>
                                        </div>

                                        <div class="bg-purple-100 rounded-lg p-3 border border-purple-200">
                                            <p class="text-sm text-purple-800">
                                                💡 <strong>Tips:</strong> Audio learning yang dibuat akan memiliki sistem exercise yang lebih lengkap dan muncul di halaman Audio Learning untuk bisa digunakan di course lain.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Simple Quiz Integration (for simple type) -->
                                <div id="simple_quiz_section" class="mb-6">
                                    <div class="flex items-center mb-3">
                                        <input type="checkbox" id="audio_has_quiz" name="audio_has_quiz" value="1"
                                               class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                               onchange="toggleAudioQuizFields()" {{ old('audio_has_quiz', true) ? 'checked' : '' }}>
                                        <label for="audio_has_quiz" class="ml-2 text-sm font-medium text-gray-700">
                                            🧠 Tambahkan Quiz Interaktif
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500">Aktifkan untuk membuat soal interaktif berdasarkan audio</p>
                                </div>

                                <!-- Quiz Settings (initially hidden) -->
                                <div id="audio_quiz_fields" class="mb-6" style="display: none;">
                                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-4 border border-orange-200">
                                        <h4 class="font-medium text-gray-900 mb-4">⚙️ Pengaturan Quiz Audio</h4>

                                        <!-- Time Limit -->
                                        <div class="mb-4">
                                            <label for="audio_time_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                                ⏱️ Durasi Pengerjaan (Menit)
                                            </label>
                                            <input type="text"
                                                   inputmode="numeric"
                                                   name="audio_time_limit"
                                                   id="audio_time_limit"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                   class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all duration-200"
                                                   placeholder="60"
                                                   value="{{ old('audio_time_limit', '30') }}">
                                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk tanpa batas waktu</p>
                                        </div>

                                        <!-- Quiz Type Selection -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                📝 Tipe Soal yang Akan Ditambahkan
                                            </label>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="audio_quiz_types[]" value="multiple_choice"
                                                           class="form-checkbox h-4 w-4 text-teal-600" checked>
                                                    <span class="ml-2 text-sm text-gray-700">📋 Multiple Choice</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="audio_quiz_types[]" value="fill_blank"
                                                           class="form-checkbox h-4 w-4 text-teal-600">
                                                    <span class="ml-2 text-sm text-gray-700">✏️ Fill in the Blanks</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="audio_quiz_types[]" value="true_false"
                                                           class="form-checkbox h-4 w-4 text-teal-600">
                                                    <span class="ml-2 text-sm text-gray-700">✅ True/False</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="audio_quiz_types[]" value="listening_comprehension"
                                                           class="form-checkbox h-4 w-4 text-teal-600">
                                                    <span class="ml-2 text-sm text-gray-700">👂 Listening Comprehension</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="bg-teal-50 rounded-lg p-3 border border-teal-200">
                                            <div class="flex items-start">
                                                <svg class="w-4 h-4 text-teal-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm text-teal-800 font-medium mb-1">Cara Kerja Audio Quiz:</p>
                                                    <div class="text-sm text-teal-700 space-y-1">
                                                        <p>• <strong>Centang tipe soal</strong> yang ingin dibuat (Multiple Choice, True/False, dll)</p>
                                                        <p>• <strong>Setelah save</strong>, template soal akan otomatis dibuat sesuai pilihan</p>
                                                        <p>• <strong>Edit detail soal</strong> di halaman edit konten - ada form editor lengkap</p>
                                                        <p>• <strong>Students</strong> akan mendengar audio sambil mengerjakan quiz</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-teal-100 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-teal-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-teal-800">
                                                <strong>Fitur Audio Learning:</strong>
                                            </p>
                                            <ul class="text-sm text-teal-700 mt-2 space-y-1">
                                                <li>• Player audio dengan kontrol kecepatan</li>
                                                <li>• Transcript yang dapat ditampilkan/disembunyikan</li>
                                                <li>• Progress tracking untuk pembelajaran</li>
                                                <li>• Quiz interaktif berbasis audio (opsional)</li>
                                            </ul>
                                        </div>
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
                
                // Initialize summernote for text
                setTimeout(() => {
                    $('#body_text').summernote({
                        height: 300,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['fontname', ['fontname']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'help']]
                        ]
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
            } else if (type === 'audio') {
                document.getElementById('audio_field').classList.remove('hidden');
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

        function handleAudioSelect(input) {
            const file = input.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('audio/')) {
                    alert('Please select a valid audio file.');
                    input.value = '';
                    return;
                }

                // Validate file size (50MB max)
                if (file.size > 50 * 1024 * 1024) {
                    alert('File size should not exceed 50MB.');
                    input.value = '';
                    return;
                }

                const preview = document.getElementById('audio_preview');
                const audioName = document.getElementById('audio_name');
                const audioSize = document.getElementById('audio_size');
                const audioPlayer = document.getElementById('audio_player');

                audioName.textContent = file.name;
                audioSize.textContent = formatFileSize(file.size);

                // Create URL for audio preview
                const url = URL.createObjectURL(file);
                audioPlayer.src = url;

                preview.style.display = 'block';
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function toggleAudioTypeFields() {
            const audioType = document.querySelector('input[name="audio_type"]:checked')?.value;
            const simpleSection = document.getElementById('simple_quiz_section');
            const existingSection = document.getElementById('existing_lesson_section');
            const newSection = document.getElementById('new_lesson_section');
            const audioUploadSection = document.querySelector('#audio_field .mb-6:first-child');
            const transcriptSection = document.getElementById('audio_transcript').closest('.mb-6');
            const difficultySection = document.querySelector('input[name="audio_difficulty"]').closest('.mb-6');

            // Hide all sections first
            if (simpleSection) simpleSection.style.display = 'none';
            if (existingSection) existingSection.style.display = 'none';
            if (newSection) newSection.style.display = 'none';

            // Show/hide audio upload and transcript based on type
            if (audioUploadSection) {
                if (audioType === 'existing_lesson') {
                    audioUploadSection.style.display = 'none';
                    transcriptSection.style.display = 'none';
                    difficultySection.style.display = 'none';
                } else {
                    audioUploadSection.style.display = 'block';
                    transcriptSection.style.display = 'block';
                    difficultySection.style.display = 'block';
                }
            }

            // Show appropriate section
            switch (audioType) {
                case 'simple':
                    if (simpleSection) simpleSection.style.display = 'block';
                    break;
                case 'existing_lesson':
                    if (existingSection) existingSection.style.display = 'block';
                    break;
                case 'new_lesson':
                    if (newSection) newSection.style.display = 'block';
                    break;
            }
        }

        function toggleAudioQuizFields() {
            const checkbox = document.getElementById('audio_has_quiz');
            const quizFields = document.getElementById('audio_quiz_fields');

            if (checkbox && quizFields) {
                if (checkbox.checked) {
                    quizFields.style.display = 'block';
                } else {
                    quizFields.style.display = 'none';
                }
            }
        }

        // Initialize audio quiz fields visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Show quiz fields by default when audio has quiz is checked
            const audioHasQuizCheckbox = document.getElementById('audio_has_quiz');
            if (audioHasQuizCheckbox && audioHasQuizCheckbox.checked) {
                toggleAudioQuizFields();
            }
        });

        function toggleScheduleFields() {
            const checkbox = document.getElementById('is_scheduled');
            const scheduleFields = document.getElementById('schedule_fields');

            if (checkbox && scheduleFields) {
                if (checkbox.checked) {
                    scheduleFields.style.display = 'block';
                } else {
                    scheduleFields.style.display = 'none';
                }
            }
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

        // Video source toggle function
        function toggleVideoSource() {
            const source = document.querySelector('input[name="video_source"]:checked')?.value;
            const urlSection = document.getElementById('video_url_section');
            const fileSection = document.getElementById('video_file_section');

            if (source === 'file') {
                urlSection.classList.add('hidden');
                fileSection.classList.remove('hidden');
                // Clear URL input when switching to file
                document.getElementById('body_video').value = '';
            } else {
                urlSection.classList.remove('hidden');
                fileSection.classList.add('hidden');
                // Clear file input when switching to URL
                document.getElementById('video_file').value = '';
            }
        }

        // Video file upload handler
        function handleVideoFileSelect(input) {
            const file = input.files[0];
            if (!file) return;

            const maxSize = 500 * 1024 * 1024; // 500MB
            if (file.size > maxSize) {
                alert('File terlalu besar. Maksimal 500MB.');
                input.value = '';
                return;
            }

            // Show upload progress (simulated)
            const progressDiv = document.getElementById('video_upload_progress');
            const progressBar = progressDiv.querySelector('.bg-red-500');
            const progressText = progressDiv.querySelector('p');

            progressDiv.classList.remove('hidden');
            progressText.textContent = `Mengupload ${file.name}...`;

            // Simulate upload progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    progressText.textContent = 'Upload selesai!';
                    setTimeout(() => {
                        progressDiv.classList.add('hidden');
                    }, 2000);
                }
                progressBar.style.width = progress + '%';
            }, 200);
        }

        // Drag and Drop Functionality
        function handleDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'copy';
        }

        function handleDragEnter(event, type) {
            event.preventDefault();
            const dropZone = event.currentTarget;

            // Add visual feedback based on type
            switch(type) {
                case 'document':
                    dropZone.classList.add('border-green-500', 'bg-green-50');
                    dropZone.classList.remove('border-gray-300');
                    break;
                case 'video':
                    dropZone.classList.add('border-red-500', 'bg-red-50');
                    dropZone.classList.remove('border-gray-300');
                    break;
                case 'audio':
                    dropZone.classList.add('border-teal-500', 'bg-teal-50');
                    dropZone.classList.remove('border-teal-300');
                    break;
            }
        }

        function handleDragLeave(event, type) {
            event.preventDefault();
            const dropZone = event.currentTarget;

            // Remove visual feedback and restore original state
            switch(type) {
                case 'document':
                    dropZone.classList.remove('border-green-500', 'bg-green-50');
                    dropZone.classList.add('border-gray-300');
                    break;
                case 'video':
                    dropZone.classList.remove('border-red-500', 'bg-red-50');
                    dropZone.classList.add('border-gray-300');
                    break;
                case 'audio':
                    dropZone.classList.remove('border-teal-500', 'bg-teal-50');
                    dropZone.classList.add('border-teal-300');
                    break;
            }
        }

        function handleDrop(event, inputId) {
            event.preventDefault();

            const dropZone = event.currentTarget;
            const files = event.dataTransfer.files;

            // Remove drag visual feedback
            dropZone.classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50', 'border-teal-500', 'bg-teal-50');
            dropZone.classList.add('border-gray-300');
            if (inputId === 'audio_file') {
                dropZone.classList.remove('border-gray-300');
                dropZone.classList.add('border-teal-300');
            }

            if (files.length === 0) return;

            const file = files[0];
            const input = document.getElementById(inputId);

            // Validate file based on input type
            const accept = input.getAttribute('accept');
            const isValid = validateFileType(file, accept);

            if (!isValid) {
                alert('Tipe file tidak didukung. Silakan pilih file yang sesuai.');
                return;
            }

            // Create a new FileList object with the dropped file
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            // Trigger the appropriate handler
            switch(inputId) {
                case 'file_upload':
                    handleFileSelect(input);
                    break;
                case 'video_file':
                    handleVideoFileSelect(input);
                    break;
                case 'audio_file':
                    handleAudioSelect(input);
                    break;
            }
        }

        function validateFileType(file, acceptString) {
            if (!acceptString) return true;

            const acceptedTypes = acceptString.split(',').map(type => type.trim().toLowerCase());
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            const fileMimeType = file.type.toLowerCase();

            // Check against file extensions and MIME types
            return acceptedTypes.some(type => {
                if (type.startsWith('.')) {
                    return fileExtension === type;
                } else if (type.includes('/*')) {
                    const mainType = type.split('/')[0];
                    return fileMimeType.startsWith(mainType + '/');
                } else {
                    return fileMimeType === type;
                }
            });
        }
    </script>
</x-app-layout>