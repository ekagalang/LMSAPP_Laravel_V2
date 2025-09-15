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
                     content: @js(array_merge($content->toArray(), [
                         'scoring_enabled' => old('scoring_enabled', $content->scoring_enabled ?? true)
                     ])),
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
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
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
                                        <div class="text-xs text-gray-500 mt-1">Upload file atau URL</div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="document" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'document' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">📄</div>
                                        <div class="text-xs font-medium">Dokumen</div>
                                        <div class="text-xs text-gray-500 mt-1">PDF, DOC, XLS, PPT</div>
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

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="audio" x-model="content.type" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                         :class="content.type === 'audio' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                        <div class="text-2xl mb-2">🎵</div>
                                        <div class="text-xs font-medium">Audio</div>
                                    </div>
                                </label>
                            </div>

                            <div x-show="content.type === 'essay'" x-transition class="mb-6">
                                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-indigo-900 mb-1">Model Penilaian Essay</h4>
                                            <p class="text-xs text-indigo-700">Pilih cara memberikan nilai dan feedback</p>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <label class="flex items-start cursor-pointer">
                                                <input type="radio" 
                                                    name="grading_mode" 
                                                    value="individual" 
                                                    {{ old('grading_mode', $content->grading_mode ?? 'individual') === 'individual' ? 'checked' : '' }}
                                                    x-model="content.grading_mode"
                                                    class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 mt-0.5">
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">Per Soal Individual</span>
                                                    <p class="text-xs text-gray-600">Berikan nilai dan feedback untuk setiap pertanyaan secara terpisah</p>
                                                </div>
                                            </label>

                                            <label class="flex items-start cursor-pointer">
                                                <input type="radio" 
                                                    name="grading_mode" 
                                                    value="overall" 
                                                    {{ old('grading_mode', $content->grading_mode ?? 'individual') === 'overall' ? 'checked' : '' }}
                                                    x-model="content.grading_mode"
                                                    class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 mt-0.5">
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">Keseluruhan Essay</span>
                                                    <p class="text-xs text-gray-600">Berikan satu nilai dan feedback untuk seluruh essay</p>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                            <div class="flex">
                                                <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                <div class="text-xs text-blue-800">
                                                    <p><strong>Per Soal:</strong> Instructor menilai setiap pertanyaan dengan skor terpisah. Total skor = jumlah skor semua soal.</p>
                                                    <p class="mt-1"><strong>Keseluruhan:</strong> Instructor memberikan satu skor untuk seluruh essay. Lebih simple dan cepat.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                        <div>
                                            <h4 class="text-sm font-medium text-blue-900 mb-1">Mode Essay</h4>
                                            <p class="text-xs text-blue-700">Pilih mode essay berdasarkan kebutuhan pembelajaran</p>
                                        </div>
                                        
                                        @php
                                            // Tentukan review mode berdasarkan existing data
                                            $currentReviewMode = 'scoring'; // default
                                            if (isset($content)) {
                                                if (!($content->requires_review ?? true)) {
                                                    $currentReviewMode = 'no_review';
                                                } elseif (!($content->scoring_enabled ?? true)) {
                                                    $currentReviewMode = 'feedback_only';
                                                } else {
                                                    $currentReviewMode = 'scoring';
                                                }
                                            }
                                        @endphp

                                        <div class="space-y-2">
                                            <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors cursor-pointer">
                                                <input type="radio" 
                                                    name="review_mode" 
                                                    value="scoring" 
                                                    {{ old('review_mode', $currentReviewMode) == 'scoring' ? 'checked' : '' }}
                                                    class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 mt-0.5">
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">🏆 Dengan Penilaian</span>
                                                    <p class="text-xs text-gray-500 mt-1">Instruktur memberikan nilai dan feedback. Peserta menunggu hasil penilaian.</p>
                                                </div>
                                            </label>
                                            
                                            <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors cursor-pointer">
                                                <input type="radio" 
                                                    name="review_mode" 
                                                    value="feedback_only" 
                                                    {{ old('review_mode', $currentReviewMode) == 'feedback_only' ? 'checked' : '' }}
                                                    class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 mt-0.5">
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">📝 Tanpa Penilaian</span>
                                                    <p class="text-xs text-gray-500 mt-1">Instruktur bisa memberikan feedback tanpa nilai. Peserta menunggu tinjauan.</p>
                                                </div>
                                            </label>
                                            
                                            <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors cursor-pointer">
                                                <input type="radio" 
                                                    name="review_mode" 
                                                    value="no_review" 
                                                    {{ old('review_mode', $currentReviewMode) == 'no_review' ? 'checked' : '' }}
                                                    class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 mt-0.5">
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">✅ Latihan Mandiri</span>
                                                    <p class="text-xs text-gray-500 mt-1">Essay untuk latihan. Langsung selesai setelah dikumpulkan, tanpa review instruktur.</p>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div class="p-3 bg-blue-50 rounded-lg">
                                            <div class="flex items-start">
                                                <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div class="text-xs text-blue-800">
                                                    <p class="font-medium mb-1">Tips pemilihan mode:</p>
                                                    <ul class="space-y-1">
                                                        <li>• <strong>Dengan Penilaian:</strong> Untuk tugas formal, ujian essay, atau karya yang perlu dinilai</li>
                                                        <li>• <strong>Tanpa Penilaian:</strong> Untuk tugas diskusi atau karya yang perlu feedback tanpa nilai</li>
                                                        <li>• <strong>Latihan Mandiri:</strong> Untuk latihan pribadi, refleksi, atau tugas yang tidak perlu direview</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                    {{-- 🆕 Tampilkan score hanya jika scoring enabled --}}
                                                    @if($content->scoring_enabled)
                                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                            {{ $question->max_score }} poin
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                                            Tanpa Penilaian
                                                        </span>
                                                    @endif
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
                                                    
                                                    {{-- 🆕 Max score input - hanya tampil jika scoring enabled --}}
                                                    <div x-show="$root.content.scoring_enabled" class="w-40">
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
                                                            :required="$root.content.scoring_enabled"
                                                        />
                                                    </div>
                                                    
                                                    {{-- 🆕 Hidden input untuk max_score jika scoring disabled --}}
                                                    <template x-if="!$root.content.scoring_enabled">
                                                        <input type="hidden" :name="'questions[' + index + '][max_score]'" value="0">
                                                    </template>
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
                                                {{-- 🆕 Total score hanya tampil jika scoring enabled --}}
                                                <p x-show="$root.content.scoring_enabled" class="text-sm text-gray-600">
                                                    Total Skor Baru: <span x-text="totalScore" class="font-semibold text-green-600"></span> poin
                                                </p>
                                                <p x-show="!$root.content.scoring_enabled" class="text-sm text-blue-600">
                                                    Mode: <span class="font-semibold">Tanpa Penilaian</span>
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
                                <div class="mb-6">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="video_source" value="url" class="mr-2" x-model="videoSource" @change="clearVideoInputs()" :checked="videoSource === 'url'">
                                            <span class="font-medium">🔗 URL Video</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="video_source" value="file" class="mr-2" x-model="videoSource" @change="clearVideoInputs()" :checked="videoSource === 'file'">
                                            <span class="font-medium">📁 Upload File</span>
                                        </label>
                                    </div>
                                </div>

                                <div x-show="videoSource === 'url'">
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

                                <div x-show="videoSource === 'file'">
                                    <label for="video_file" class="block text-sm font-semibold text-gray-700 mb-3">
                                        🎬 Upload File Video
                                    </label>

                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-400 transition-colors duration-300"
                                         @dragover="handleDragOver($event)"
                                         @dragenter="handleDragEnter($event, 'video')"
                                         @dragleave="handleDragLeave($event, 'video')"
                                         @drop="handleDrop($event, 'video_file_edit')">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <input type="file"
                                               name="video_file"
                                               id="video_file_edit"
                                               class="hidden"
                                               accept=".mp4,.mov,.avi,.mkv,.webm,.flv,.wmv,.3gp"
                                               @change="handleVideoFileSelect($event)">
                                        <label for="video_file_edit" class="cursor-pointer">
                                            <span class="text-red-600 font-medium hover:text-red-500">Klik untuk memilih video</span>
                                            <span class="text-gray-500"> atau drag & drop file di sini</span>
                                        </label>
                                        <p class="text-sm text-gray-500 mt-2">Format: MP4, MOV, AVI, MKV, WebM, FLV, WMV, 3GP</p>
                                        <p class="text-xs text-gray-400 mt-1">Maksimal 500MB</p>
                                    </div>

                                    @if($content->file_path && $content->type === 'video')
                                        <div class="mt-4 p-4 bg-white rounded-lg border">
                                            <h4 class="font-medium text-gray-900 mb-2">File Video Saat Ini:</h4>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                    🎬
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ basename($content->file_path) }}</p>
                                                    @if($content->audio_metadata && isset($content->audio_metadata['file_size']))
                                                        <p class="text-xs text-gray-500">{{ number_format($content->audio_metadata['file_size'] / 1024 / 1024, 2) }} MB</p>
                                                    @endif
                                                </div>
                                                <a href="{{ Storage::url($content->file_path) }}" target="_blank" class="text-red-600 hover:text-red-800 text-sm">
                                                    Lihat File
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div x-show="videoUploadProgress" class="mt-4">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-500 h-2 rounded-full transition-all duration-300" :style="`width: ${videoUploadProgress}%`"></div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2" x-text="videoUploadText"></p>
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

                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-400 transition-colors duration-300"
                                     @dragover="handleDragOver($event)"
                                     @dragenter="handleDragEnter($event, 'document')"
                                     @dragleave="handleDragLeave($event, 'document')"
                                     @drop="handleDrop($event, 'file_upload')">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <input type="file" name="file_upload" id="file_upload" class="hidden"
                                        @change="handleFileUpload($event)"
                                        :accept="isType('image') ? 'image/*' : (isType('document') ? '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt' : '')">
                                    <label for="file_upload" class="cursor-pointer">
                                        <span class="text-green-600 font-medium hover:text-green-500">
                                            <span x-text="content.file_path ? 'Ganti file' : 'Pilih file'"></span>
                                        </span>
                                        <span class="text-gray-500"> atau drag & drop file di sini</span>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <span x-show="isType('document')">PDF (preview inline), DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT - Max 100MB</span>
                                        <span x-show="isType('image')">JPG, PNG, GIF - Max 100MB</span>
                                    </p>
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

                    <div x-show="isType('audio')" x-cloak class="animate-fadeIn">
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

                            <!-- Audio Type Selection -->
                            <div class="mb-6">
                                <h4 class="text-md font-semibold text-gray-800 mb-4">🎯 Tipe Audio Learning</h4>

                                <div class="space-y-3">
                                    <label class="flex items-start p-4 border-2 rounded-xl transition-colors cursor-pointer"
                                           :class="audioType === 'simple' ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-300'">
                                        <input type="radio" name="audio_type" value="simple" class="mt-1 mr-3 text-teal-600"
                                               x-model="audioType" @change="updateAudioType()">
                                        <div>
                                            <div class="font-medium text-gray-800">📚 Audio Content Sederhana</div>
                                            <div class="text-sm text-gray-600 mt-1">Audio dengan quiz sederhana sebagai bagian course</div>
                                        </div>
                                    </label>

                                    <label class="flex items-start p-4 border-2 rounded-xl transition-colors cursor-pointer"
                                           :class="audioType === 'existing_lesson' ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-300'">
                                        <input type="radio" name="audio_type" value="existing_lesson" class="mt-1 mr-3 text-teal-600"
                                               x-model="audioType" @change="updateAudioType()">
                                        <div>
                                            <div class="font-medium text-gray-800">🔗 Link ke Audio Learning</div>
                                            <div class="text-sm text-gray-600 mt-1">Hubungkan dengan audio learning yang sudah ada</div>
                                        </div>
                                    </label>

                                    <label class="flex items-start p-4 border-2 rounded-xl transition-colors cursor-pointer"
                                           :class="audioType === 'new_lesson' ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-300'">
                                        <input type="radio" name="audio_type" value="new_lesson" class="mt-1 mr-3 text-teal-600"
                                               x-model="audioType" @change="updateAudioType()">
                                        <div>
                                            <div class="font-medium text-gray-800">✨ Buat Audio Learning Baru</div>
                                            <div class="text-sm text-gray-600 mt-1">Buat audio learning lengkap yang juga muncul di halaman Audio Learning</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Existing Audio Lesson Selection -->
                            <div x-show="audioType === 'existing_lesson'" class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    🎵 Pilih Audio Learning
                                </label>
                                <select name="audio_lesson_id" x-model="content.audio_lesson_id"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all duration-300">
                                    <option value="">Pilih audio learning yang sudah ada...</option>
                                    @foreach(App\Models\AudioLesson::active()->availableForCourses()->get() as $audioLesson)
                                        <option value="{{ $audioLesson->id }}" {{ old('audio_lesson_id', $content->audio_lesson_id) == $audioLesson->id ? 'selected' : '' }}>
                                            {{ $audioLesson->title }} ({{ ucfirst($audioLesson->difficulty_level) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- New Audio Learning Creation -->
                            <div x-show="audioType === 'new_lesson'" class="mb-6">
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
                                               :value="content.title">
                                    </div>

                                    <!-- Audio Learning Description -->
                                    <div class="mb-4">
                                        <label for="new_lesson_description" class="block text-sm font-medium text-gray-700 mb-2">
                                            📄 Deskripsi Audio Learning
                                        </label>
                                        <textarea name="new_lesson_description" id="new_lesson_description" rows="3"
                                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300"
                                                  placeholder="Deskripsi singkat tentang apa yang akan dipelajari..."
                                                  x-text="content.description"></textarea>
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
                                            <option value="general" selected>🌟 General</option>
                                        </select>
                                    </div>

                                    <div class="bg-purple-100 rounded-lg p-3 border border-purple-200">
                                        <p class="text-sm text-purple-800">
                                            💡 <strong>Tips:</strong> Audio learning yang dibuat akan memiliki sistem exercise yang lebih lengkap dan muncul di halaman Audio Learning untuk bisa digunakan di course lain.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Audio File Display -->
                            <div x-show="(audioType === 'simple' || audioType === 'new_lesson') && content.audio_file_path && !uploadedAudioFileName" class="mb-6 p-4 bg-white rounded-lg border border-teal-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">File audio saat ini:</p>
                                            <a :href="`/storage/${content.audio_file_path}`"
                                               target="_blank"
                                               class="text-teal-600 hover:text-teal-800 text-sm underline"
                                               x-text="content.audio_file_path ? content.audio_file_path.split('/').pop() : ''"></a>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-teal-100 text-teal-700 rounded-full text-xs">Aktif</span>
                                </div>
                                <div class="mt-3">
                                    <audio controls class="w-full">
                                        <source :src="`/storage/${content.audio_file_path}`" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            </div>

                            <!-- New Audio File Upload -->
                            <div x-show="audioType === 'simple' || audioType === 'new_lesson'" class="mb-6">
                                <label for="audio_file" class="block text-sm font-semibold text-gray-700 mb-3">
                                    🎧 File Audio
                                </label>
                                <div class="border-2 border-dashed border-teal-300 rounded-xl p-8 text-center hover:border-teal-400 transition-colors duration-300"
                                     @dragover="handleDragOver($event)"
                                     @dragenter="handleDragEnter($event, 'audio')"
                                     @dragleave="handleDragLeave($event, 'audio')"
                                     @drop="handleDrop($event, 'audio_file')">
                                    <div class="mb-4">
                                        <svg class="mx-auto h-12 w-12 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <label for="audio_file" class="relative cursor-pointer bg-white rounded-md font-medium text-teal-600 hover:text-teal-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500">
                                            <span x-text="content.audio_file_path ? 'Ganti file audio' : 'Upload audio file'"></span>
                                            <input id="audio_file" name="audio_file" type="file" class="sr-only"
                                                   accept="audio/*" @change="handleAudioUpload($event)">
                                        </label>
                                        <p class="pl-1">atau drag and drop file di sini</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">MP3, WAV, atau M4A up to 50MB</p>
                                </div>

                                <div x-show="uploadedAudioFileName" class="mt-4 p-4 bg-teal-50 rounded-lg border border-teal-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900" x-text="uploadedAudioFileName"></p>
                                                <p class="text-sm text-gray-500">File baru dipilih</p>
                                            </div>
                                        </div>
                                        <audio x-show="uploadedAudioPreviewUrl" id="audio_player_new" controls class="ml-4">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                            </div>

                            <!-- Audio Transcript -->
                            <div x-show="audioType === 'simple' || audioType === 'new_lesson'" class="mb-6">
                                <label for="audio_transcript" class="block text-sm font-semibold text-gray-700 mb-3">
                                    📝 Transkrip Audio (Opsional)
                                </label>
                                <textarea name="audio_transcript" id="audio_transcript" rows="4"
                                          x-model="content.audio_transcript"
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all duration-300 placeholder-gray-400"
                                          placeholder="Masukkan transkrip audio untuk membantu pembelajaran..."></textarea>
                                <p class="text-sm text-gray-500 mt-2">Transkrip akan membantu learner memahami audio dengan lebih baik</p>
                            </div>

                            <!-- Difficulty Level -->
                            <div x-show="audioType === 'simple' || audioType === 'new_lesson'" class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    📊 Tingkat Kesulitan
                                </label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="audio_difficulty" value="beginner"
                                               x-model="content.audio_difficulty"
                                               class="form-radio h-4 w-4 text-teal-600">
                                        <span class="ml-2 text-sm text-gray-700">🟢 Beginner</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="audio_difficulty" value="intermediate"
                                               x-model="content.audio_difficulty"
                                               class="form-radio h-4 w-4 text-teal-600">
                                        <span class="ml-2 text-sm text-gray-700">🟡 Intermediate</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="audio_difficulty" value="advanced"
                                               x-model="content.audio_difficulty"
                                               class="form-radio h-4 w-4 text-teal-600">
                                        <span class="ml-2 text-sm text-gray-700">🔴 Advanced</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Quiz Integration -->
                            <div class="mb-6">
                                <div class="flex items-center mb-3">
                                    <input type="checkbox" id="audio_has_quiz" name="audio_has_quiz" value="1"
                                           x-model="content.audio_has_quiz"
                                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="audio_has_quiz" class="ml-2 text-sm font-medium text-gray-700">
                                        🧠 Tambahkan Quiz Interaktif
                                    </label>
                                </div>
                                <p class="text-sm text-gray-500">Aktifkan untuk membuat soal interaktif berdasarkan audio</p>
                            </div>

                            <!-- Quiz Settings -->
                            <div x-show="content.audio_has_quiz" class="mb-6">
                                <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-4 border border-orange-200">
                                    <h4 class="font-medium text-gray-900 mb-4">⚙️ Pengaturan Quiz Audio</h4>

                                    <!-- Time Limit -->
                                    <div class="mb-4">
                                        <label for="audio_time_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                            ⏱️ Durasi Pengerjaan (Menit)
                                        </label>
                                        <input type="text"
                                               inputmode="numeric"
                                               name="time_limit"
                                               id="audio_time_limit"
                                               x-model="content.quiz.time_limit"
                                               @input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                               class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all duration-200"
                                               placeholder="60">
                                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk tanpa batas waktu</p>
                                    </div>

                                    <!-- Quiz Details and Questions Editor -->
                                    <div x-show="content.quiz && content.quiz.questions && content.quiz.questions.length > 0" class="mt-6">
                                        <div class="bg-white rounded-lg border border-teal-200 p-4">
                                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                                                🎧 Quiz Audio Interactive
                                                <span class="ml-2 px-2 py-1 bg-teal-100 text-teal-700 rounded-full text-sm">
                                                    <span x-text="content.quiz.questions ? content.quiz.questions.length : 0"></span> Soal
                                                </span>
                                            </h4>

                                            @include('quizzes.partials.full-quiz-form')
                                        </div>
                                    </div>

                                    <!-- Initial Setup for New Audio Quiz -->
                                    <div x-show="!content.quiz || !content.quiz.questions || content.quiz.questions.length === 0" class="mt-4">
                                        <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-teal-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm text-teal-800 font-medium mb-2">Setup Awal Quiz Audio</p>
                                                    <div class="text-sm text-teal-700 space-y-1">
                                                        <p>• Centang tipe soal yang ingin dibuat</p>
                                                        <p>• Setelah save, soal template akan otomatis dibuat</p>
                                                        <p>• Anda bisa mengedit detail soal di form di bawah</p>
                                                    </div>

                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium text-teal-700 mb-2">
                                                            📝 Pilih Tipe Soal untuk Template
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
                uploadedAudioFileName: '',
                uploadedAudioPreviewUrl: null,
                videoSource: 'url',
                videoUploadProgress: 0,
                videoUploadText: '',
                audioType: '{{ old('audio_type', $content->is_audio_learning ? ($content->audio_lesson_id ? 'existing_lesson' : 'new_lesson') : 'simple') }}',

                initForm() {
                    if (!this.content.hasOwnProperty('grading_mode')) {
                        this.content.grading_mode = '{{ old('grading_mode', $content->grading_mode ?? 'individual') }}';
                    }
                    
                    if (!this.content.hasOwnProperty('scoring_enabled')) {
                        this.content.scoring_enabled = {{ old('scoring_enabled', $content->scoring_enabled ?? true) ? 'true' : 'false' }};
                    }

                    this.$watch('content.type', (newType) => this.handleTypeChange(newType));

                    // 🆕 TAMBAHAN: Watch scoring_enabled changes
                    this.$watch('content.grading_mode', (value) => {
                        console.log('Grading mode changed to:', value);
                    });
                    
                    this.$watch('content.scoring_enabled', (value) => {
                        console.log('Scoring enabled changed to:', value);
                    });
                    
                    if (this.content.type === 'zoom' && this.content.body) {
                    try {
                        const zoomDetails = JSON.parse(this.content.body);
                        this.content.zoom_link = zoomDetails.link || '';
                        this.content.zoom_meeting_id = zoomDetails.meeting_id || '';
                        this.content.zoom_password = zoomDetails.password || '';
                        
                        // Parse scheduling data dari JSON body
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

                    // Initialize audio fields
                    if (!this.content.hasOwnProperty('audio_file_path')) {
                        this.content.audio_file_path = @json($content->audio_file_path ?? '');
                    }
                    if (!this.content.hasOwnProperty('audio_transcript')) {
                        this.content.audio_transcript = @json($content->audio_transcript ?? '');
                    }
                    if (!this.content.hasOwnProperty('audio_difficulty')) {
                        this.content.audio_difficulty = @json($content->audio_difficulty ?? 'beginner');
                    }
                    if (!this.content.hasOwnProperty('audio_has_quiz')) {
                        this.content.audio_has_quiz = @json(!!($content->audio_has_quiz ?? false));
                    }
                    if (!this.content.hasOwnProperty('audio_time_limit')) {
                        this.content.audio_time_limit = @json($content->audio_time_limit ?? '');
                    }

                    // Fix quiz data conversion
                    if (this.content.quiz) {
                        this.content.quiz.show_answers_after_attempt = !!parseInt(this.content.quiz.show_answers_after_attempt);
                        if (this.content.quiz.questions) {
                            this.content.quiz.questions.forEach(q => {
                                // Ensure new fields are initialized
                                if (!q.hasOwnProperty('correct_answer')) q.correct_answer = '';
                                if (!q.hasOwnProperty('alternative_answers')) q.alternative_answers = '';
                                if (!q.hasOwnProperty('comprehension_type')) q.comprehension_type = 'text';
                                if (!q.hasOwnProperty('expected_answer')) q.expected_answer = '';

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
                        'zoom': 'Zoom Meeting',
                        'audio': 'Audio Learning'
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

                    // 🆕 TAMBAHAN: Reset scoring_enabled to true when changing to essay
                    if (type === 'essay' && !this.content.hasOwnProperty('scoring_enabled')) {
                        this.content.scoring_enabled = true;
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
                        
                        // Scheduling validation
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

                // Helper method untuk debug scheduling
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

                handleAudioUpload(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        this.uploadedAudioFileName = '';
                        this.uploadedAudioPreviewUrl = null;
                        return;
                    }

                    // Validate file type
                    if (!file.type.startsWith('audio/')) {
                        alert('Please select a valid audio file.');
                        event.target.value = '';
                        return;
                    }

                    // Validate file size (50MB max)
                    if (file.size > 50 * 1024 * 1024) {
                        alert('File size should not exceed 50MB.');
                        event.target.value = '';
                        return;
                    }

                    this.uploadedAudioFileName = file.name;

                    // Create URL for audio preview
                    const url = URL.createObjectURL(file);
                    this.uploadedAudioPreviewUrl = url;

                    // Set source for audio player
                    const audioPlayer = document.getElementById('audio_player_new');
                    if (audioPlayer) {
                        audioPlayer.src = url;
                    }
                },

                // Video handling methods
                clearVideoInputs() {
                    const videoUrlInput = document.querySelector('input[name="body_video"]');
                    const videoFileInput = document.querySelector('input[name="video_file"]');

                    if (this.videoSource === 'file') {
                        // Clear URL when switching to file
                        if (videoUrlInput) {
                            videoUrlInput.value = '';
                            this.content.body = '';
                        }
                    } else {
                        // Clear file when switching to URL
                        if (videoFileInput) {
                            videoFileInput.value = '';
                        }
                        this.videoUploadProgress = 0;
                        this.videoUploadText = '';
                    }
                },

                handleVideoFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        this.videoUploadProgress = 0;
                        this.videoUploadText = '';
                        return;
                    }

                    // Validate file type
                    const validTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm', 'video/x-flv', 'video/x-ms-wmv', 'video/3gpp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid video file (MP4, MOV, AVI, MKV, WebM, FLV, WMV, 3GP).');
                        event.target.value = '';
                        return;
                    }

                    // Validate file size (500MB max)
                    if (file.size > 500 * 1024 * 1024) {
                        alert('File size should not exceed 500MB.');
                        event.target.value = '';
                        return;
                    }

                    // Clear URL when file is selected
                    this.content.body = '';

                    // Show upload progress (simulated)
                    this.videoUploadProgress = 0;
                    this.videoUploadText = `Uploading ${file.name}...`;

                    // Simulate upload progress
                    const interval = setInterval(() => {
                        this.videoUploadProgress += Math.random() * 10;
                        if (this.videoUploadProgress >= 100) {
                            this.videoUploadProgress = 100;
                            clearInterval(interval);
                            this.videoUploadText = 'Upload completed!';
                            setTimeout(() => {
                                this.videoUploadProgress = 0;
                                this.videoUploadText = '';
                            }, 2000);
                        }
                    }, 200);
                },

                // Audio type management
                updateAudioType() {
                    // Logic to show/hide appropriate sections based on audio type
                    console.log('Audio type changed to:', this.audioType);
                },

                // Quiz management methods
                defaultQuizObject() {
                    return {
                        title: this.content.title || '',
                        description: '',
                        duration: 0,
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
                        correct_answer: '',
                        alternative_answers: '',
                        comprehension_type: 'text',
                        expected_answer: ''
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

        // Debug helper untuk development
        document.addEventListener('DOMContentLoaded', function() {
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

            // Trigger the Alpine.js event handlers based on input type
            const component = Alpine.$data(document.querySelector('[x-data*="contentFormManager"]'));
            if (component) {
                const mockEvent = { target: input };
                switch(inputId) {
                    case 'file_upload':
                        if (component.handleFileUpload) {
                            component.handleFileUpload(mockEvent);
                        }
                        break;
                    case 'video_file_edit':
                        if (component.handleVideoFileSelect) {
                            component.handleVideoFileSelect(mockEvent);
                        }
                        break;
                    case 'audio_file':
                        if (component.handleAudioUpload) {
                            component.handleAudioUpload(mockEvent);
                        }
                        break;
                }
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

        // 🆕 TAMBAHAN: Essay Questions Manager dengan scoring awareness
        function essayQuestionsManager() {
            return {
                questions: [
                    { text: '', max_score: 100 }
                ],
                
                get totalScore() {
                    // 🆕 Hanya hitung total jika scoring enabled
                    const parentComponent = this.$root;
                    if (parentComponent && parentComponent.content && !parentComponent.content.scoring_enabled) {
                        return 0;
                    }
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