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

    <style>
        @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
        .shake { animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both; }
        .form-input-error { border-color: #ef4444 !important; }
        .form-input-error:focus { border-color: #ef4444 !important; ring-color: #fee2e2 !important; }
    </style>

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

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden"
                 x-data="contentFormManager({
                     content: {{ Js::from($content) }},
                     createUrl: '{{ route('lessons.contents.store', $lesson) }}',
                     updateUrl: '{{ $content->exists ? route('lessons.contents.update', [$lesson, $content]) : '' }}'
                 })"
                 x-init="initForm()">

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

                <form id="contentForm" :action="formAction" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    <template x-if="content.id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-6 mb-8">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm font-bold">1</div>
                                Informasi Dasar
                            </h3>
                        </div>

                        <div class="group">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                📝 Judul Konten
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   x-model="content.title"
                                   {{-- ✅ TAMBAHKAN :class untuk error --}}
                                   :class="{ 'form-input-error': errors.title }"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 text-lg"
                                   placeholder="Masukkan judul konten yang menarik...">
                            {{-- ✅ TAMBAHKAN pesan error --}}
                            <p x-show="errors.title" x-text="errors.title" class="text-sm text-red-600 mt-1"></p>
                        </div>

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

                        <div class="group">
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-3">
                                🎯 Tipe Konten
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="text" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'text' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">📝</div>
                                        <div class="text-xs font-medium">Teks</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="video" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'video' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🎥</div>
                                        <div class="text-xs font-medium">Video</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="document" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'document' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">📄</div>
                                        <div class="text-xs font-medium">Dokumen</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="image" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'image' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🖼️</div>
                                        <div class="text-xs font-medium">Gambar</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="quiz" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'quiz' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🧠</div>
                                        <div class="text-xs font-medium">Kuis</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="essay" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'essay' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">✍️</div>
                                        <div class="text-xs font-medium">Esai</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="zoom" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'zoom' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">💻</div>
                                        <div class="text-xs font-medium">Zoom</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 mb-8">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm font-bold">2</div>
                                Konten <span x-text="getTypeLabel(content.type)" class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm"></span>
                            </h3>
                        </div>

                        <div x-show="isType('text')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                                <label for="body_editor" class="block text-sm font-semibold text-gray-700 mb-3">
                                    📝 Isi Konten
                                </label>
                                <p class="text-xs text-gray-600 mb-4">Gunakan editor untuk memformat teks dengan rich content</p>
                                
                                <x-forms.summernote-editor 
                                    id="body_editor" 
                                    name="body_text" 
                                    :value="old('body_text', $content->body ?? '')" 
                                />
                                @error('body_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div x-show="isType('essay')" x-cloak class="animate-fadeIn" x-data="essayQuestionsManager()">
                            <div class="space-y-6">
                                {{-- Info Box --}}
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                                    <div class="flex items-start">
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
                                </div>

                                {{-- Existing Questions (hanya tampil info, belum bisa edit) --}}
                                @if($content->exists && $content->essayQuestions && $content->essayQuestions->count() > 0)
                                    <div class="space-y-4">
                                        <h4 class="font-semibold text-gray-900 flex items-center">
                                            <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2 text-xs">
                                                {{ $content->essayQuestions->count() }}
                                            </div>
                                            Pertanyaan yang Sudah Ada
                                        </h4>
                                        @foreach($content->essayQuestions as $index => $question)
                                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                                <div class="flex justify-between items-start mb-2">
                                                    <span class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">
                                                        Soal {{ $index + 1 }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                        {{ $question->max_score }} poin
                                                    </span>
                                                </div>
                                                <div class="text-sm text-gray-700 leading-relaxed">
                                                    {!! nl2br(e($question->question)) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                            <p class="text-sm text-blue-800">
                                                <strong>💡 Info:</strong> Pertanyaan di atas sudah tersimpan. 
                                                Gunakan form di bawah untuk menambah pertanyaan baru.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Form untuk menambah questions baru --}}
                                <div class="border border-gray-200 rounded-lg">
                                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                        <h4 class="font-semibold text-gray-900 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            @if($content->exists && $content->essayQuestions && $content->essayQuestions->count() > 0)
                                                Tambah Pertanyaan Baru
                                            @else  
                                                Pertanyaan Essay
                                            @endif
                                        </h4>
                                    </div>

                                    <div class="p-6 space-y-6">
                                        {{-- Questions Container --}}
                                        <template x-for="(question, index) in questions" :key="index">
                                            <div class="question-item border border-gray-200 rounded-lg p-4 bg-gray-50">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h5 class="font-medium text-gray-700 flex items-center">
                                                        <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs mr-2" 
                                                            x-text="index + 1"></span>
                                                        <span x-text="'Pertanyaan ' + (index + 1)"></span>
                                                    </h5>
                                                    <button type="button" 
                                                            @click="removeQuestion(index)"
                                                            x-show="questions.length > 1"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
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
                                                            placeholder="Tulis pertanyaan essay yang akan dijawab oleh peserta..."
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

                                        {{-- Add Question & Summary --}}
                                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                            <button type="button" 
                                                    @click="addQuestion()"
                                                    class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Tambah Pertanyaan
                                            </button>
                                            
                                            <div class="text-right">
                                                <p class="text-sm text-gray-600">
                                                    Pertanyaan Baru: <span x-text="questions.length" class="font-semibold"></span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Total Skor Baru: <span x-text="totalScore" class="font-semibold text-green-600"></span> poin
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Legacy Support --}}
                                @if($content->exists && $content->body && (!$content->essayQuestions || $content->essayQuestions->count() === 0))
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <h4 class="font-medium text-yellow-800 mb-1">Essay Lama Terdeteksi</h4>
                                                <p class="text-sm text-yellow-700 mb-3">
                                                    Content ini masih menggunakan sistem essay lama. 
                                                    Pertanyaan lama: "{{ Str::limit(strip_tags($content->body), 100) }}"
                                                </p>
                                                <p class="text-xs text-yellow-600">
                                                    Gunakan form di atas untuk membuat multiple questions. Sistem akan menggunakan yang baru.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

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
                                @error('body_video')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

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

                        <div x-show="isType('document') || isType('image')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    📁 <span x-text="isType('image') ? 'Unggah Gambar' : 'Unggah Dokumen'"></span>
                                </label>

                                <div x-show="content.file_path && !uploadedFileName" class="mb-4 p-4 bg-white rounded-lg border border-green-200">
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

                                <div x-show="uploadedFileName" class="mb-4 p-4 bg-white rounded-lg border border-indigo-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">File dipilih:</p>
                                                <span class="text-indigo-600 text-sm" x-text="uploadedFileName"></span>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs">Baru</span>
                                    </div>
                                    {{-- Preview untuk gambar --}}
                                    <div x-show="isType('image') && uploadedImagePreviewUrl" class="mt-4">
                                        <img :src="uploadedImagePreviewUrl" class="max-h-48 rounded-lg mx-auto">
                                    </div>
                                </div>

                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-400 transition-colors duration-300">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <input type="file" name="file_upload" id="file_upload" class="hidden"
                                        @change="handleFileUpload($event)"
                                        :accept="isType('image') ? 'image/*' : ''">
                                    <label for="file_upload" class="cursor-pointer">
                                        <span class="text-green-600 font-medium hover:text-green-500">
                                            <span x-text="content.file_path ? 'Ganti file' : 'Pilih file'"></span>
                                        </span>
                                        <span class="text-gray-500"> atau drag & drop</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-2">Maksimal 100MB</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="isType('quiz')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    🧠 Pengaturan Kuis
                                    <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-sm">
                                        <span x-text="content.quiz && content.quiz.questions ? content.quiz.questions.length : 0"></span> Pertanyaan
                                    </span>
                                </h3>
                                
                                <div class="mb-4">
                                    <label for="quiz_duration" class="block text-sm font-semibold text-gray-700 mb-2">
                                        ⏱️ Durasi Pengerjaan (Menit)
                                    </label>
                                    <input 
                                        type="text"  {{-- 1. Ubah tipe menjadi text --}}
                                        inputmode="numeric" {{-- Menampilkan keyboard numerik di perangkat mobile --}}
                                        name="time_limit"
                                        id="quiz_time_limit"
                                        x-model.number="content.quiz.time_limit"
                                        {{-- 2. Tambahkan event listener untuk hanya mengizinkan angka --}}
                                        @input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                        class="w-full max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                        placeholder="Contoh: 60">
                                    <p class="text-sm text-gray-500 mt-1">Biarkan kosong atau isi 0 jika tidak ada batas waktu.</p>
                                </div>

                                @include('quizzes.partials.full-quiz-form')
                            </div>
                        </div>
                    </div>

                    <div x-show="isType('zoom')" x-cloak class="animate-fadeIn">
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100 space-y-4">
                            <!-- Existing Zoom Fields -->
                            <div>
                                <label for="zoom_link" class="block text-sm font-semibold text-gray-700 mb-2">🔗 Link Rapat Zoom</label>
                                <input type="url" name="zoom_link" id="zoom_link" x-model="content.zoom_link"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                                    :class="{ 'form-input-error': errors.zoom_link }"
                                    placeholder="https://zoom.us/j/...">
                                <p x-show="errors.zoom_link" x-text="errors.zoom_link" class="text-sm text-red-600 mt-1"></p>
                            </div>
                            
                            <div>
                                <label for="zoom_meeting_id" class="block text-sm font-semibold text-gray-700 mb-2">🆔 Meeting ID</label>
                                <input type="text" name="zoom_meeting_id" id="zoom_meeting_id" x-model="content.zoom_meeting_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                                    :class="{ 'form-input-error': errors.zoom_meeting_id }">
                                <p x-show="errors.zoom_meeting_id" x-text="errors.zoom_meeting_id" class="text-sm text-red-600 mt-1"></p>
                            </div>
                            
                            <div>
                                <label for="zoom_password" class="block text-sm font-semibold text-gray-700 mb-2">🔑 Password (Opsional)</label>
                                <input type="text" name="zoom_password" id="zoom_password" x-model="content.zoom_password"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                            </div>

                            <!-- ✅ NEW: Scheduling Section -->
                            <div class="border-t border-blue-200 pt-4">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" 
                                        id="is_scheduled" 
                                        name="is_scheduled" 
                                        x-model="content.is_scheduled"
                                        value="1"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_scheduled" class="ml-3 block text-sm font-semibold text-gray-700">
                                        📅 Jadwalkan Meeting (Akses terbatas pada waktu tertentu)
                                    </label>
                                </div>
                                
                                <div x-show="content.is_scheduled" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    class="space-y-4">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="scheduled_start" class="block text-sm font-semibold text-gray-700 mb-2">
                                                🕒 Waktu Mulai
                                            </label>
                                            <input type="datetime-local" 
                                                name="scheduled_start" 
                                                id="scheduled_start"
                                                x-model="content.scheduled_start"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                                                :class="{ 'border-red-300 focus:border-red-500': errors.scheduled_start }">
                                            <p x-show="errors.scheduled_start" x-text="errors.scheduled_start" class="text-sm text-red-600 mt-1"></p>
                                        </div>
                                        
                                        <div>
                                            <label for="scheduled_end" class="block text-sm font-semibold text-gray-700 mb-2">
                                                🕕 Waktu Selesai
                                            </label>
                                            <input type="datetime-local" 
                                                name="scheduled_end" 
                                                id="scheduled_end"
                                                x-model="content.scheduled_end"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                                                :class="{ 'border-red-300 focus:border-red-500': errors.scheduled_end }">
                                            <p x-show="errors.scheduled_end" x-text="errors.scheduled_end" class="text-sm text-red-600 mt-1"></p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="timezone" class="block text-sm font-semibold text-gray-700 mb-2">
                                            🌍 Zona Waktu
                                        </label>
                                        <select name="timezone" 
                                                id="timezone"
                                                x-model="content.timezone"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                                            <option value="Asia/Jakarta">WIB (Jakarta)</option>
                                            <option value="Asia/Kuala_Lumpur">MYT (Kuala Lumpur)</option>
                                            <option value="Asia/Singapore">SGT (Singapore)</option>
                                            <option value="UTC">UTC</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Info Box -->
                                    <div class="bg-blue-100 border border-blue-300 rounded-xl p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-semibold text-blue-800">Catatan Penjadwalan</h3>
                                                <div class="mt-2 text-sm text-blue-700">
                                                    <ul class="list-disc list-inside space-y-1">
                                                        <li>Peserta hanya bisa join meeting pada waktu yang dijadwalkan</li>
                                                        <li>Pastikan waktu sesuai dengan rencana meeting Anda</li>
                                                        <li>Zona waktu default: WIB (Jakarta)</li>
                                                        <li>Meeting akan otomatis tidak bisa diakses di luar jadwal</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('courses.show', $lesson->course) }}"
                               class="inline-flex items-center px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </a>
                        </div>

                        <button type="button"
                                @click="submitForm()"
                                {{-- ✅ TAMBAHKAN :class untuk animasi goyang --}}
                                :class="{ 'shake': formHasErrors }"
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
                errors: {},
                formHasErrors: false,
                uploadedFileName: '',
                uploadedImagePreviewUrl: null,

                initForm() {
                    this.$watch('content.type', (newType) => this.handleTypeChange(newType));

                    if (this.content.type === 'zoom' && this.content.body) {
                    try {
                        const zoomDetails = JSON.parse(this.content.body);
                        this.content.zoom_link = zoomDetails.link || '';
                        this.content.zoom_meeting_id = zoomDetails.meeting_id || '';
                        this.content.zoom_password = zoomDetails.password || '';
                        
                        // ✅ NEW: Parse scheduling data dari JSON body
                        this.content.is_scheduled = zoomDetails.is_scheduled || false;
                        this.content.timezone = zoomDetails.timezone || 'Asia/Jakarta';
                    } catch (e) {
                        console.log('Error parsing zoom details:', e);
                    }
                    } else {
                        this.content.zoom_link = '';
                        this.content.zoom_meeting_id = '';
                        this.content.zoom_password = '';
                        this.content.is_scheduled = false;
                        this.content.timezone = 'Asia/Jakarta';
                    }

                    if (!this.content.hasOwnProperty('is_scheduled')) {
                        this.content.is_scheduled = @json($content->is_scheduled ?? false);
                    }
                    if (!this.content.hasOwnProperty('scheduled_start')) {
                        this.content.scheduled_start = @json($content->scheduled_start ? $content->scheduled_start->format('Y-m-d\TH:i') : '');
                    }
                    if (!this.content.hasOwnProperty('scheduled_end')) {
                        this.content.scheduled_end = @json($content->scheduled_end ? $content->scheduled_end->format('Y-m-d\TH:i') : '');
                    }
                    if (!this.content.hasOwnProperty('timezone')) {
                        this.content.timezone = 'Asia/Jakarta';
                    }

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
                        'essay': 'Esai',
                        'zoom': 'Zoom Meeting'
                    };
                    return labels[type] || type;
                },

                handleTypeChange(type) {
                    // Cukup pastikan editor Summernote dihancurkan jika tidak diperlukan
                    if (type !== 'text' && type !== 'essay') {
                         if ($('#body_editor').hasClass('note-editor')) {
                            $('#body_editor').summernote('destroy');
                        }
                    }
                    
                    if (type !== 'zoom') {
                        this.content.is_scheduled = false;
                        this.content.scheduled_start = '';
                        this.content.scheduled_end = '';
                        this.content.timezone = 'Asia/Jakarta';
                    }
                },

                submitForm() {
                    if (this.validate()) {
                        document.getElementById('contentForm').submit();
                    } else {
                        this.formHasErrors = true;
                        setTimeout(() => { this.formHasErrors = false; }, 820);
                    }
                },

                validate() {
                    this.errors = {};
                    // Cek judul di Informasi Dasar
                    if (!this.content.title || !this.content.title.trim()) {
                        this.errors.title = 'Judul konten tidak boleh kosong.';
                    }
                    // Cek field lain berdasarkan tipe
                    if (this.isType('video') && (!this.content.body || !this.content.body.trim())) {
                        this.errors.body_video = 'URL Video tidak boleh kosong.';
                    }
                    if (this.isType('zoom')) {
                        if (!this.content.zoom_link || !this.content.zoom_link.trim()) {
                            this.errors.zoom_link = 'Link rapat tidak boleh kosong.';
                        }
                        if (!this.content.zoom_meeting_id || !this.content.zoom_meeting_id.trim()) {
                            this.errors.zoom_meeting_id = 'Meeting ID tidak boleh kosong.';
                        }
                        
                        // ✅ NEW: Scheduling validation
                        if (this.content.is_scheduled) {
                            if (!this.content.scheduled_start) {
                                this.errors.scheduled_start = 'Waktu mulai harus diisi';
                            }
                            if (!this.content.scheduled_end) {
                                this.errors.scheduled_end = 'Waktu selesai harus diisi';
                            }
                            
                            if (this.content.scheduled_start && this.content.scheduled_end) {
                                const start = new Date(this.content.scheduled_start);
                                const end = new Date(this.content.scheduled_end);
                                
                                if (start >= end) {
                                    this.errors.scheduled_end = 'Waktu selesai harus setelah waktu mulai';
                                }
                                
                                if (start <= new Date()) {
                                    this.errors.scheduled_start = 'Waktu mulai harus di masa depan';
                                }
                            }
                        }
                    }
                    return Object.keys(this.errors).length === 0;
                },

                // ✅ NEW: Helper method untuk debug scheduling
                debugScheduling() {
                    console.log('Zoom Scheduling Debug:', {
                        is_scheduled: this.content.is_scheduled,
                        scheduled_start: this.content.scheduled_start,
                        scheduled_end: this.content.scheduled_end,
                        timezone: this.content.timezone,
                        zoom_link: this.content.zoom_link,
                        zoom_meeting_id: this.content.zoom_meeting_id
                    });
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        this.uploadedFileName = '';
                        this.uploadedImagePreviewUrl = null;
                        return;
                    }

                    this.uploadedFileName = file.name;

                    if (this.isType('image')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.uploadedImagePreviewUrl = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.uploadedImagePreviewUrl = null;
                    }
                },

                // Quiz management methods
                defaultQuizObject() {
                    return {
                        title: this.content.title || '',
                        description: '',
                        duration: 0, // Ditambahkan: default duration
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

        // ✅ TESTING: Add console debug untuk development
        document.addEventListener('DOMContentLoaded', function() {
            // Debug helper - remove in production
            if (typeof window.debugZoomScheduling === 'undefined') {
                window.debugZoomScheduling = function() {
                    const alpine = document.querySelector('[x-data]').__x;
                    if (alpine && alpine.debugScheduling) {
                        alpine.debugScheduling();
                    } else {
                        console.log('Alpine component not found or debug method not available');
                    }
                };
                console.log('Debug helper loaded. Call debugZoomScheduling() to inspect zoom scheduling data.');
            }
        });

        function essayQuestionsManager() {
    return {
        questions: [
            { text: '', max_score: 100 }
        ],
        
        get totalScore() {
            return this.questions.reduce((total, q) => total + parseInt(q.max_score || 0), 0);
        },
        
        addQuestion() {
            this.questions.push({ text: '', max_score: 100 });
            console.log('Question added, total:', this.questions.length);
        },
        
        removeQuestion(index) {
            if (this.questions.length > 1) {
                this.questions.splice(index, 1);
                console.log('Question removed, total:', this.questions.length);
            }
        },

        init() {
            console.log('Essay Questions Manager initialized');
            
            // Jika edit content lama tanpa questions, load dari body
            @if($content->exists && $content->body && (!$content->essayQuestions || $content->essayQuestions->count() === 0))
                this.questions = [{ text: @json(strip_tags($content->body)), max_score: 100 }];
                console.log('Loaded legacy question from body');
            @else
                this.questions = [{ text: '', max_score: 100 }];
                console.log('Started with empty question');
            @endif
        }
    }
}
    </script>
    @endpush
</x-app-layout>