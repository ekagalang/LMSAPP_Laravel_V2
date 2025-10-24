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

                            <div class="mt-6">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_optional" value="1"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @checked(old('is_optional', $content->is_optional ?? false))>
                                    <span class="ml-2 text-sm text-gray-700">Boleh dilewati (opsional)</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Jika dicentang, konten ini tidak akan mengunci konten berikutnya.</p>
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

                        {{-- ✅ FIX: Untuk TEXT type only - jangan render untuk essay type --}}
                        @if(!$content->exists || $content->type !== 'essay')
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
                        @endif

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

                                {{-- Existing Questions with Edit/Delete functionality --}}
                                @if($content->exists && $content->essayQuestions && $content->essayQuestions->count() > 0)
                                    <div class="space-y-4" id="existing-questions-list">
                                        <h4 class="font-semibold text-gray-900 flex items-center">
                                            <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2 text-xs">
                                                {{ $content->essayQuestions->count() }}
                                            </div>
                                            Pertanyaan yang Sudah Ada
                                        </h4>
                                        @foreach($content->essayQuestions as $index => $question)
                                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm" id="existing-question-{{ $question->id }}">
                                                {{-- View Mode --}}
                                                <div class="view-mode-existing">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div class="flex-1">
                                                            <span class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full mb-2">
                                                                Soal {{ $index + 1 }}
                                                            </span>
                                                            <div class="text-sm text-gray-700 leading-relaxed mt-2">
                                                                {!! nl2br(e($question->question)) !!}
                                                            </div>
                                                            {{-- 🆕 Tampilkan score hanya jika scoring enabled --}}
                                                            @if($content->scoring_enabled)
                                                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full mt-2">
                                                                    {{ $question->max_score }} poin
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2 ml-4">
                                                            <button type="button"
                                                                    class="edit-existing-question-btn text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50 transition-colors"
                                                                    data-question-id="{{ $question->id }}"
                                                                    title="Edit pertanyaan">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </button>
                                                            {{-- ✅ FIX: Use button instead of nested form --}}
                                                            <button type="button"
                                                                    class="delete-question-btn text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                                    data-question-id="{{ $question->id }}"
                                                                    data-delete-url="{{ route('essay.questions.destroy', $question) }}"
                                                                    title="Hapus pertanyaan">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Edit Mode (Initially Hidden) - ✅ FIX: No nested form --}}
                                                <div class="edit-mode-existing hidden" data-update-url="{{ route('essay.questions.update', $question) }}">
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                Edit Soal {{ $index + 1 }}:
                                                            </label>
                                                            <textarea
                                                                class="question-text-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                                rows="4"
                                                                required>{{ $question->question }}</textarea>
                                                        </div>

                                                        @if ($content->scoring_enabled)
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                    Skor Maksimal:
                                                                </label>
                                                                <input
                                                                    type="number"
                                                                    class="max-score-input block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                                    min="1"
                                                                    value="{{ $question->max_score }}"
                                                                    required>
                                                            </div>
                                                        @else
                                                            <input type="hidden" class="max-score-input" value="0">
                                                        @endif

                                                        <div class="flex items-center gap-2">
                                                            <button type="button"
                                                                    class="save-edit-existing-btn inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors"
                                                                    data-question-id="{{ $question->id }}">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Simpan Perubahan
                                                            </button>
                                                            <button type="button"
                                                                    class="cancel-edit-existing-btn inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-md transition-colors"
                                                                    data-question-id="{{ $question->id }}">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Batal
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                            <p class="text-sm text-blue-800">
                                                <strong>💡 Info:</strong> Klik tombol <strong>Edit</strong> untuk mengubah pertanyaan yang sudah ada,
                                                atau gunakan form di bawah untuk menambah pertanyaan baru.
                                            </p>
                                        </div>
                                    </div>

                                    {{-- ✅ FIX: Updated JavaScript with DELETE and UPDATE handlers --}}
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // ===== CSRF Token Helper =====
                                            function getCsrfToken() {
                                                return document.querySelector('meta[name="csrf-token"]')?.content ||
                                                       document.querySelector('input[name="_token"]')?.value || '';
                                            }

                                            // ===== EDIT TOGGLE =====
                                            document.querySelectorAll('.edit-existing-question-btn').forEach(btn => {
                                                btn.addEventListener('click', function() {
                                                    const questionId = this.dataset.questionId;
                                                    const container = document.getElementById(`existing-question-${questionId}`);
                                                    container.querySelector('.view-mode-existing').classList.add('hidden');
                                                    container.querySelector('.edit-mode-existing').classList.remove('hidden');
                                                });
                                            });

                                            // ===== CANCEL EDIT =====
                                            document.querySelectorAll('.cancel-edit-existing-btn').forEach(btn => {
                                                btn.addEventListener('click', function() {
                                                    const questionId = this.dataset.questionId;
                                                    const container = document.getElementById(`existing-question-${questionId}`);
                                                    container.querySelector('.view-mode-existing').classList.remove('hidden');
                                                    container.querySelector('.edit-mode-existing').classList.add('hidden');
                                                });
                                            });

                                            // ===== DELETE QUESTION =====
                                            document.querySelectorAll('.delete-question-btn').forEach(btn => {
                                                btn.addEventListener('click', function() {
                                                    if (!confirm('Hapus pertanyaan ini? Jawaban participant untuk pertanyaan ini juga akan terhapus.')) {
                                                        return;
                                                    }

                                                    const deleteUrl = this.dataset.deleteUrl;
                                                    const questionId = this.dataset.questionId;

                                                    // Create temporary form and submit
                                                    const form = document.createElement('form');
                                                    form.method = 'POST';
                                                    form.action = deleteUrl;
                                                    form.style.display = 'none';

                                                    // CSRF token
                                                    const csrfInput = document.createElement('input');
                                                    csrfInput.type = 'hidden';
                                                    csrfInput.name = '_token';
                                                    csrfInput.value = getCsrfToken();
                                                    form.appendChild(csrfInput);

                                                    // Method spoofing for DELETE
                                                    const methodInput = document.createElement('input');
                                                    methodInput.type = 'hidden';
                                                    methodInput.name = '_method';
                                                    methodInput.value = 'DELETE';
                                                    form.appendChild(methodInput);

                                                    document.body.appendChild(form);
                                                    form.submit();
                                                });
                                            });

                                            // ===== SAVE EDIT =====
                                            document.querySelectorAll('.save-edit-existing-btn').forEach(btn => {
                                                btn.addEventListener('click', function() {
                                                    const questionId = this.dataset.questionId;
                                                    const container = document.getElementById(`existing-question-${questionId}`);
                                                    const editMode = container.querySelector('.edit-mode-existing');
                                                    const updateUrl = editMode.dataset.updateUrl;

                                                    const questionText = editMode.querySelector('.question-text-input').value;
                                                    const maxScore = editMode.querySelector('.max-score-input').value;

                                                    if (!questionText.trim()) {
                                                        alert('Pertanyaan tidak boleh kosong!');
                                                        return;
                                                    }

                                                    // Create temporary form and submit
                                                    const form = document.createElement('form');
                                                    form.method = 'POST';
                                                    form.action = updateUrl;
                                                    form.style.display = 'none';

                                                    // CSRF token
                                                    const csrfInput = document.createElement('input');
                                                    csrfInput.type = 'hidden';
                                                    csrfInput.name = '_token';
                                                    csrfInput.value = getCsrfToken();
                                                    form.appendChild(csrfInput);

                                                    // Method spoofing for PUT
                                                    const methodInput = document.createElement('input');
                                                    methodInput.type = 'hidden';
                                                    methodInput.name = '_method';
                                                    methodInput.value = 'PUT';
                                                    form.appendChild(methodInput);

                                                    // Question text
                                                    const questionInput = document.createElement('input');
                                                    questionInput.type = 'hidden';
                                                    questionInput.name = 'question';
                                                    questionInput.value = questionText;
                                                    form.appendChild(questionInput);

                                                    // Max score
                                                    const scoreInput = document.createElement('input');
                                                    scoreInput.type = 'hidden';
                                                    scoreInput.name = 'max_score';
                                                    scoreInput.value = maxScore;
                                                    form.appendChild(scoreInput);

                                                    document.body.appendChild(form);
                                                    form.submit();
                                                });
                                            });

                                            // ===== ADD NEW QUESTION =====
                                            const addNewQuestionBtn = document.getElementById('submit-new-question-btn');
                                            if (addNewQuestionBtn) {
                                                addNewQuestionBtn.addEventListener('click', function() {
                                                    const formContainer = document.getElementById('add-new-question-form');
                                                    const storeUrl = formContainer.dataset.storeUrl;
                                                    const scoringEnabled = formContainer.dataset.scoringEnabled === '1';

                                                    const questionText = document.getElementById('new-question-text').value;
                                                    const maxScore = document.getElementById('new-question-score').value;

                                                    if (!questionText.trim()) {
                                                        alert('Pertanyaan tidak boleh kosong!');
                                                        return;
                                                    }

                                                    if (scoringEnabled && (!maxScore || maxScore < 1)) {
                                                        alert('Skor maksimal harus diisi dan lebih dari 0!');
                                                        return;
                                                    }

                                                    // Create temporary form and submit
                                                    const form = document.createElement('form');
                                                    form.method = 'POST';
                                                    form.action = storeUrl;
                                                    form.style.display = 'none';

                                                    // CSRF token
                                                    const csrfInput = document.createElement('input');
                                                    csrfInput.type = 'hidden';
                                                    csrfInput.name = '_token';
                                                    csrfInput.value = getCsrfToken();
                                                    form.appendChild(csrfInput);

                                                    // Question text
                                                    const questionInput = document.createElement('input');
                                                    questionInput.type = 'hidden';
                                                    questionInput.name = 'question';
                                                    questionInput.value = questionText;
                                                    form.appendChild(questionInput);

                                                    // Max score
                                                    const scoreInput = document.createElement('input');
                                                    scoreInput.type = 'hidden';
                                                    scoreInput.name = 'max_score';
                                                    scoreInput.value = maxScore;
                                                    form.appendChild(scoreInput);

                                                    document.body.appendChild(form);
                                                    form.submit();
                                                });
                                            }
                                        });
                                    </script>
                                @endif

                                {{-- ✅ FIX: No nested form - use button with JS handler --}}
                                @if($content->exists)
                                    <div class="border border-gray-200 rounded-lg" id="add-new-question-form" data-store-url="{{ route('essay.questions.store', $content) }}" data-scoring-enabled="{{ $content->scoring_enabled ? '1' : '0' }}">
                                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                            <h4 class="font-semibold text-gray-900 flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Tambah Pertanyaan Baru
                                            </h4>
                                        </div>

                                        <div class="p-6 space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Pertanyaan <span class="text-red-500">*</span>
                                                </label>
                                                <textarea
                                                    id="new-question-text"
                                                    rows="4"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    placeholder="Tulis pertanyaan essay yang akan dijawab oleh peserta..."
                                                    required></textarea>
                                            </div>

                                            @if($content->scoring_enabled)
                                                <div class="w-40">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Skor Maksimal <span class="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        type="number"
                                                        id="new-question-score"
                                                        min="1"
                                                        max="1000"
                                                        value="100"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                        required />
                                                </div>
                                            @else
                                                <input type="hidden" id="new-question-score" value="0">
                                            @endif

                                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                                <button type="button" id="submit-new-question-btn" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                    Tambah Pertanyaan
                                                </button>
                                            </div>

                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                <p class="text-xs text-blue-700">
                                                    <strong>💡 Tip:</strong> Tombol ini akan menambahkan pertanyaan baru tanpa mengubah konten lain.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Form untuk CREATE mode (bagian dari main form) --}}
                                    <div class="border border-gray-200 rounded-lg">
                                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                            <h4 class="font-semibold text-gray-900 flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Pertanyaan Essay
                                            </h4>
                                        </div>

                                        <div class="p-6 space-y-6">
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

                                                        <template x-if="!$root.content.scoring_enabled">
                                                            <input type="hidden" :name="'questions[' + index + '][max_score]'" value="0">
                                                        </template>
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

                                                <div class="text-right">
                                                    <p class="text-sm text-gray-600">
                                                        Pertanyaan: <span x-text="questions.length" class="font-semibold"></span>
                                                    </p>
                                                    <p x-show="$root.content.scoring_enabled" class="text-sm text-gray-600">
                                                        Total Skor: <span x-text="totalScore" class="font-semibold text-green-600"></span> poin
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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

                        {{-- ✅ FIX: Untuk VIDEO type - skip render kalau existing essay --}}
                        @if(!$content->exists || $content->type !== 'essay')
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
                        @endif

                        <div x-show="isType('document') || isType('image')" x-cloak class="animate-fadeIn">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    📁 <span x-text="isType('image') ? 'Unggah Gambar' : 'Unggah Dokumen'"></span>
                                </label>

                                {{-- Opsi Kontrol Akses untuk Dokumen --}}
                                <div x-show="isType('document')"
                                     x-data="{ selectedAccessType: '{{ old('document_access_type', $content->document_access_type ?? 'both') }}' }"
                                     class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        🔐 Kontrol Akses Dokumen
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                   name="document_access_type"
                                                   value="both"
                                                   x-model="selectedAccessType"
                                                   class="sr-only">
                                            <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                                 :class="selectedAccessType === 'both' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'">
                                                <div class="text-2xl mb-2">👁️💾</div>
                                                <div class="text-xs font-medium">Preview & Download</div>
                                                <div class="text-[10px] text-gray-500 mt-1">Keduanya</div>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                   name="document_access_type"
                                                   value="download_only"
                                                   x-model="selectedAccessType"
                                                   class="sr-only">
                                            <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                                 :class="selectedAccessType === 'download_only' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300'">
                                                <div class="text-2xl mb-2">💾</div>
                                                <div class="text-xs font-medium">Download Saja</div>
                                                <div class="text-[10px] text-gray-500 mt-1">Tanpa preview</div>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                   name="document_access_type"
                                                   value="preview_only"
                                                   x-model="selectedAccessType"
                                                   class="sr-only">
                                            <div class="p-4 border-2 rounded-xl text-center transition-all duration-300 hover:shadow-md"
                                                 :class="selectedAccessType === 'preview_only' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300'">
                                                <div class="text-2xl mb-2">👁️</div>
                                                <div class="text-xs font-medium">Preview Saja</div>
                                                <div class="text-[10px] text-gray-500 mt-1">Tanpa download</div>
                                            </div>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-3">
                                        💡 <strong>Tips:</strong> Gunakan "Preview Saja" untuk mencegah user mendownload dokumen.
                                    </p>
                                </div>

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

                                <!-- Multiple Documents Uploader (tambahan untuk tipe document) -->
                                <div x-show="isType('document')" class="mt-6">
                                    <label for="documents" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Lampirkan Beberapa Dokumen (opsional)
                                    </label>
                                    <input type="file" name="documents[]" id="documents" multiple
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,application/rtf"
                                           class="block w-full text-sm text-gray-600">
                                    <p class="text-xs text-gray-500 mt-1">Maks 20 file, masing-masing hingga 100MB.</p>
                                    <div id="documents_preview" class="mt-3 space-y-2"></div>
                                </div>

                                @if($content->documents && $content->documents->count())
                                    <div class="mt-6" x-data>
                                        <p class="text-sm font-semibold text-gray-700 mb-2">Lampiran Dokumen</p>
                                        <input type="hidden" name="document_order" id="document_order" value="{{ $content->documents->pluck('id')->implode(',') }}">
                                        <div id="delete_documents_container"></div>
                                        <div id="current_documents_list" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($content->documents as $doc)
                                                <div class="group relative rounded-lg overflow-hidden border bg-white p-3 flex items-center justify-between cursor-move" draggable="true" data-id="{{ $doc->id }}">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8"/></svg>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->original_name ?? basename($doc->file_path) }}</p>
                                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">Buka</a>
                                                        </div>
                                                    </div>
                                                    <div class="opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button type="button" class="toggle-delete-doc p-1.5 rounded bg-white/80 text-red-600 hover:bg-white shadow ring-1 ring-black/5" title="Hapus dokumen">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m1 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7z"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Tip: Drag untuk mengubah urutan. Klik ikon tempat sampah untuk menandai penghapusan.</p>
                                    </div>
                                @endif

                                <!-- Multiple Images Uploader (tambahan untuk tipe image) -->
                                <div x-show="isType('image')" class="mt-6">
                                    <label for="images" class="block text-sm font-semibold text-gray-700 mb-2">
                                        🖼️ Tambah Beberapa Gambar (opsional)
                                    </label>
                                    <input type="file" name="images[]" id="images" accept="image/*" multiple class="block w-full text-sm text-gray-600">
                                    <p class="text-xs text-gray-500 mt-1">Anda dapat memilih lebih dari satu gambar. Setiap gambar maksimal 10MB.</p>
                                    <div id="images_preview" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
                                </div>

                                @if($content->images->count())
                                    <div class="mt-6" x-data>
                                        <p class="text-sm font-semibold text-gray-700 mb-2">Kelola Galeri</p>
                                        <input type="hidden" name="image_order" id="image_order" value="{{ $content->images->pluck('id')->implode(',') }}">
                                        <div id="delete_images_container"></div>
                                        <div id="current_images_list" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                            @foreach($content->images as $img)
                                                <div class="group relative rounded-lg overflow-hidden border bg-white cursor-move" draggable="true" data-id="{{ $img->id }}">
                                                    <img src="{{ Storage::url($img->file_path) }}" class="w-full h-32 object-cover select-none pointer-events-none" alt="Gambar {{ $loop->iteration }}">
                                                    <div class="absolute inset-0 bg-red-500/0 group-[.to-delete]:bg-red-500/20 transition-colors"></div>
                                                    <div class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button type="button" class="toggle-delete p-1 rounded bg-white/80 text-red-600 hover:bg-white shadow ring-1 ring-black/5" title="Hapus gambar">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m1 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7z"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Tip: Drag untuk mengubah urutan. Klik ikon tempat sampah untuk menandai penghapusan.</p>
                                    </div>
                                @endif
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
                                
                                <!-- Quiz Creation Method Toggle (Only show when creating new quiz) -->
                                <div class="mb-6" x-show="!content.quiz_id">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        📋 Cara Membuat Kuis
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="quiz_method" value="manual" class="sr-only" onchange="toggleQuizMethodEdit()" checked>
                                            <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-300 text-center quiz-method-card-edit">
                                                <div class="text-3xl mb-2">✍️</div>
                                                <h4 class="font-semibold text-gray-900">Manual</h4>
                                                <p class="text-xs text-gray-500 mt-1">Buat nanti di edit</p>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="quiz_method" value="import" class="sr-only" onchange="toggleQuizMethodEdit()">
                                            <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-300 text-center quiz-method-card-edit">
                                                <div class="text-3xl mb-2">📊</div>
                                                <h4 class="font-semibold text-gray-900">Import Excel</h4>
                                                <p class="text-xs text-gray-500 mt-1">Upload file Excel</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Manual Method Fields -->
                                <div id="manual_quiz_fields_edit">
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

                                <!-- Import Method Fields -->
                                <div id="import_quiz_fields_edit" class="hidden">
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
                                        <label for="quiz_excel_file_edit" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-file-upload mr-2 text-orange-600"></i>Upload File Excel
                                        </label>
                                        <div class="flex items-center justify-center w-full">
                                            <label for="quiz_excel_file_edit" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-all duration-200">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                                    <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                                    <p class="text-xs text-gray-500">File Excel (XLSX, XLS) maksimal 2MB</p>
                                                    <p class="text-xs text-gray-400 mt-2" id="quiz_file_name_edit"></p>
                                                </div>
                                                <input id="quiz_excel_file_edit" name="quiz_excel_file" type="file" class="hidden" accept=".xlsx,.xls" onchange="updateQuizFileNameEdit(this)" />
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

                    <!-- ✅ ATTENDANCE SETTINGS SECTION (MOVED OUTSIDE ZOOM - FOR ALL CONTENT TYPES) -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 mt-6">
                        <div class="border-t border-green-200 pt-4 mt-0">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox"
                                        id="attendance_required"
                                        name="attendance_required"
                                        x-model="content.attendance_required"
                                        value="1"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="attendance_required" class="ml-3 block text-sm font-semibold text-gray-700">
                                        ✅ Require Attendance (Wajib hadir untuk melanjutkan)
                                    </label>
                                </div>

                                <div x-show="content.attendance_required"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    class="space-y-4">

                                    <div>
                                        <label for="min_attendance_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                            ⏱️ Minimum Durasi Kehadiran (menit)
                                        </label>
                                        <input type="number"
                                            name="min_attendance_minutes"
                                            id="min_attendance_minutes"
                                            x-model="content.min_attendance_minutes"
                                            min="1"
                                            placeholder="Contoh: 60 (untuk 1 jam)"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-300"
                                            :class="{ 'border-red-300 focus:border-red-500': errors.min_attendance_minutes }">
                                        <p x-show="errors.min_attendance_minutes" x-text="errors.min_attendance_minutes" class="text-sm text-red-600 mt-1"></p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Kosongkan jika tidak ada minimal durasi. Peserta harus hadir minimal selama durasi ini untuk bisa melanjutkan ke content berikutnya.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="attendance_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                            📝 Catatan Kehadiran (Opsional)
                                        </label>
                                        <textarea
                                            name="attendance_notes"
                                            id="attendance_notes"
                                            x-model="content.attendance_notes"
                                            rows="3"
                                            placeholder="Contoh: Peserta wajib hadir di Zoom meeting ini. Kehadiran akan dicatat oleh instruktur."
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-300 resize-none"></textarea>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Catatan ini akan ditampilkan kepada peserta sebagai informasi tentang persyaratan kehadiran.
                                        </p>
                                    </div>

                                    <!-- Info Box -->
                                    <div class="bg-green-100 border border-green-300 rounded-xl p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-semibold text-green-800">ℹ️ Cara Kerja Attendance Requirement</h3>
                                                <div class="mt-2 text-sm text-green-700">
                                                    <ul class="list-disc list-inside space-y-1">
                                                        <li><strong>Content berikutnya akan terkunci</strong> sampai instruktur mark kehadiran peserta</li>
                                                        <li>Peserta harus di-mark sebagai <strong>"Present"</strong> atau <strong>"Excused"</strong> untuk bisa lanjut</li>
                                                        <li>Status <strong>"Absent"</strong> atau <strong>"Late"</strong> akan memblokir akses ke content berikutnya</li>
                                                        <li>Durasi kehadiran minimal (jika diisi) akan di-check oleh sistem</li>
                                                        <li>Instruktur dapat mark kehadiran via: Course → Attendance button</li>
                                                        <li><strong>Certificate juga diblokir</strong> jika attendance requirement tidak terpenuhi</li>
                                                    </ul>
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
        // Drag & delete manager for existing images
        (function(){
            document.addEventListener('DOMContentLoaded', function(){
                const list = document.getElementById('current_images_list');
                const orderInput = document.getElementById('image_order');
                const delContainer = document.getElementById('delete_images_container');
                if (!list || !orderInput || !delContainer) return;

                function updateOrderInput(){
                    const ids = Array.from(list.querySelectorAll('[data-id]')).map(el => el.getAttribute('data-id'));
                    orderInput.value = ids.join(',');
                }

                // Initialize deletion toggles
                list.querySelectorAll('.toggle-delete').forEach(btn => {
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        const card = this.closest('[data-id]');
                        const id = card.getAttribute('data-id');
                        const isDel = card.classList.toggle('to-delete');
                        if (isDel) {
                            const input = document.createElement('input');
                            input.type = 'hidden'; input.name = 'delete_images[]'; input.value = id; input.dataset.ref = `del-${id}`;
                            delContainer.appendChild(input);
                        } else {
                            const sel = delContainer.querySelector(`[data-ref="del-${id}"]`);
                            if (sel) sel.remove();
                        }
                    });
                });

                // DnD reorder
                let draggingEl = null;
                list.addEventListener('dragstart', e => {
                    const el = e.target.closest('[data-id]');
                    if (!el) return; draggingEl = el; e.dataTransfer.effectAllowed = 'move';
                });
                list.addEventListener('dragover', e => {
                    e.preventDefault();
                    const target = e.target.closest('[data-id]');
                    if (!target || target === draggingEl) return;
                    const rect = target.getBoundingClientRect();
                    const before = (e.clientY - rect.top) / rect.height < 0.5;
                    target.parentNode.insertBefore(draggingEl, before ? target : target.nextSibling);
                });
                list.addEventListener('drop', e => { e.preventDefault(); updateOrderInput(); });
                list.addEventListener('dragend', () => { draggingEl = null; updateOrderInput(); });

                // Initial set
                updateOrderInput();
            });
        })();
        // Drag & delete manager for existing documents
        (function(){
            document.addEventListener('DOMContentLoaded', function(){
                const list = document.getElementById('current_documents_list');
                const orderInput = document.getElementById('document_order');
                const delContainer = document.getElementById('delete_documents_container');
                if (!list || !orderInput || !delContainer) return;

                function updateOrderInput(){
                    const ids = Array.from(list.querySelectorAll('[data-id]')).map(el => el.getAttribute('data-id'));
                    orderInput.value = ids.join(',');
                }

                // Initialize deletion toggles
                list.querySelectorAll('.toggle-delete-doc').forEach(btn => {
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        const card = this.closest('[data-id]');
                        const id = card.getAttribute('data-id');
                        const isDel = card.classList.toggle('to-delete');
                        if (isDel) {
                            const input = document.createElement('input');
                            input.type = 'hidden'; input.name = 'delete_documents[]'; input.value = id; input.dataset.ref = `del-doc-${id}`;
                            delContainer.appendChild(input);
                        } else {
                            const sel = delContainer.querySelector(`[data-ref="del-doc-${id}"]`);
                            if (sel) sel.remove();
                        }
                    });
                });

                // DnD reorder
                let draggingEl = null;
                list.addEventListener('dragstart', e => {
                    const el = e.target.closest('[data-id]');
                    if (!el) return; draggingEl = el; e.dataTransfer.effectAllowed = 'move';
                });
                list.addEventListener('dragover', e => {
                    e.preventDefault();
                    const target = e.target.closest('[data-id]');
                    if (!target || target === draggingEl) return;
                    const rect = target.getBoundingClientRect();
                    const before = (e.clientY - rect.top) / rect.height < 0.5;
                    target.parentNode.insertBefore(draggingEl, before ? target : target.nextSibling);
                });
                list.addEventListener('drop', e => { e.preventDefault(); updateOrderInput(); });
                list.addEventListener('dragend', () => { draggingEl = null; updateOrderInput(); });

                // Initial set
                updateOrderInput();
            });
        })();
        // Preview for multiple documents (filenames)
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
        // Preview untuk multiple images (edit)
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
        function contentFormManager(data) {
            return {
                content: data.content,
                formAction: data.content.id ? data.updateUrl : data.createUrl,
                errors: {},
                formHasErrors: false,
                uploadedFileName: '',
                uploadedImagePreviewUrl: null,

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

                    // ✅ INITIALIZE ATTENDANCE FIELDS (NEW)
                    if (!this.content.hasOwnProperty('attendance_required')) {
                        this.content.attendance_required = @json($content->attendance_required ?? false);
                    }
                    if (!this.content.hasOwnProperty('min_attendance_minutes')) {
                        this.content.min_attendance_minutes = @json($content->min_attendance_minutes ?? null);
                    }
                    if (!this.content.hasOwnProperty('attendance_notes')) {
                        this.content.attendance_notes = @json($content->attendance_notes ?? '');
                    }

                    // Fix quiz data conversion
                    if (this.content.quiz) {
                        this.content.quiz.show_answers_after_attempt = !!parseInt(this.content.quiz.show_answers_after_attempt);
                        this.content.quiz.enable_leaderboard = !!parseInt(this.content.quiz.enable_leaderboard);
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

                    // 🆕 TAMBAHAN: Reset scoring_enabled to true when changing to essay
                    if (type === 'essay' && !this.content.hasOwnProperty('scoring_enabled')) {
                        this.content.scoring_enabled = true;
                    }
                },

                async submitForm() {
                    // ✅ DEBUG: Log state sebelum submit
                    console.log('=== SUBMIT FORM DEBUG ===');
                    console.log('Content ID:', this.content.id);
                    console.log('Form Action:', this.formAction);
                    console.log('Content Object:', this.content);

                    if (this.validate()) {
                        // ✅ CRITICAL FIX: Pastikan form action benar sebelum submit
                        const form = document.getElementById('contentForm');
                        const actualAction = form.getAttribute('action');
                        console.log('Actual Form Action:', actualAction);

                        if (!this.content.id && actualAction && actualAction.includes('/contents/')) {
                            console.error('ERROR: Content ID is missing but form action suggests UPDATE!');
                            console.error('This will cause CREATE instead of UPDATE!');
                            alert('ERROR: Form state corrupted. Please reload the page and try again.');
                            return;
                        }

                        // Client-side compression for image uploads to speed up submission
                        await (async () => {
                            try {
                                if (this.isType('image')) {
                                    const compressImageFile = async (file, { maxWidth = 1600, quality = 0.8 } = {}) => {
                                        try {
                                            if (!(file && file.type && file.type.startsWith('image/'))) return file;
                                            if (file.size < 300 * 1024) return file; // skip small
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
                                            const targetType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                                            const blob = await new Promise(resolve => canvas.toBlob(resolve, targetType, quality));
                                            if (!blob) return file;
                                            const newName = file.name.replace(/\.(png|jpg|jpeg|webp)$/i, targetType === 'image/png' ? '.png' : '.jpg');
                                            return new File([blob], newName, { type: targetType, lastModified: Date.now() });
                                        } catch (e) { return file; }
                                    };

                                    const single = document.getElementById('file_upload');
                                    if (single && single.files && single.files[0] && single.files[0].type.startsWith('image/')) {
                                        const compressed = await compressImageFile(single.files[0]);
                                        const dt = new DataTransfer();
                                        dt.items.add(compressed);
                                        single.files = dt.files;
                                    }

                                    const multi = document.getElementById('images');
                                    if (multi && multi.files && multi.files.length) {
                                        const dt = new DataTransfer();
                                        for (const f of Array.from(multi.files)) {
                                            const cf = await compressImageFile(f);
                                            dt.items.add(cf);
                                        }
                                        multi.files = dt.files;
                                    }
                                }
                            } catch (_) {}
                        })();
                        form.submit();
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

                // Quiz management methods
                defaultQuizObject() {
                    return {
                        title: this.content.title || '',
                        description: '',
                        duration: 0,
                        passing_percentage: 70,
                        status: 'draft',
                        show_answers_after_attempt: false,
                        enable_leaderboard: false,
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

        // Toggle quiz method (manual vs import) for Edit page
        function toggleQuizMethodEdit() {
            const method = document.querySelector('input[name="quiz_method"]:checked').value;
            const manualFields = document.getElementById('manual_quiz_fields_edit');
            const importFields = document.getElementById('import_quiz_fields_edit');

            if (method === 'manual') {
                manualFields.classList.remove('hidden');
                importFields.classList.add('hidden');
                // Clear excel file input
                const fileInput = document.getElementById('quiz_excel_file_edit');
                if (fileInput) {
                    fileInput.value = '';
                    document.getElementById('quiz_file_name_edit').textContent = '';
                }
            } else {
                manualFields.classList.add('hidden');
                importFields.classList.remove('hidden');
            }

            // Update card styling
            document.querySelectorAll('.quiz-method-card-edit').forEach(card => {
                card.classList.remove('border-orange-500', 'bg-orange-50', 'shadow-lg');
            });
            const selectedCard = document.querySelector('input[name="quiz_method"]:checked').parentElement.querySelector('.quiz-method-card-edit');
            if (selectedCard) {
                selectedCard.classList.add('border-orange-500', 'bg-orange-50', 'shadow-lg');
            }
        }

        // Update quiz file name display for Edit page
        function updateQuizFileNameEdit(input) {
            const fileName = input.files[0]?.name;
            const fileNameDisplay = document.getElementById('quiz_file_name_edit');
            if (fileName && fileNameDisplay) {
                fileNameDisplay.textContent = 'File terpilih: ' + fileName;
                fileNameDisplay.classList.remove('text-gray-400');
                fileNameDisplay.classList.add('text-orange-600', 'font-semibold');
            }
        }
    </script>

    <style>
        .quiz-method-card-edit {
            transition: all 0.3s ease;
        }
        .quiz-method-card-edit:has(input:checked) {
            @apply border-orange-500 bg-orange-50 shadow-lg;
        }
    </style>
    @endpush
</x-app-layout>
