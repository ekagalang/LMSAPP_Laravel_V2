<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hasil Essay: ') }} {{ $submission->content->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <a href="javascript:void(0)" onclick="window.history.back()"
                       class="text-indigo-600 dark:text-indigo-400 hover:underline mb-6 inline-block">
                        &larr; Kembali
                    </a>

                    {{-- Info Submission --}}
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-lg font-bold mb-2">Informasi Submission</h3>
                        <div class="grid grid-cols-1 md:grid-cols-{{ $submission->content->scoring_enabled ? '3' : '2' }} gap-4 text-sm">
                            <div>
                                <span class="font-medium">Mode Penilaian:</span><br>
                                <span class="text-sm {{ $submission->content->grading_mode === 'overall' ? 'text-purple-600' : 'text-blue-600' }}">
                                    {{ $submission->content->grading_mode === 'overall' ? 'Keseluruhan Essay' : 'Per Soal Individual' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Dikumpulkan:</span><br>
                                {{ $submission->created_at->format('d F Y, H:i') }}
                            </div>
                            <div>
                                <span class="font-medium">Status:</span><br>
                                @php
                                    // Logic status berdasarkan grading mode dan scoring
                                    $isFullyProcessed = false;
                                    
                                    if ($submission->content->scoring_enabled) {
                                        // Dengan scoring
                                        if ($submission->content->grading_mode === 'overall') {
                                            // Overall scoring: cek ada score di answer manapun
                                            $isFullyProcessed = $submission->answers()->whereNotNull('score')->count() > 0;
                                        } else {
                                            // Individual scoring: gunakan is_fully_graded
                                            $isFullyProcessed = $submission->is_fully_graded;
                                        }
                                    } else {
                                        // Tanpa scoring
                                        if ($submission->content->grading_mode === 'overall') {
                                            // Overall tanpa scoring: cek ada feedback di answer manapun
                                            $isFullyProcessed = $submission->answers()->whereNotNull('feedback')->count() > 0;
                                        } else {
                                            // Individual tanpa scoring: semua answer harus ada feedback
                                            $totalQuestions = $submission->content->essayQuestions()->count();
                                            $answersWithFeedback = $submission->answers()->whereNotNull('feedback')->count();
                                            $isFullyProcessed = $answersWithFeedback >= $totalQuestions;
                                        }
                                    }
                                @endphp
                                
                                @if(!($submission->content->requires_review ?? true))
                                    <span class="text-green-600">✅ Latihan Selesai</span>
                                @elseif($submission->content->scoring_enabled)
                                    @if($isFullyProcessed)
                                        <span class="text-green-600">Sudah Dinilai</span>
                                    @else
                                        <span class="text-yellow-600">Menunggu Penilaian</span>
                                    @endif
                                @else
                                    @if($isFullyProcessed)
                                        <span class="text-green-600">Sudah Ditinjau</span>
                                    @else
                                        <span class="text-blue-600">Berhasil Dikumpulkan</span>
                                    @endif
                                @endif
                            </div>
                            @if($submission->content->scoring_enabled)
                                <div>
                                    <span class="font-medium">Total Nilai:</span><br>
                                    @if($isFullyProcessed)
                                        <span class="text-2xl font-bold text-blue-600">
                                            @if($submission->content->grading_mode === 'overall')
                                                {{ $submission->answers()->whereNotNull('score')->first()->score ?? 0 }}/{{ $submission->max_total_score }}
                                            @else
                                                {{ $submission->total_score }}/{{ $submission->max_total_score }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-500">Belum dinilai</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Questions and Answers --}}
                    @foreach($submission->answers as $index => $answer)
                        <div class="mb-8 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-semibold">
                                        @if($answer->question)
                                            Soal {{ $index + 1 }}
                                        @else
                                            Essay Answer
                                        @endif
                                    </h3>
                                    @if($answer->question && $submission->content->scoring_enabled)
                                        <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ $answer->question->max_score }} poin
                                        </span>
                                    @endif
                                </div>
                                @if($answer->question)
                                    <div class="mt-2 text-gray-700 dark:text-gray-300">
                                        {!! nl2br(e($answer->question->question)) !!}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                <div class="mb-6">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                        Jawaban Anda:
                                    </h4>
                                    <div class="prose dark:prose-invert max-w-none p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border">
                                        {!! $answer->answer !!}
                                    </div>
                                </div>

                                {{-- Grading Section - hanya tampil jika scoring enabled --}}
                                @if($submission->content->scoring_enabled)
                                    @php
                                        // Logic untuk menentukan apakah soal ini sudah dinilai
                                        $isGraded = false;
                                        if ($submission->content->grading_mode === 'overall') {
                                            // Overall mode: semua soal dianggap graded jika submission fully graded
                                            $isGraded = $submission->is_fully_graded;
                                        } else {
                                            // Individual mode: cek score per answer
                                            $isGraded = $answer->score !== null;
                                        }
                                    @endphp
                                    
                                    @if($isGraded)
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg">
                                                    <p class="text-sm text-green-800 dark:text-green-200">Nilai:</p>
                                                    <p class="text-3xl font-bold text-green-900 dark:text-green-100">
                                                        @if($submission->content->grading_mode === 'overall')
                                                            @if($loop->first)
                                                                {{ $answer->score }}/{{ $submission->content->essayQuestions->sum('max_score') }}
                                                            @else
                                                                <span class="text-lg">Lihat Total</span>
                                                            @endif
                                                        @else
                                                            {{ $answer->score }}@if($answer->question)/{{ $answer->question->max_score }}@endif
                                                        @endif
                                                    </p>
                                                </div>
                                                
                                                <div class="md:col-span-2 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">
                                                        @if($submission->content->grading_mode === 'overall')
                                                            @if($loop->first)
                                                                Feedback Keseluruhan dari Instruktur:
                                                            @else
                                                                Catatan:
                                                            @endif
                                                        @else
                                                            Feedback dari Instruktur:
                                                        @endif
                                                    </p>
                                                    <p class="mt-2 text-gray-800 dark:text-gray-200">
                                                        @if($submission->content->grading_mode === 'overall')
                                                            @if($loop->first)
                                                                {{ $answer->feedback ?: 'Tidak ada feedback khusus.' }}
                                                            @else
                                                                Essay ini dinilai secara keseluruhan. Lihat feedback pada soal pertama untuk detail penilaian.
                                                            @endif
                                                        @else
                                                            {{ $answer->feedback ?: 'Tidak ada feedback khusus untuk soal ini.' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                                            <p class="text-gray-500 text-center py-4">
                                                Soal ini belum dinilai oleh instruktur.
                                            </p>
                                        </div>
                                    @endif
                                @else
                                    {{-- Essay tanpa scoring - hanya tampilkan feedback jika ada --}}
                                    @php
                                        // Logic untuk essay tanpa scoring
                                        $hasFeedback = false;
                                        if ($submission->content->grading_mode === 'overall') {
                                            // Overall tanpa scoring: cek ada feedback di answer manapun
                                            $hasFeedback = $submission->answers()->whereNotNull('feedback')->count() > 0;
                                        } else {
                                            // Individual tanpa scoring: cek feedback per answer
                                            $hasFeedback = $answer->feedback !== null;
                                        }
                                    @endphp
                                    
                                    @if($hasFeedback)
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                                            <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg">
                                                <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                                                    @if($submission->content->grading_mode === 'overall')
                                                        @if($loop->first)
                                                            Catatan Keseluruhan dari Instruktur:
                                                        @else
                                                            Catatan:
                                                        @endif
                                                    @else
                                                        Catatan dari Instruktur:
                                                    @endif
                                                </p>
                                                <p class="mt-2 text-blue-900 dark:text-blue-100">
                                                    @if($submission->content->grading_mode === 'overall')
                                                        @if($loop->first)
                                                            {{ $answer->feedback ?: 'Tidak ada catatan khusus.' }}
                                                        @else
                                                            Essay ini dinilai secara keseluruhan. Lihat catatan pada soal pertama.
                                                        @endif
                                                    @else
                                                        {{ $answer->feedback }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                                            <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg text-center">
                                                <p class="text-green-800 dark:text-green-200 font-medium">
                                                    Essay berhasil dikumpulkan
                                                </p>
                                                <p class="text-sm text-green-600 dark:text-green-300 mt-1">
                                                    Essay ini tidak memerlukan penilaian
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Overall Grade Summary - hanya tampil jika scoring enabled --}}
                    @if($submission->content->scoring_enabled && $submission->is_fully_graded && $submission->answers->count() > 1)
                        <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                            <h3 class="text-lg font-bold mb-4 text-blue-900 dark:text-blue-100">
                                Ringkasan Nilai
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                @foreach($submission->answers as $index => $answer)
                                    @if($answer->question)
                                        <div class="flex justify-between">
                                            <span>Soal {{ $index + 1 }}:</span>
                                            <span class="font-medium">{{ $answer->score }}/{{ $answer->question->max_score }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="border-t pt-2 mt-2 flex justify-between font-bold text-lg">
                                    <span>Total:</span>
                                    <span class="text-blue-600">{{ $submission->total_score }}/{{ $submission->max_total_score }}</span>
                                </div>
                                <div class="flex justify-between text-blue-700 dark:text-blue-300">
                                    <span>Persentase:</span>
                                    <span>{{ $submission->max_total_score > 0 ? number_format(($submission->total_score / $submission->max_total_score) * 100, 1) : 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>