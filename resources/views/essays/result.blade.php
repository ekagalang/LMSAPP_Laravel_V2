<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hasil Esai: ') }} {{ $submission->content->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <a href="{{ route('contents.show', $submission->content->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline mb-6 inline-block">&larr; Kembali ke Materi</a>

                    <div class="mb-8">
                        <h3 class="text-lg font-bold mb-2">Pertanyaan Esai:</h3>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $submission->content->description !!}
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-bold mb-2">Jawaban Anda:</h3>
                        <div class="prose dark:prose-invert max-w-none p-4 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700">
                            {!! $submission->answer !!}
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-bold mb-4">Hasil Penilaian</h3>
                        @if($submission->graded_at)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg">
                                    <p class="text-sm text-blue-800 dark:text-blue-200">Nilai:</p>
                                    <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $submission->score }} / 100</p>
                                </div>
                                <div class="md:col-span-2 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Feedback dari Instruktur:</p>
                                    <p class="mt-2 text-gray-800 dark:text-gray-200">{{ $submission->feedback ?? 'Tidak ada feedback.' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500">Esai Anda sudah dikumpulkan dan sedang menunggu penilaian dari instruktur.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
