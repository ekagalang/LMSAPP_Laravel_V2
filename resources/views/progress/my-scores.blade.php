<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nilai Saya • {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-500">Ringkasan nilai kuis dan esai Anda pada kursus ini</p>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-gray-900 font-semibold">Rata-rata Kuis</h3>
                            <p class="text-sm text-gray-500">Persentase dari attempt terbaru per kuis</p>
                        </div>
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($quizAverage, 2) }}%</div>
                    </div>
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, max(0, $quizAverage)) }}%"></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-gray-900 font-semibold">Rata-rata Esai</h3>
                            <p class="text-sm text-gray-500">Esai yang memiliki penilaian (skoring)</p>
                        </div>
                        <div class="text-3xl font-bold text-emerald-600">{{ number_format($essayAverage, 2) }}%</div>
                    </div>
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ min(100, max(0, $essayAverage)) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Quiz Scores -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Nilai Kuis</h3>
                        <p class="text-sm text-gray-500">Semua attempt per kuis (terbaru di atas)</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($quizSummaries as $quiz)
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-gray-900 font-semibold truncate">{{ $quiz['title'] }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Rata-rata (attempt terakhir): {{ number_format($quiz['latest_percentage'], 2) }}%</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ count($quiz['attempts']) }} attempt
                                    </span>
                                </div>

                                <div class="mt-3 space-y-2">
                                    @foreach($quiz['attempts'] as $idx => $attempt)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span class="text-gray-400 w-12 shrink-0">#{{ count($quiz['attempts']) - $idx }}</span>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 text-gray-700">
                                                        <span>Skor: {{ $attempt['score'] }}/{{ $attempt['total'] }}</span>
                                                        <span>•</span>
                                                        <span>{{ $attempt['percentage'] }}%</span>
                                                        <span>•</span>
                                                        <span class="font-medium {{ $attempt['passed'] ? 'text-green-600' : 'text-red-600' }}">{{ $attempt['passed'] ? 'Lulus' : 'Tidak Lulus' }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        @if($attempt['completed_at'])
                                                            Selesai: {{ $attempt['completed_at']->format('d M Y, H:i') }}
                                                        @else
                                                            Belum selesai
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('quizzes.result', ['quiz' => $quiz['quiz_id'], 'attempt' => $attempt['attempt_id']]) }}" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg shrink-0">Lihat Hasil</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500">Belum ada attempt kuis</div>
                        @endforelse
                    </div>
                </div>

                <!-- Essay Scores -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Nilai Esai</h3>
                        <p class="text-sm text-gray-500">Status dan skor esai</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($essaySummaries as $essay)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="min-w-0 pr-4">
                                    <p class="text-gray-900 font-medium truncate">{{ $essay['title'] }}</p>
                                    <div class="mt-1 text-sm text-gray-600 flex items-center gap-3">
                                        @if($essay['scoring_enabled'] && $essay['percentage'] !== null)
                                            <span>Skor: {{ $essay['score'] }}/{{ $essay['total'] }}</span>
                                            <span>•</span>
                                            <span>{{ $essay['percentage'] }}%</span>
                                        @else
                                            <span>Tanpa skoring</span>
                                        @endif
                                        <span>•</span>
                                        <span class="font-medium {{ $essay['graded'] ? 'text-green-600' : 'text-yellow-600' }}">{{ $essay['graded'] ? 'Sudah Dinilai' : 'Menunggu Penilaian' }}</span>
                                    </div>
                                    @if($essay['graded_at'])
                                        <p class="text-xs text-gray-400 mt-1">Dinilai: {{ $essay['graded_at']->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('essays.result', ['submission' => $essay['submission_id']]) }}" class="px-3 py-2 text-sm bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg">Lihat Detail</a>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500">Belum ada pengumpulan esai</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
