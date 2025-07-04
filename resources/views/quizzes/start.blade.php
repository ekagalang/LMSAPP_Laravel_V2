<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mulai Kuis: ') }} {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Instruksi Kuis</h3>
                    <div class="prose mt-2 max-w-none">
                        {!! $quiz->description !!}
                    </div>

                    <ul class="mt-4 space-y-2">
                        <li><strong>Total Pertanyaan:</strong> {{ $quiz->questions->count() }}</li>
                        <li><strong>Total Poin:</strong> {{ $quiz->total_marks }}</li>
                        <li><strong>Poin untuk Lulus:</strong> {{ $quiz->pass_marks }}</li>
                        <li><strong>Batas Waktu:</strong> {{ $quiz->time_limit }} menit</li>
                    </ul>

                    <div class="mt-6">
                        <form action="{{ route('quizzes.attempt', $quiz) }}" method="GET">
                            <x-primary-button>
                                {{ __('Mulai Kerjakan Sekarang') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>