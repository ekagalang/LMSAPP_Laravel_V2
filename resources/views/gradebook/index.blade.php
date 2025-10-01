<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                            Buku Nilai
                        </h2>
                        <p class="text-indigo-600 font-medium text-sm">{{ $course->title }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 max-w-2xl">
                    Fokus pada penilaian esai dan pemberian feedback komprehensif. Untuk melihat nilai quiz, gunakan menu "Lihat Nilai" di halaman kursus.
                </p>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm" role="alert">
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
            
            {{-- Enhanced Search & Filter Section --}}
            <div class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <label for="course_filter" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                            </svg>
                            Pindah ke Gradebook Kursus Lain
                        </label>
                        <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm transition-all duration-200">
                            @foreach ($allCoursesForFilter as $filterCourse)
                                <option value="{{ route('courses.gradebook', $filterCourse) }}" @selected($filterCourse->id == $course->id)>
                                    {{ $filterCourse->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-4">
                        <form action="{{ route('courses.gradebook', $course) }}" method="GET" class="space-y-2">
                            <label for="search" class="flex items-center text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari Peserta di Kursus Ini
                            </label>
                            <div class="flex space-x-3">
                                <div class="relative flex-1">
                                    <x-text-input type="text" name="search" id="search" class="w-full pl-4 pr-4 py-3 rounded-lg shadow-sm" placeholder="Masukkan nama atau email peserta..." value="{{ request('search') }}" />
                                </div>
                                <x-primary-button class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Cari
                                </x-primary-button>
                                @if(request('search'))
                                    <a href="{{ route('courses.gradebook', $course) }}" class="inline-flex items-center px-4 py-3 text-sm text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div x-data="{ activeTab: 'essays' }" class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-200">
                {{-- Enhanced Tab Navigation --}}
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex px-6" aria-label="Tabs">
                        <button @click="activeTab = 'essays'" :class="{'border-indigo-500 text-indigo-600 bg-white': activeTab === 'essays', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'essays'}" class="relative whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all duration-200 rounded-t-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Penilaian Esai</span>
                            </div>
                        </button>
                        <button @click="activeTab = 'feedback'" :class="{'border-indigo-500 text-indigo-600 bg-white': activeTab === 'feedback', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'feedback'}" class="relative whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all duration-200 rounded-t-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Feedback Umum</span>
                            </div>
                        </button>
                    </nav>
                </div>

                {{-- Enhanced Essays Tab Content --}}
                <div x-show="activeTab === 'essays'" x-transition>
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-2xl font-bold text-gray-900">Daftar Peserta</h3>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                                    {{ $participantsWithEssays->count() }} Peserta
                                </span>
                            </div>
                        </div>
                        
                        @if($participantsWithEssays->count() > 0)
                            <div class="space-y-6">
                                @foreach($participantsWithEssays as $participant)
                                    @php
                                        $submissions = $participant->essaySubmissions()
                                            ->whereIn('content_id', $essayContentIds)
                                            ->with(['content.essayQuestions', 'answers'])
                                            ->get();
                                    @endphp
                                    
                                    @if($submissions->isNotEmpty())
                                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                            {{-- Header participant --}}
                                            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($participant->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-xl font-bold text-white">{{ $participant->name }}</h3>
                                                            <p class="text-blue-100 text-sm">{{ $participant->email }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right text-white">
                                                        <div class="text-sm opacity-75">Total Essays</div>
                                                        <div class="text-2xl font-bold">{{ $submissions->count() }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- Essay submissions --}}
                                            <div class="p-6">
                                                <div class="space-y-4">
                                                    @foreach($submissions as $submission)
                                                        @php
                                                            $questions = $submission->content->essayQuestions;
                                                            $totalQuestions = $questions->count();
                                                            $scoringEnabled = $submission->content->scoring_enabled ?? true;
                                                            $gradingMode = $submission->content->grading_mode ?? 'individual';
                                                            
                                                            // ✅ PERBAIKAN: Logic completion yang benar untuk semua mode
                                                            if ($gradingMode === 'overall') {
                                                                if ($scoringEnabled) {
                                                                    // Overall + Scoring: Cek answer pertama punya score
                                                                    $firstAnswer = $submission->answers()->first();
                                                                    $isFullyGraded = $firstAnswer && $firstAnswer->score !== null;
                                                                    $submissionScore = $firstAnswer ? $firstAnswer->score : 0;
                                                                    $submissionMaxScore = $questions->sum('max_score') ?: 100;
                                                                } else {
                                                                    // Overall + Feedback: Cek answer pertama punya feedback
                                                                    $firstAnswer = $submission->answers()->first();
                                                                    $isFullyGraded = $firstAnswer && !empty($firstAnswer->feedback);
                                                                    $submissionScore = 0;
                                                                    $submissionMaxScore = 0;
                                                                }
                                                            } else {
                                                                // Individual mode (logic lama)
                                                                if ($totalQuestions > 0) {
                                                                    if ($scoringEnabled) {
                                                                        $gradedAnswers = $submission->answers()->whereNotNull('score')->count();
                                                                        $isFullyGraded = $gradedAnswers >= $totalQuestions;
                                                                        $submissionScore = $submission->answers()->sum('score');
                                                                        $submissionMaxScore = $questions->sum('max_score');
                                                                    } else {
                                                                        $feedbackAnswers = $submission->answers()->whereNotNull('feedback')->count();
                                                                        $isFullyGraded = $feedbackAnswers >= $totalQuestions;
                                                                        $submissionScore = 0;
                                                                        $submissionMaxScore = 0;
                                                                    }
                                                                } else {
                                                                    // Legacy essay
                                                                    if ($scoringEnabled) {
                                                                        $hasScore = $submission->answers()->whereNotNull('score')->count() > 0;
                                                                        $isFullyGraded = $hasScore;
                                                                        $submissionScore = $submission->answers()->first()?->score ?? 0;
                                                                        $submissionMaxScore = 100;
                                                                    } else {
                                                                        $hasFeedback = $submission->answers()->whereNotNull('feedback')->count() > 0;
                                                                        $isFullyGraded = $hasFeedback;
                                                                        $submissionScore = 0;
                                                                        $submissionMaxScore = 0;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        
                                                        <div class="flex items-center justify-between p-5 {{ $isFullyGraded ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-lg transition-all duration-200 hover:shadow-sm">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-3 mb-2">
                                                                    <h4 class="font-medium text-gray-900">{{ $submission->content->title }}</h4>
                                                                    @if($isFullyGraded)
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                            ✅ Complete
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                            ⏳ Needs {{ $scoringEnabled ? 'Grading' : 'Feedback' }}
                                                                        </span>
                                                                    @endif
                                                                    
                                                                    {{-- Mode indicator --}}
                                                                    @if($gradingMode === 'overall')
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                                            📊 Overall
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                            📝 Individual
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="flex items-center space-x-6 text-sm">
                                                                    <span class="text-gray-600">
                                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                        </svg>
                                                                        Submitted: {{ $submission->created_at->format('d M Y') }}
                                                                    </span>
                                                                    
                                                                    @if($scoringEnabled && $isFullyGraded)
                                                                        <span class="text-gray-700 font-medium">
                                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                            </svg>
                                                                            Score: {{ $submissionScore }}/{{ $submissionMaxScore }}
                                                                            @if($submissionMaxScore > 0)
                                                                                ({{ round(($submissionScore / $submissionMaxScore) * 100, 1) }}%)
                                                                            @endif
                                                                        </span>
                                                                    @elseif(!$scoringEnabled && $isFullyGraded)
                                                                        <span class="text-blue-600 font-medium">
                                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                                            </svg>
                                                                            Feedback Given
                                                                        </span>
                                                                    @endif
                                                                    
                                                                    @if($gradingMode === 'individual' && $totalQuestions > 0)
                                                                        <span class="text-{{ $isFullyGraded ? 'green' : 'yellow' }}-600 font-medium">
                                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                            </svg>
                                                                            Questions: {{ $scoringEnabled ? $submission->answers()->whereNotNull('score')->count() : $submission->answers()->whereNotNull('feedback')->count() }}/{{ $totalQuestions }} {{ $scoringEnabled ? 'graded' : 'feedback' }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="flex items-center space-x-3">
                                                                {{-- Action Button --}}
                                                                <a href="{{ route('gradebook.essay-detail', $submission) }}" 
                                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm hover:shadow">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                    <span>
                                                                        @if($isFullyGraded)
                                                                            Review {{ $gradingMode === 'overall' ? 'Overall ' : '' }}{{ $scoringEnabled ? 'Grade' : 'Feedback' }}
                                                                        @else
                                                                            {{ $gradingMode === 'overall' ? 'Grade Overall' : 'Grade Questions' }}
                                                                        @endif
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">No Essay Submissions</h4>
                                <p class="text-gray-500 max-w-md mx-auto">No students have submitted essays yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Grade Modal for single question essays --}}
                <div id="quickGradeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Grade Essay</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    <span id="studentName"></span> - <span id="essayTitle"></span>
                                </p>
                                
                                <form id="quickGradeForm" action="" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="score" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nilai (0-100)
                                        </label>
                                        <input type="number" 
                                               id="score" 
                                               name="score" 
                                               min="0" 
                                               max="100" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               required>
                                    </div>
                                    
                                    <div class="mb-6">
                                        <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                                            Feedback (opsional)
                                        </label>
                                        <textarea id="feedback" 
                                                  name="feedback" 
                                                  rows="3" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Berikan feedback untuk mahasiswa..."></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" 
                                                onclick="closeQuickGradeModal()" 
                                                class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" 
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                            Simpan Nilai
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enhanced Feedback Tab Content --}}
                <div x-show="activeTab === 'feedback'" x-cloak class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-2xl font-bold text-gray-900">Feedback Umum</h3>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                {{ $participants->count() }} Peserta
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse ($participants as $participant)
                            <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                                <button @click="open = !open" class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 rounded-xl transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-sm">
                                            <span class="font-bold text-white text-sm">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $participant->name }}</span>
                                            <p class="text-sm text-gray-500">{{ $participant->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($participant->feedback->first()?->feedback)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                Ada Feedback
                                            </span>
                                        @endif
                                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </button>
                                <div x-show="open" x-collapse class="px-6 pb-6">
                                    <div class="border-t border-gray-100 pt-4">
                                        <form action="{{ route('gradebook.storeFeedback', ['course' => $course, 'user' => $participant]) }}" method="POST" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Feedback untuk {{ $participant->name }}
                                                </label>
                                                <textarea 
                                                    name="feedback" 
                                                    rows="4" 
                                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 resize-none" 
                                                    placeholder="Berikan feedback konstruktif untuk {{ $participant->name }}. Misalnya: poin kuat, area yang perlu diperbaiki, saran pengembangan..."
                                                >{{ $participant->feedback->first()->feedback ?? '' }}</textarea>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Feedback akan tersimpan otomatis</span>
                                                </div>
                                                <x-primary-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Simpan Feedback
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Peserta</h4>
                                <p class="text-gray-500 max-w-md mx-auto">Tidak ada peserta yang cocok dengan pencarian Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Quick Grade Modal --}}
    <script>
    function openQuickGradeModal(submissionId, essayTitle, studentName) {
        document.getElementById('quickGradeModal').classList.remove('hidden');
        document.getElementById('studentName').textContent = studentName;
        document.getElementById('essayTitle').textContent = essayTitle;
        
        // Set form action URL
        const form = document.getElementById('quickGradeForm');
        form.action = `/essay-submissions/${submissionId}/grade`;
        
        // Reset form
        form.reset();
        
        // Focus on score input
        document.getElementById('score').focus();
    }

    function closeQuickGradeModal() {
        document.getElementById('quickGradeModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('quickGradeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuickGradeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQuickGradeModal();
        }
    });

    // Handle form submission
    document.getElementById('quickGradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        // Show loading state
        submitButton.textContent = 'Menyimpan...';
        submitButton.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg';
                alertDiv.textContent = data.message || 'Nilai berhasil disimpan!';
                
                const container = document.querySelector('.max-w-7xl.mx-auto.sm\\:px-6.lg\\:px-8');
                container.insertBefore(alertDiv, container.firstChild);
                
                // Close modal
                closeQuickGradeModal();
                
                // Refresh page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg';
            alertDiv.textContent = 'Error: ' + error.message;
            
            const container = document.querySelector('.max-w-7xl.mx-auto.sm\\:px-6.lg\\:px-8');
            container.insertBefore(alertDiv, container.firstChild);
        })
        .finally(() => {
            // Reset button state
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    });
    </script>
</x-app-layout>
