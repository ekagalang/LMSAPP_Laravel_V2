@if ($content->type == 'essay')
    @php
        // Cek apakah peserta sudah pernah mengirimkan jawaban untuk esai ini
        $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
    @endphp

    <div class="mt-6 border-t pt-6">
        {{-- JIKA SUDAH ADA JAWABAN --}}
        @if ($submission)
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md dark:bg-green-900 dark:text-green-200 dark:border-green-600" role="alert">
                <p class="font-bold">Anda Sudah Mengumpulkan Jawaban</p>
                <p>Jawaban Anda dikumpulkan pada: {{ $submission->created_at->format('d F Y, H:i') }}</p>

                @if ($submission->graded_at)
                    {{-- [LOGIKA BARU] Jika sudah dinilai, tampilkan tombol untuk melihat hasil --}}
                    <div class="mt-4">
                        <a href="{{ route('essays.result', $submission->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md inline-block">
                            Lihat Nilai dan Feedback
                        </a>
                    </div>
                @else
                    {{-- [LOGIKA BARU] Jika belum dinilai --}}
                    <p class="mt-2">Jawaban Anda sedang menunggu penilaian dari instruktur.</p>
                @endif
            </div>

        {{-- JIKA BELUM ADA JAWABAN DAN USER ADALAH PESERTA --}}
        @elseif (Auth::user()->hasRole('participant'))
            <form action="{{ route('essays.store', $content) }}" method="POST" class="mt-6">
                @csrf
                <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Tulis Jawaban Anda:</h3>
                
                {{-- Textarea ini akan diubah menjadi editor oleh TinyMCE --}}
                <x-forms.summernote-editor id="essay_editor" name="essay_content" />
                
                <div class="mt-4">
                    <x-primary-button>{{ __('Kirim Jawaban') }}</x-primary-button>
                </div>
            </form>

            {{-- Skrip ini hanya akan dimuat jika form jawaban ditampilkan --}}
        @endif
    </div>

@elseif ($content->type == 'quiz' && $content->quiz)
    @php
        // [LOGIKA BARU] Cek percobaan kuis terakhir dari peserta
        $latestAttempt = Auth::user()
            ->quizAttempts()
            ->where('quiz_id', $content->quiz->id)
            ->latest('created_at')
            ->first();
        
        $hasPassed = $latestAttempt && $latestAttempt->score >= $content->quiz->passing_grade;
    @endphp

    <div class="mt-6 border-t pt-6">
        @if($latestAttempt)
            {{-- [TAMPILAN BARU] Jika sudah pernah mengerjakan kuis --}}
            <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Hasil Kuis</h3>
            <div class="p-4 rounded-md border 
                {{ $hasPassed ? 'bg-green-100 border-green-500 text-green-800 dark:bg-green-900 dark:text-green-200 dark:border-green-600' : 'bg-red-100 border-red-500 text-red-800 dark:bg-red-900 dark:text-red-200 dark:border-red-600' }}">
                
                <p class="text-sm">Nilai terakhir Anda:</p>
                <p class="font-bold text-3xl">{{ $latestAttempt->score }}<span class="text-base font-normal">/100</span></p>
                <p class="text-sm mt-1">Nilai minimal untuk lulus: {{ $content->quiz->passing_grade }}</p>
                
                <p class="font-bold mt-3">{{ $hasPassed ? 'Selamat, Anda telah lulus kuis ini!' : 'Anda belum mencapai nilai minimal.' }}</p>

                <div class="mt-4">
                    {{-- PERBAIKAN: Memberikan parameter 'quiz' dan 'attempt' ke route --}}
                    <a href="{{ route('quizzes.result', ['quiz' => $content->quiz->id, 'attempt' => $latestAttempt->id]) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Lihat Detail Jawaban
                    </a>
                    
                    @if(!$hasPassed)
                        {{-- Tampilkan tombol coba lagi jika belum lulus --}}
                        <a href="{{ route('quizzes.start', $content->quiz->id) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900">
                            Coba Lagi
                        </a>
                    @endif
                </div>
            </div>
        @else
             {{-- [TAMPILAN LAMA] Jika belum pernah mengerjakan --}}
            <a href="{{ route('quizzes.start', $content->quiz->id) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700">
                Mulai Kuis
            </a>
        @endif
    </div>
@endif
