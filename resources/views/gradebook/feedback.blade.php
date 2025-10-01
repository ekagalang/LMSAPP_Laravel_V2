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

            <div class="space-y-6">
                {{-- Loop untuk setiap peserta. Karena halaman ini untuk satu user, kita langsung gunakan variabel $user --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $user->email }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Kolom Kiri: Daftar Hasil Kuis --}}
                            <div class="md:col-span-2">
                                <h4 class="text-lg font-medium text-gray-800 mb-2">Hasil Kuis</h4>
                                <div class="space-y-3">
                                    @forelse ($quizzes as $quiz)
                                        @php $attempt = $attempts->where('quiz_id', $quiz->id)->last(); @endphp
                                        <div class="flex justify-between items-center p-3 border rounded-md bg-gray-50">
                                            <span>{{ $quiz->title }}</span>
                                            @if ($attempt)
                                                <a href="{{ route('gradebook.review', $attempt) }}" class="font-semibold text-indigo-600 hover:underline">
                                                    Skor: {{ number_format($attempt->score) }}
                                                </a>
                                            @else
                                                <span class="text-gray-500 text-sm">Belum dikerjakan</span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-gray-500">Tidak ada kuis di kursus ini.</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Kolom Kanan: Form Feedback --}}
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-2">Feedback Keseluruhan</h4>
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
            </div>
             <div class="mt-6">
                <a href="javascript:void(0)" onclick="window.history.back()" class="text-sm text-indigo-600 hover:text-indigo-900">&larr; Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
