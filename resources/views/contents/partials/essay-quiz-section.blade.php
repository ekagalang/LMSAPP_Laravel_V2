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

                @if ($submission->is_fully_graded)
                    <div class="mt-4">
                        <a href="{{ route('essays.result', $submission->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md inline-block">
                            Lihat Nilai dan Feedback
                        </a>
                        <div class="mt-2">
                            <span class="text-sm">Total Nilai: {{ $submission->total_score }}/{{ $submission->max_total_score }}</span>
                        </div>
                    </div>
                @else
                    <p class="mt-2">Jawaban Anda sedang menunggu penilaian dari instruktur.</p>
                @endif
            </div>

        {{-- JIKA BELUM ADA JAWABAN DAN USER ADALAH PESERTA --}}
        @elseif (Auth::user()->hasRole('participant'))
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
                    <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                        Jawab Pertanyaan Essay ({{ $questions->count() }} Soal)
                    </h3>
                    
                    @foreach ($questions as $index => $question)
                        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    Soal {{ $index + 1 }}
                                </h4>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $question->max_score }} poin
                                </span>
                            </div>
                            
                            <div class="prose dark:prose-invert max-w-none mb-4">
                                {!! nl2br(e($question->question)) !!}
                            </div>
                            
                            <div>
                                <label for="answer_{{ $question->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Jawaban Anda:
                                </label>
                                <textarea
                                    id="answer_{{ $question->id }}"
                                    name="answer_{{ $question->id }}"
                                    rows="6"
                                    class="summernote-editor block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300"
                                    placeholder="Tulis jawaban Anda untuk soal {{ $index + 1 }}..."
                                    required
                                >{{ old("answer_{$question->id}") }}</textarea>
                                @error("answer_{$question->id}")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-6 flex items-center justify-between">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Total: {{ $questions->sum('max_score') }} poin
                        </p>
                        <x-primary-button>{{ __('Kirim Semua Jawaban') }}</x-primary-button>
                    </div>
                </form>
            @endif

        {{-- JIKA USER BISA EDIT CONTENT (instructor/admin) --}}
        @elseif (Auth::user()->can('update', $content->lesson->course))
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-4">Kelola Pertanyaan Essay</h3>
                
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
                    
                    <div class="mb-4">
                        <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">
                            Skor Maksimal:
                        </label>
                        <input
                            type="number"
                            id="max_score"
                            name="max_score"
                            min="1"
                            max="1000"
                            value="100"
                            class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Tambah Pertanyaan
                    </button>
                </form>

                {{-- List pertanyaan existing --}}
                @if ($questions->count() > 0)
                    <div class="space-y-4">
                        <h4 class="font-medium">Pertanyaan yang Ada ({{ $questions->count() }})</h4>
                        @foreach ($questions as $index => $question)
                            <div class="flex items-start justify-between p-4 bg-white border rounded-lg">
                                <div class="flex-1">
                                    <h5 class="font-medium">Soal {{ $index + 1 }} ({{ $question->max_score }} poin)</h5>
                                    <p class="text-gray-600 mt-1">{!! nl2br(e(Str::limit($question->question, 200))) !!}</p>
                                </div>
                                <form action="{{ route('essay.questions.destroy', $question->id) }}" method="POST" class="ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 text-sm"
                                            onclick="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                        <p class="text-sm text-gray-500">
                            Total skor maksimal: {{ $questions->sum('max_score') }} poin
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>

@elseif ($content->type == 'quiz' && $content->quiz)
    {{-- QUIZ LOGIC TETAP SAMA --}}
    @php
        $latestAttempt = Auth::user()
            ->quizAttempts()
            ->where('quiz_id', $content->quiz->id)
            ->latest('created_at')
            ->first();
        
        $hasPassed = $latestAttempt && $latestAttempt->score >= $content->quiz->passing_grade;
    @endphp

    <div class="mt-6 border-t pt-6">
        @if($latestAttempt)
            <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Hasil Kuis</h3>
            <div class="p-4 rounded-md border {{ $hasPassed ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                <p class="font-semibold {{ $hasPassed ? 'text-green-800' : 'text-red-800' }}">
                    Skor: {{ $latestAttempt->score }}/{{ $content->quiz->total_points }}
                    ({{ number_format(($latestAttempt->score / $content->quiz->total_points) * 100, 1) }}%)
                </p>
                <p class="text-sm {{ $hasPassed ? 'text-green-600' : 'text-red-600' }}">
                    {{ $hasPassed ? 'Lulus' : 'Tidak Lulus' }} - Batas Kelulusan: {{ $content->quiz->passing_grade }}%
                </p>
                <a href="{{ route('quizzes.result', [$content->quiz, $latestAttempt]) }}" 
                   class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Lihat Detail Hasil
                </a>
            </div>
        @elseif(Auth::user()->hasRole('participant'))
            <div class="text-center">
                <p class="mb-4 text-gray-600 dark:text-gray-400">Kuis belum pernah dikerjakan</p>
                <a href="{{ route('quizzes.start', $content->quiz) }}" 
                   class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    Mulai Kuis
                </a>
            </div>
        @endif
    </div>
@endif