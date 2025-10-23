@if ($content->type == 'essay')
    @php
        $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
        $questions = $content->essayQuestions;
    @endphp

    <div class="mt-6 border-t pt-6">
        {{-- JIKA SUDAH ADA JAWABAN --}}
        @if ($submission)
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md dark:bg-green-900 dark:text-green-200 dark:border-green-600" role="alert">
                <p class="font-bold">Anda Sudah Mengumpulkan Jawaban</p>
                <p>Jawaban Anda dikumpulkan pada: {{ $submission->created_at->format('d F Y, H:i') }}</p>

                @php
                        // Logic untuk cek apakah essay sudah diproses
                        $isProcessed = false;
                        
                        // FITUR BARU: Jika essay tidak perlu review (latihan mandiri), langsung dianggap sudah diproses
                        if (!($content->requires_review ?? true)) {
                            $isProcessed = true; // Auto-processed tanpa review instruktur
                        } else {
                            if ($submission->content->scoring_enabled) {
                                // Dengan scoring
                                if ($submission->content->grading_mode === 'overall') {
                                    $isProcessed = $submission->answers()->whereNotNull('score')->count() > 0;
                                } else {
                                    $isProcessed = $submission->is_fully_graded;
                                }
                            } else {
                                // Tanpa scoring
                                if ($submission->content->grading_mode === 'overall') {
                                    $isProcessed = $submission->answers()->whereNotNull('feedback')->count() > 0;
                                } else {
                                    $totalQuestions = $submission->content->essayQuestions()->count();
                                    $answersWithFeedback = $submission->answers()->whereNotNull('feedback')->count();
                                    $isProcessed = $answersWithFeedback >= $totalQuestions;
                                }
                            }
                        }
                    @endphp

                    @if ($isProcessed)
                        <div class="mt-4">
                            <a href="{{ route('essays.result', $submission->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md inline-block">
                                @if($submission->content->scoring_enabled)
                                    Lihat Nilai dan Feedback
                                @else
                                    Lihat Hasil dan Feedback
                                @endif
                            </a>
                            @if($submission->content->scoring_enabled)
                                <div class="mt-2">
                                    <span class="text-sm">
                                        @if($submission->content->grading_mode === 'overall')
                                            Total Nilai: {{ $submission->answers()->whereNotNull('score')->first()->score ?? 0 }}/{{ $submission->max_total_score }}
                                        @else
                                            Total Nilai: {{ $submission->total_score }}/{{ $submission->max_total_score }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    @else
                        @if(!($submission->content->requires_review ?? true))
                            {{-- Essay latihan mandiri - tidak perlu review --}}
                            <div class="mt-4">
                                <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    ✅ Latihan Selesai
                                </div>
                                <p class="mt-2 text-sm text-gray-600">Essay latihan mandiri telah berhasil dikumpulkan dan langsung selesai.</p>
                                <div class="mt-3">
                                    <a href="{{ route('essays.result', $submission->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md inline-block">
                                        Lihat Jawaban Saya
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Essay yang masih menunggu review --}}
                            <p class="mt-2">
                                @if($submission->content->scoring_enabled)
                                    Jawaban Anda sedang menunggu penilaian dari instruktur.
                                @else
                                    Jawaban Anda sedang menunggu tinjauan dari instruktur.
                                @endif
                            </p>
                        @endif
                    @endif
            </div>

        {{-- JIKA BELUM ADA JAWABAN DAN USER DIIZINKAN MENGERJAKAN --}}
        @elseif (Auth::user()->can('attempt quizzes'))
            @if ($questions->isEmpty())
                {{-- Fallback untuk essay lama tanpa questions --}}
                <form action="{{ route('essays.store', $content) }}" method="POST" class="mt-6">
                    @csrf
                    <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Tulis Jawaban Anda:</h3>
                    
                    <x-forms.summernote-editor id="essay_editor" name="essay_content" />
                    
                    <div class="mt-4">
                        <x-primary-button>{{ __('Kirim Jawaban') }}</x-primary-button>
                    </div>
                </form>
            @else
                {{-- NEW SYSTEM: Multiple questions --}}
                <form action="{{ route('essays.store', $content) }}" method="POST" class="mt-6">
                    @csrf
                    <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Pertanyaan Essay</h3>
                    
                    @foreach($questions as $index => $question)
                        <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="text-lg font-medium text-gray-700">Pertanyaan {{ $index + 1 }}</h4>
                                {{-- Tampilkan max score hanya jika scoring enabled --}}
                                @if ($content->scoring_enabled)
                                    <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $question->max_score }} poin</span>
                                @endif
                            </div>
                            <p class="text-gray-600 mb-4">{{ $question->question }}</p>
                            <div>
                                <textarea 
                                    name="answer_{{ $question->id }}" 
                                    rows="6" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Tulis jawaban Anda di sini..."
                                    required>{{ old("answer_{$question->id}") }}</textarea>
                                @error("answer_{$question->id}")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-6 flex items-center justify-between">
                        {{-- Tampilkan total score hanya jika scoring enabled --}}
                        @if ($content->scoring_enabled)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Total: {{ $questions->sum('max_score') }} poin
                            </p>
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $questions->count() }} pertanyaan
                            </p>
                        @endif
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                            {{ __('Kirim Semua Jawaban') }}
                        </button>
                    </div>
                </form>
            @endif

        {{-- JIKA USER BISA EDIT CONTENT (instructor/admin) --}}
        @elseif (Auth::user()->can('update', $content->lesson->course))
            <div class="mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Kelola Pertanyaan Essay</h3>
                    {{-- Status scoring indicator --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Status Penilaian:</span>
                        @if ($content->scoring_enabled)
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Dengan Penilaian
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tanpa Penilaian
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Form tambah pertanyaan --}}
                <form action="{{ route('essay.questions.store', $content) }}" method="POST" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    @csrf
                    <div class="mb-4">
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                            Pertanyaan Baru:
                        </label>
                        <textarea
                            id="question"
                            name="question"
                            rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Masukkan pertanyaan essay..."
                            required
                        ></textarea>
                    </div>
                    
                    {{-- Max score input hanya tampil jika scoring enabled --}}
                    @if ($content->scoring_enabled)
                        <div class="mb-4">
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">
                                Skor Maksimal:
                            </label>
                            <input
                                type="number"
                                id="max_score"
                                name="max_score"
                                min="1"
                                value="100"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            >
                        </div>
                    @else
                        <input type="hidden" name="max_score" value="0">
                    @endif
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                        Tambah Pertanyaan
                    </button>
                </form>

                {{-- Daftar pertanyaan existing --}}
                @if($questions->count() > 0)
                    <div class="space-y-4">
                        @foreach($questions as $index => $question)
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-700">Pertanyaan {{ $index + 1 }}</h4>
                                        <p class="text-gray-600 mt-1">{{ $question->question }}</p>
                                        {{-- Tampilkan max score hanya jika scoring enabled --}}
                                        @if ($content->scoring_enabled)
                                            <span class="text-sm text-gray-500 mt-2 inline-block">Skor Maksimal: {{ $question->max_score }} poin</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('essay.questions.destroy', $question) }}" method="POST" class="ml-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus pertanyaan ini?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endif
