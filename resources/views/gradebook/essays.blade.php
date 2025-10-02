<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Penilaian Esai: {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Tinjau dan berikan nilai untuk jawaban esai yang masuk.
                </p>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($submissions->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-gray-500">Tidak ada jawaban esai baru yang perlu dinilai saat ini.</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach ($submissions as $submission)
                                <div class="border rounded-lg p-6 bg-gray-50/50" id="submission-{{ $submission->id }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">{{ $submission->user->name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $submission->user->email }}</p>
                                            <p class="text-sm text-gray-700 mt-2">
                                                Menjawab esai: <span class="font-semibold">{{ $submission->content->title }}</span>
                                            </p>
                                        </div>
                                        <p class="text-xs text-gray-400">Dikirim: {{ $submission->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="font-semibold text-gray-600 mb-2">Jawaban Peserta:</h4>
                                        <div class="prose max-w-none p-4 bg-white rounded-md border">
                                            {!! $submission->answer !!}
                                        </div>
                                    </div>

                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="font-semibold text-gray-600 mb-2">Form Penilaian:</h4>
                                        <form action="{{ route('gradebook.storeEssayGrade', $submission) }}" method="POST">
                                            @csrf
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div class="md:col-span-3">
                                                    <label for="feedback-{{ $submission->id }}" class="sr-only">Feedback</label>
                                                    <textarea name="feedback" id="feedback-{{ $submission->id }}" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Tulis feedback untuk peserta..."></textarea>
                                                </div>
                                                <div class="space-y-2">
                                                    <label for="score-{{ $submission->id }}" class="sr-only">Nilai</label>
                                                    <input type="number" name="score" id="score-{{ $submission->id }}" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Nilai (0-100)" min="0" max="100" required>
                                                    <x-primary-button type="submit" class="w-full justify-center">Simpan Nilai</x-primary-button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>