<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-start space-x-4">
                <!-- User Avatar -->
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="font-bold text-white text-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        {{ $user->name }}
                    </h2>
                    <div class="flex items-center space-x-3 mt-1">
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                        <p class="text-sm text-indigo-600 font-medium">{{ $course->title }}</p>
                    </div>
                    <div class="flex items-center mt-2 space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs text-gray-500">Penilaian Esai</span>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-5 py-3 bg-white border border-gray-300 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 hover:shadow-lg transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Statistics Summary -->
            @if($submissions->count() > 0)
                @php
                    // Filter hanya submissions dengan scoring enabled untuk statistik
                    $scoringEnabledSubmissions = $submissions->filter(function($submission) {
                        return $submission->content->scoring_enabled ?? true;
                    });
                    $gradedCount = $scoringEnabledSubmissions->where('graded_at', '!=', null)->count();
                    $needsGradingCount = $scoringEnabledSubmissions->where('graded_at', null)->count();
                    $noScoringCount = $submissions->count() - $scoringEnabledSubmissions->count();
                @endphp
                
                <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600">Total Esai</p>
                                <p class="text-3xl font-bold text-blue-900">{{ $submissions->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-600">Sudah Dinilai</p>
                                <p class="text-3xl font-bold text-green-900">{{ $gradedCount }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 border border-orange-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-orange-600">Perlu Dinilai</p>
                                <p class="text-3xl font-bold text-orange-900">{{ $needsGradingCount }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Tanpa Penilaian</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $noScoringCount }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-8">
                @forelse ($submissions as $index => $submission)
                    @php
                        $isGraded = $submission->graded_at !== null;
                        $scoringEnabled = $submission->content->scoring_enabled ?? true;
                    @endphp
                    
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <!-- Essay Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
                            <div class="flex flex-wrap justify-between items-start gap-4">
                                <div class="flex items-start space-x-4">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <span class="font-bold text-indigo-600 text-sm">{{ $index + 1 }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $submission->content->title }}</h3>
                                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Dikumpulkan {{ $submission->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    @if(!$scoringEnabled)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Tanpa Penilaian
                                        </span>
                                    @elseif($isGraded)
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Sudah Dinilai
                                            </span>
                                            <div class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                                Nilai: {{ $submission->score }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Perlu Dinilai
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Essay Content -->
                        <div class="p-6">
                            <div class="mb-6">
                                <div class="flex items-center mb-3">
                                    <h4 class="font-semibold text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Jawaban Peserta
                                    </h4>
                                </div>
                                
                                {{-- Handle multiple answers atau single answer --}}
                                @if($submission->answers && $submission->answers->count() > 0)
                                    {{-- New system: Multiple questions --}}
                                    @foreach($submission->answers as $answerIndex => $answer)
                                        <div class="mb-6">
                                            @if($answer->question)
                                                <h5 class="font-medium text-gray-700 mb-2">Pertanyaan {{ $answerIndex + 1 }}: {{ $answer->question->question }}</h5>
                                            @endif
                                            <div class="prose prose-sm max-w-none p-6 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 shadow-sm">
                                                {!! nl2br(e($answer->answer)) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Old system: Single answer --}}
                                    <div class="prose prose-sm max-w-none p-6 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 shadow-sm">
                                        {!! $submission->answer !!}
                                    </div>
                                @endif
                            </div>

                            <!-- Grading Section -->
                            @if($scoringEnabled)
                                <div class="border-t border-gray-200 pt-6">
                                    @if($isGraded)
                                        <!-- Existing Grade Display -->
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                                            <div class="flex items-center mb-4">
                                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-green-900">Hasil Penilaian</h4>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-green-200">
                                                        <span class="font-medium text-gray-700">Nilai</span>
                                                        <span class="text-2xl font-bold text-green-600">{{ $submission->score }}/100</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-green-200">
                                                        <span class="font-medium text-gray-700">Dinilai pada</span>
                                                        <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($submission->graded_at)->format('d M Y, H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($submission->feedback)
                                                <div class="mt-4">
                                                    <label class="block text-sm font-medium text-green-800 mb-2">Feedback:</label>
                                                    <div class="p-4 bg-white rounded-lg border border-green-200">
                                                        <p class="text-gray-700 leading-relaxed">{{ $submission->feedback }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Grading Form -->
                                        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border border-orange-200">
                                            <div class="flex items-center mb-6">
                                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-orange-900">Form Penilaian</h4>
                                            </div>
                                            
                                            <form action="{{ route('gradebook.storeEssayGrade', $submission) }}" method="POST" class="space-y-6">
                                                @csrf
                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                                    <!-- Feedback Section -->
                                                    <div class="lg:col-span-2">
                                                        <label for="feedback_{{ $submission->id }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                                            Feedback & Komentar
                                                        </label>
                                                        <textarea 
                                                            name="feedback" 
                                                            id="feedback_{{ $submission->id }}"
                                                            rows="4" 
                                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all duration-200" 
                                                            placeholder="Berikan feedback konstruktif: poin kuat, area yang perlu diperbaiki, saran pengembangan..."></textarea>
                                                        <p class="text-xs text-gray-500 mt-1">Opsional - berikan masukan untuk membantu peserta berkembang</p>
                                                    </div>
                                                    
                                                    <!-- Score Section -->
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label for="score_{{ $submission->id }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Nilai (0-100)
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                name="score" 
                                                                id="score_{{ $submission->id }}"
                                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center text-lg font-semibold transition-all duration-200" 
                                                                placeholder="75" 
                                                                min="0" 
                                                                max="100" 
                                                                required>
                                                            <p class="text-xs text-gray-500 mt-1">Masukkan nilai dari 0 hingga 100</p>
                                                        </div>
                                                        
                                                        <x-primary-button type="submit" class="w-full justify-center py-3 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 font-semibold">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Simpan Penilaian
                                                        </x-primary-button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        @include('gradebook.partials.essay-grading-forms', ['submission' => $submission])
                                    @endif
                                </div>
                            @else
                                <div class="border-t border-gray-200 pt-6">
                                    @if($submission->status === 'reviewed')
                                        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                                            <h4 class="font-bold text-blue-900 mb-2">Feedback Telah Diberikan</h4>
                                            <div class="bg-white p-4 rounded-lg">
                                                <p class="text-gray-700">{{ $submission->answers->first()->feedback ?? 'Tidak ada feedback.' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        @include('gradebook.partials.essay-grading-forms', ['submission' => $submission])
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Enhanced Empty State -->
                    <div class="bg-white text-center py-20 rounded-2xl shadow-sm border border-gray-200">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Esai</h3>
                        <p class="text-gray-500 max-w-md mx-auto mb-6">
                            {{ $user->name }} belum mengumpulkan jawaban esai apapun untuk kursus ini.
                        </p>
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Esai akan muncul di sini setelah peserta mengumpulkan jawaban</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>