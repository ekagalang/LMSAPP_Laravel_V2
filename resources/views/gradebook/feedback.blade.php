<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Penilaian & Feedback untuk: <span class="font-normal">{{ $user->name }}</span>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            Kursus: <span class="font-medium">{{ $course->title }}</span>
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kolom Hasil Kuis -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Hasil Kuis</h3>
                            <div class="space-y-4">
                                @forelse ($quizzes as $quiz)
                                    @php $attempt = $attempts->where('quiz_id', $quiz->id)->last(); @endphp
                                    <div class="flex justify-between items-center p-4 border rounded-md">
                                        <span>{{ $quiz->title }}</span>
                                        @if ($attempt)
                                            <a href="{{ route('gradebook.review', $attempt) }}" class="font-semibold text-indigo-600 hover:underline">
                                                Skor: {{ number_format($attempt->score, 2) }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">Belum dikerjakan</span>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500">Tidak ada kuis di kursus ini.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Feedback -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Feedback Keseluruhan</h3>
                            <form action="{{ route('gradebook.storeFeedback', ['course' => $course, 'user' => $user]) }}" method="POST">
                                @csrf
                                <div>
                                    <label for="feedback" class="sr-only">Feedback</label>
                                    <textarea name="feedback" id="feedback" rows="8" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Tuliskan feedback atau catatan Anda untuk peserta ini...">{{ old('feedback', $existingFeedback) }}</textarea>
                                </div>
                                <div class="mt-4">
                                    <x-primary-button>Simpan Feedback</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
             <div class="mt-6">
                <a href="{{ route('courses.gradebook', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-900">&larr; Kembali ke Gradebook</a>
            </div>
        </div>
    </div>
</x-app-layout>
