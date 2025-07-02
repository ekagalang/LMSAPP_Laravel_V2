<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- PERBAIKAN: Menggunakan $quiz->title secara langsung --}}
            Hasil Kuis: {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold mb-4">Hasil untuk: {{ $quiz->title }}</h3>

                    <div class="mb-6 p-4 rounded-lg {{ $quizAttempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{-- PERBAIKAN: Menggunakan $quiz->total_marks secara langsung --}}
                        <p class="font-bold">Skor Anda: {{ number_format($quizAttempt->score, 2) }} / {{ $quiz->total_marks }}</p>
                        <p>Status: {{ $quizAttempt->passed ? 'Lulus' : 'Tidak Lulus' }}</p>
                    </div>

                    {{-- PERBAIKAN: Menggunakan $quiz->show_answers_after_attempt secara langsung --}}
                    @if($quiz->show_answers_after_attempt)
                        <h4 class="text-xl font-bold mb-4">Detail Jawaban</h4>
                        
                        {{-- PERBAIKAN: Melakukan perulangan pada $quiz->questions secara langsung --}}
                        @foreach($quiz->questions as $question)
                            <div class="mb-6 pb-4 border-b">
                                <p class="font-semibold">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                                <p class="text-sm text-gray-600 mb-2">({{ $question->marks }} Poin)</p>

                                @php
                                    $userAnswer = $quizAttempt->answers->where('question_id', $question->id)->first();
                                    $correctOption = $question->options->where('is_correct', true)->first();
                                @endphp

                                <div class="space-y-2 mt-2">
                                    @foreach($question->options as $option)
                                        @php
                                            $isUserAnswer = $userAnswer && $userAnswer->option_id == $option->id;
                                            $isCorrect = $option->is_correct;
                                            $class = '';

                                            if ($isUserAnswer && $isCorrect) {
                                                $class = 'bg-green-100 border-green-400'; // Jawaban benar
                                            } elseif ($isUserAnswer && !$isCorrect) {
                                                $class = 'bg-red-100 border-red-400'; // Jawaban salah
                                            } elseif (!$isUserAnswer && $isCorrect) {
                                                $class = 'bg-blue-100 border-blue-400'; // Kunci jawaban yang tidak dipilih
                                            } else {
                                                $class = 'bg-gray-100 border-gray-300'; // Opsi lain
                                            }
                                        @endphp
                                        <div class="p-3 rounded border {{ $class }}">
                                            {{ $option->option_text }}
                                            @if ($isUserAnswer && $isCorrect) <span class="text-green-600 font-bold ml-2">(Jawaban Anda Benar)</span> @endif
                                            @if ($isUserAnswer && !$isCorrect) <span class="text-red-600 font-bold ml-2">(Jawaban Anda Salah)</span> @endif
                                            @if (!$isUserAnswer && $isCorrect) <span class="text-blue-600 font-bold ml-2">(Kunci Jawaban)</span> @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if(!$userAnswer)
                                    <div class="mt-2 p-3 rounded border bg-yellow-100 border-yellow-400">
                                        <p class="text-yellow-800 font-semibold">Tidak Dijawab</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p>Tinjauan jawaban tidak tersedia untuk kuis ini.</p>
                    @endif

                    <div class="mt-6">
                        {{-- PERBAIKAN: Menggunakan $quiz->lesson->course secara langsung --}}
                        <a href="{{ route('courses.show', $quiz->lesson->course) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Kembali ke Kursus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>