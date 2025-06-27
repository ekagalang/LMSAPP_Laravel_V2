<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Kuis: {{ $quiz->title }}
        </h2>
        <p class="text-sm text-gray-600">Kursus: {{ $quiz->course->title }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Ringkasan Hasil</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
                    <p><strong>Skor Anda:</strong> <span class="text-indigo-600 text-xl font-bold">{{ $attempt->score }}</span> / {{ $quiz->total_marks }}</p>
                    <p><strong>Status Kelulusan:</strong>
                        @if ($attempt->passed)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">LULUS</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-medium">TIDAK LULUS</span>
                        @endif
                    </p>
                    <p><strong>Nilai Minimal Lulus:</strong> {{ $quiz->pass_marks }}</p>
                    <p><strong>Waktu Mulai:</strong> {{ $attempt->started_at->format('d M Y, H:i') }}</p>
                    <p><strong>Waktu Selesai:</strong> {{ $attempt->completed_at->format('d M Y, H:i') }}</p>
                    <p><strong>Durasi Pengerjaan:</strong> {{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}</p>
                </div>

                @if ($quiz->show_answers_after_attempt)
                    <h4 class="text-xl font-bold text-gray-900 mt-8 mb-4 border-t pt-4">Tinjau Jawaban Anda</h4>
                    <div class="space-y-6">
                        @foreach ($quiz->questions as $index => $question)
                            @php
                                $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                                $isCorrect = $userAnswer ? $userAnswer->is_correct : false;
                                $correctOption = $question->options->where('is_correct', true)->first();
                            @endphp
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm border {{ $isCorrect ? 'border-green-400' : 'border-red-400' }}">
                                <p class="font-bold text-lg text-gray-900 mb-3">
                                    {{ ($index + 1) }}. {{ $question->question_text }}
                                    <span class="float-right text-sm font-normal {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $isCorrect ? 'Benar' : 'Salah' }} ({{ $question->marks }} poin)
                                    </span>
                                </p>

                                @if ($question->type === 'multiple_choice')
                                    <p class="text-sm text-gray-700 font-semibold mb-2">Pilihan Anda:</p>
                                    <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
                                        @foreach ($question->options as $option)
                                            <li class="{{ $option->id == $userAnswer->option_id ? 'font-bold' : '' }} {{ $option->is_correct ? 'text-green-600' : '' }} {{ $option->id == $userAnswer->option_id && !$option->is_correct ? 'text-red-600' : '' }}">
                                                {{ $option->option_text }}
                                                @if ($option->id == $userAnswer->option_id)
                                                    <span class="font-bold">(Pilihan Anda)</span>
                                                @endif
                                                @if ($option->is_correct)
                                                    <span class="text-green-600">(Jawaban Benar)</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif ($question->type === 'true_false')
                                    <p class="text-sm text-gray-700 font-semibold mb-2">Jawaban Anda: {{ $userAnswer->answer_text }}</p>
                                    <p class="text-sm text-gray-700">Jawaban Benar: {{ $correctOption ? $correctOption->option_text : 'N/A' }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('courses.show', $quiz->course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Kembali ke Kursus
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>