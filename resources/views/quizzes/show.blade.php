<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                {{-- Navigasi kembali ke kursus induk dari pelajaran kuis --}}
                <a href="{{ route('courses.show', $quiz->lesson->course) }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Kembali ke Kursus') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-2">
                    Kuis: {{ $quiz->title }}
                </h2>
                {{-- Tampilkan judul pelajaran dan kursus --}}
                <p class="text-sm text-gray-600">Pelajaran: {{ $quiz->lesson?->title }} (Kursus: {{ $quiz->lesson?->course?->title }})</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $quiz->title }}</h3>
                <p class="text-gray-700 mb-4">{{ $quiz->description }}</p>

                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-6">
                    <div><strong>Total Soal:</strong> {{ $quiz->questions->count() }}</div>
                    <div><strong>Total Nilai:</strong> {{ $quiz->total_marks }}</div>
                    <div><strong>Nilai Lulus:</strong> {{ $quiz->pass_marks }}</div>
                    <div><strong>Batas Waktu:</strong> {{ $quiz->time_limit ? $quiz->time_limit . ' menit' : 'Tidak ada' }}</div>
                    <div><strong>Dibuat Oleh:</strong> {{ $quiz->instructor->name }}</div>
                    <div><strong>Status:</strong> <span class="capitalize">{{ $quiz->status }}</span></div>
                </div>

                @auth
                    @if (Auth::user()->isParticipant())
                        @if ($quiz->status == 'published')
                            <form action="{{ route('quizzes.start_attempt', $quiz) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Mulai Kuis') }}
                                </button>
                            </form>
                        @else
                            <p class="text-red-500">Kuis ini belum dipublikasikan dan tidak dapat dikerjakan.</p>
                        @endif
                    @else {{-- Admin atau Instruktur bisa melihat pertanyaan --}}
                        <div class="mt-8 border-t pt-4">
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Daftar Pertanyaan:</h4>
                            @if ($quiz->questions->isEmpty())
                                <p class="text-gray-500">Kuis ini belum memiliki pertanyaan.</p>
                            @else
                                <div class="space-y-6">
                                    @foreach ($quiz->questions as $question)
                                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                            <p class="font-medium text-gray-800 mb-2">Q: {{ $question->question_text }} ({{ $question->marks }} poin)</p>
                                            <p class="text-sm text-gray-600 mb-2">Tipe: {{ ucfirst(str_replace('_', ' ', $question->type)) }}</p>
                                            @if ($question->type === 'multiple_choice')
                                                <ul class="list-disc list-inside text-sm text-gray-700">
                                                    @foreach ($question->options as $option)
                                                        <li class="{{ $option->is_correct ? 'text-green-600 font-semibold' : '' }}">
                                                            {{ $option->option_text }}
                                                            @if ($option->is_correct) <span class="text-green-600">(Benar)</span> @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @elseif ($question->type === 'true_false')
                                                <p class="text-sm text-gray-700">Jawaban Benar:
                                                    @php
                                                        $correctTFOption = $question->options->where('is_correct', true)->first();
                                                    @endphp
                                                    {{ $correctTFOption ? ($correctTFOption->option_text === 'True' ? 'True' : 'False') : 'Tidak ditentukan' }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>