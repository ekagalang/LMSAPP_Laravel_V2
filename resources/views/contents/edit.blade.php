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
                    {{ $content->exists ? '✏️ Edit Konten' : '✨ Buat Konten Baru' }}
                </h1>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full font-medium">
                        {{ $lesson->title }}
                    </span>
                    <span class="text-gray-400">•</span>
                    <span>{{ $lesson->course->title }}</span>
                    @if($content->exists)
                        <span class="text-gray-400">•</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                            {{ ucfirst($content->type) }}
                        </span>
                    @endif
                </div>
            </div>
            @if($content->exists)
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                        Mode Edit
                    </span>
                    <div class="text-xs text-gray-500">
                        Terakhir diubah: {{ $content->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Ada beberapa kesalahan:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden"
                 x-data="contentFormManager({
                     content: {{ Js::from($content) }},
                     createUrl: '{{ route('lessons.contents.store', $lesson) }}',
                     updateUrl: '{{ $content->exists ? route('lessons.contents.update', [$lesson, $content]) : '' }}'
                 })"
                 x-init="initForm()">

                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">
                                {{ $content->exists ? 'Edit Konten' : 'Buat Konten Baru' }}
                            </h2>
                            <p class="text-indigo-100 mt-1">
                                {{ $content->exists ? 'Perbarui informasi konten pembelajaran' : 'Isi detail konten pembelajaran yang akan dibuat' }}
                            </p>
                        </div>
                        @if($content->exists)
                            <div class="text-indigo-100 text-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    ID: {{ $content->id }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Content -->
                <form id="contentForm" :action="formAction" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    <template x-if="content.id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Basic Information Section -->
                    <div class="space-y-6 mb-8">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm font-bold">1</div>
                                Informasi Dasar
                            </h3>
                        </div>

                        <!-- Title Field -->
                        <div class="group">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                📝 Judul Konten
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   x-model="content.title"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-lg"
                                   placeholder="Masukkan judul konten yang menarik..."
                                   required>
                        </div>

                        <!-- Description Field -->
                        <div class="group">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                📄 Deskripsi (Opsional)
                            </label>
                            <textarea name="description"
                                      id="description"
                                      x-model="content.description"
                                      rows="3"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                      placeholder="Berikan deskripsi singkat tentang konten ini..."></textarea>
                        </div>

                        <!-- Content Type Field -->
                        <div class="group">
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-3">
                                🎯 Tipe Konten
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                                <!-- Text -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="text" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'text' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">📝</div>
                                        <div class="text-xs font-medium">Teks</div>
                                    </div>
                                </label>

                                <!-- Video -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="video" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'video' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🎥</div>
                                        <div class="text-xs font-medium">Video</div>
                                    </div>
                                </label>

                                <!-- Document -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="document" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'document' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">📄</div>
                                        <div class="text-xs font-medium">Dokumen</div>
                                    </div>
                                </label>

                                <!-- Image -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="image" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'image' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🖼️</div>
                                        <div class="text-xs font-medium">Gambar</div>
                                    </div>
                                </label>

                                <!-- Quiz -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="quiz" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'quiz' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🧠</div>
                                        <div class="text-xs font-medium">Kuis</div>
                                    </div>
                                </label>

                                <!-- Essay -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="essay" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'essay' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">✍️</div>
                                        <div class="text-xs font-medium">Esai</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Content Section -->
                    <div class="space-y-6 mb-8">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm font-bold">2</div>
                                Konten <span x-text="getTypeLabel(content.type)" class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm"></span>
                            </h3>
                        </div>

                        <!-- Text/Essay Content -->
                        <div x-show="isType('text') || isType('essay')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                                <label for="body_editor" class="block text-sm font-semibold text-gray-700 mb-3">
                                    <span x-text="isType('essay') ? '✍️ Pertanyaan Esai' : '📝 Isi Konten'"></span>
                                </label>
                                <textarea name="body_text"
                                          id="body_editor"
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300">{{ old('body_text', $content->body) }}</textarea>
                                <p class="text-sm text-gray-500 mt-2" x-text="isType('essay') ? 'Tulis pertanyaan yang akan dijawab peserta' : 'Gunakan editor untuk memformat konten'"></p>
                            </div>
                        </div>

                        <!-- Video Content -->
                        <div x-show="isType('video')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-6 border border-red-100">
                                <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-3">
                                    🎥 URL Video YouTube/Vimeo
                                </label>
                                <input type="url"
                                       name="body_video"
                                       x-model="content.body"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-300"
                                       placeholder="https://www.youtube.com/watch?v=...">
                                <p class="text-sm text-gray-500 mt-2">Masukkan URL lengkap video dari YouTube atau Vimeo</p>

                                <!-- Video Preview -->
                                <div x-show="content.body && content.body.includes('youtube')" class="mt-4">
                                    <div class="bg-white rounded-lg p-4 border">
                                        <h4 class="font-medium text-gray-900 mb-2">Preview Video:</h4>
                                        <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-500">Video akan ditampilkan di sini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Content -->
                        <div x-show="isType('document') || isType('image')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    📁 <span x-text="isType('image') ? 'Unggah Gambar' : 'Unggah Dokumen'"></span>
                                </label>

                                <!-- Current File Display -->
                                <div x-show="content.file_path" class="mb-4 p-4 bg-white rounded-lg border border-green-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">File saat ini:</p>
                                                <a :href="`/storage/${content.file_path}`"
                                                   target="_blank"
                                                   class="text-green-600 hover:text-green-800 text-sm underline"
                                                   x-text="content.file_path ? content.file_path.split('/').pop() : ''"></a>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-400 transition-colors duration-300">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <input type="file" name="file_upload" id="file_upload" class="hidden">
                                    <label for="file_upload" class="cursor-pointer">
                                        <span class="text-green-600 font-medium hover:text-green-500">
                                            <span x-text="content.file_path ? 'Ganti file' : 'Pilih file'"></span>
                                        </span>
                                        <span class="text-gray-500"> atau drag & drop</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-2">Maksimal 10MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quiz Content -->
                        <div x-show="isType('quiz')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    🧠 Pengaturan Kuis
                                    <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-sm">
                                        <span x-text="content.quiz && content.quiz.questions ? content.quiz.questions.length : 0"></span> Pertanyaan
                                    </span>
                                </h3>
                                @include('quizzes.partials.full-quiz-form')
                            </div>
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="space-y-6 mb-8">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm font-bold">3</div>
                                Pengaturan Tambahan
                            </h3>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <label for="order" class="block text-sm font-semibold text-gray-700 mb-3">
                                🔢 Urutan Konten
                            </label>
                            <input type="number"
                                   name="order"
                                   id="order"
                                   x-model="content.order"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                   placeholder="Urutan otomatis jika kosong">
                            <p class="text-sm text-gray-500 mt-2">Nomor urutan untuk menentukan posisi konten dalam pelajaran</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('courses.show', $lesson->course) }}"
                               class="inline-flex items-center px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </a>

                            @if($content->exists)
                                <button type="button"
                                        onclick="if(confirm('Yakin ingin menghapus konten ini?')) { window.location.href='{{ route('lessons.contents.destroy', [$lesson, $content]) }}' }"
                                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            @endif
                        </div>

                        <button type="button"
                                @click="submitForm()"
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="content.id ? '💾 Simpan Perubahan' : '✨ Buat Konten'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.tiny.cloud/1/wfo9boig39silkud2152anvh7iaqnu9wf4wqh75iudy3mry6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        .animate-fadeIn {
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

        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function contentFormManager(data) {
            return {
                content: data.content,
                formAction: data.content.id ? data.updateUrl : data.createUrl,

                initForm() {
                    this.$watch('content.type', (newType) => this.handleTypeChange(newType));

                    // Fix quiz data conversion
                    if (this.content.quiz) {
                        this.content.quiz.show_answers_after_attempt = !!parseInt(this.content.quiz.show_answers_after_attempt);
                        if (this.content.quiz.questions) {
                            this.content.quiz.questions.forEach(q => {
                                if (q.options) {
                                    q.options.forEach(opt => {
                                        opt.is_correct = !!parseInt(opt.is_correct);
                                    });
                                }
                                if (q.type === 'true_false') {
                                    const correctOption = q.options.find(opt => opt.is_correct);
                                    q.correct_answer_tf = correctOption ? correctOption.option_text.toLowerCase() : 'false';
                                }
                            });
                        }
                    } else {
                        this.content.quiz = this.defaultQuizObject();
                    }

                    if (!this.content.quiz.questions || this.content.quiz.questions.length === 0) {
                        this.addQuestion();
                    }

                    this.handleTypeChange(this.content.type);
                },

                initTinymce() {
                    if (tinymce.get('body_editor')) tinymce.get('body_editor').destroy();
                    const textarea = document.getElementById('body_editor');
                    if (!textarea) return;

                    const initialContent = textarea.value;
                    tinymce.init({
                        selector: 'textarea#body_editor',
                        height: 400,
                        plugins: 'code table lists link image media autosave wordcount fullscreen template codesample',
                        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | indent outdent | bullist numlist | code codesample | table | link image media | fullscreen',
                        branding: false,
                        menubar: false,
                        statusbar: true,
                        resize: true,
                        skin: 'oxide',
                        setup: (editor) => {
                            editor.on('init', () => {
                                editor.setContent(initialContent);
                            });
                        }
                    });
                },

                isType(type) {
                    return this.content.type === type;
                },

                getTypeLabel(type) {
                    const labels = {
                        'text': 'Teks',
                        'video': 'Video',
                        'document': 'Dokumen',
                        'image': 'Gambar',
                        'quiz': 'Kuis',
                        'essay': 'Esai'
                    };
                    return labels[type] || type;
                },

                handleTypeChange(type) {
                    if (type === 'text' || type === 'essay') {
                        this.$nextTick(() => {
                            this.initTinymce();
                        });
                    } else {
                        if (tinymce.get('body_editor')) {
                            tinymce.get('body_editor').destroy();
                        }
                    }
                },

                submitForm() {
                    if (tinymce.get('body_editor')) {
                        tinymce.triggerSave();
                    }
                    document.getElementById('contentForm').submit();
                },

                // Quiz management methods
                defaultQuizObject() {
                    return {
                        title: this.content.title || '',
                        description: '',
                        total_marks: 100,
                        pass_marks: 70,
                        status: 'draft',
                        show_answers_after_attempt: false,
                        questions: []
                    };
                },

                addQuestion() {
                    this.content.quiz.questions.push({
                        id: null,
                        question_text: '',
                        type: 'multiple_choice',
                        marks: 10,
                        open: true,
                        options: [{ id: null, option_text: '', is_correct: false }],
                        correct_answer_tf: 'false',
                    });
                },

                defaultOptionObject() {
                    return { id: null, option_text: '', is_correct: false };
                },

                removeQuestion(qIndex) {
                    if (this.content.quiz.questions.length > 1) {
                        this.content.quiz.questions.splice(qIndex, 1);
                    }
                },

                addOption(qIndex) {
                    this.content.quiz.questions[qIndex].options.push(this.defaultOptionObject());
                },

                removeOption(qIndex, oIndex) {
                    if (this.content.quiz.questions[qIndex].options.length > 1) {
                        this.content.quiz.questions[qIndex].options.splice(oIndex, 1);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
