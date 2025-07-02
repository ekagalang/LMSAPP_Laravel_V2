<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gradebook: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Peserta
                                    </th>
                                    {{-- Loop untuk membuat kolom header untuk setiap kuis --}}
                                    @foreach ($quizzes as $quiz)
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $quiz->title }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($participants as $participant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $participant->name }}
                                        </td>
                                        {{-- Loop untuk menampilkan skor setiap kuis untuk peserta ini --}}
                                        @foreach ($quizzes as $quiz)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    // Cari percobaan kuis (attempt) terakhir oleh peserta untuk kuis ini
                                                    $attempt = $participant->quizAttempts->where('quiz_id', $quiz->id)->last();
                                                @endphp
                                                {{-- Tampilkan skor jika ada, jika tidak, tampilkan strip --}}
                                                {{ $attempt ? number_format($attempt->score, 2) : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($quizzes) + 1 }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada peserta yang terdaftar di kursus ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
