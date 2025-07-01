<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Kuis: {{ $quiz->title }}
        </h2>
        <p class="text-sm text-gray-600">
            Kursus: 
            @if ($quiz->lesson && $quiz->lesson->course)
                {{ $quiz->lesson->course->title }}
            @else
                Kursus Tidak Ditemukan
            @endif
        </p>
        @if ($quiz->time_limit)
            <p class="text-sm text-red-600 mt-1">Sisa Waktu: <span id="time-left"></span></p>
        @endif
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="quiz-attempt-form" method="POST" action="{{ route('quizzes.submit_attempt', [$quiz, $attempt]) }}">
                    @csrf

                    <div class="space-y-6">
                        @foreach ($quiz->questions as $index => $question)
                            <div class="question-item bg-gray-50 p-4 rounded-lg shadow-sm">
                                <p class="font-bold text-lg text-gray-900 mb-3">
                                    {{ ($index + 1) }}. {{ $question->question_text }} ({{ $question->marks }} poin)
                                </p>

                                @if ($question->type === 'multiple_choice')
                                    <div class="options space-y-2">
                                        @foreach ($question->options as $option)
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="answers[{{ $index }}][option_id]" value="{{ $option->id }}" class="form-radio text-indigo-600" required>
                                                <span class="ml-2 text-gray-700">{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                                @elseif ($question->type === 'true_false')
                                    <div class="options space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="answers[{{ $index }}][answer_text]" value="True" class="form-radio text-indigo-600" required>
                                            <span class="ml-2 text-gray-700">True</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="answers[{{ $index }}][answer_text]" value="False" class="form-radio text-indigo-600" required>
                                            <span class="ml-2 text-gray-700">False</span>
                                        </label>
                                    </div>
                                    <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 text-right">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Selesai & Kirim Kuis') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($quiz->time_limit)
        <script>
            // Timer logic
            const timeLimit = {{ $quiz->time_limit }}; // in minutes
            let timeLeft = timeLimit * 60; // in seconds
            const timeLeftDisplay = document.getElementById('time-left');
            const quizForm = document.getElementById('quiz-attempt-form');

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timeLeftDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    alert('Waktu kuis habis! Jawaban Anda akan dikirim otomatis.');
                    quizForm.submit(); // Automatically submit the form
                } else {
                    timeLeft--;
                }
            }

            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer(); // Call once immediately to display initial time
        </script>
    @endif
</x-app-layout>