<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Penilaian Esai untuk: {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kursus: {{ $course->title }}</p>
            </div>
            <a href="{{ route('courses.gradebook', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                &larr; Kembali ke Daftar Peserta
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="space-y-8">
                @forelse ($submissions as $submission)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-gray-800">Esai: {{ $submission->content->title }}</h3>
                            @if($submission->graded_at)
                                <span class="text-xs font-semibold bg-green-100 text-green-800 px-2 py-1 rounded-full">Sudah Dinilai</span>
                            @else
                                <span class="text-xs font-semibold bg-red-100 text-red-800 px-2 py-1 rounded-full">Perlu Dinilai</span>
                            @endif
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <h4 class="font-semibold text-gray-600 mb-2">Jawaban:</h4>
                            <div class="prose max-w-none p-4 bg-gray-50 rounded-md border">{!! $submission->answer !!}</div>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            @if($submission->graded_at)
                                <h4 class="font-semibold text-gray-600 mb-2">Hasil Penilaian:</h4>
                                <p><strong>Nilai:</strong> {{ $submission->score }}</p>
                                <p><strong>Feedback:</strong> {{ $submission->feedback ?? '-' }}</p>
                            @else
                                <h4 class="font-semibold text-gray-600 mb-2">Form Penilaian:</h4>
                                <form action="{{ route('gradebook.storeEssayGrade', $submission) }}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div class="md:col-span-3">
                                            <textarea name="feedback" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Tulis feedback..."></textarea>
                                        </div>
                                        <div class="space-y-2">
                                            <input type="number" name="score" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Nilai (0-100)" min="0" max="100" required>
                                            <x-primary-button type="submit" class="w-full justify-center">Simpan Nilai</x-primary-button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white text-center py-10 rounded-lg shadow-sm">
                        <p class="text-gray-500">Peserta ini belum mengumpulkan jawaban esai apapun.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>