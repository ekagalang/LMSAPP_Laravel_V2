<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Tugas') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ $assignments->count() }} tugas tersedia
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Info!</strong>
                    <span class="block sm:inline">{{ session('info') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            <!-- Summary Cards -->
            @if(Auth::check())
                @php
                    $totalAssignments = $assignments->count();
                    $submittedCount = $userSubmissions->where('status', '!=', 'draft')->count();
                    $gradedCount = $userSubmissions->where('status', 'graded')->count();
                    $pendingCount = $totalAssignments - $submittedCount;
                    $overdueTasks = $assignments->filter(function($assignment) use ($userSubmissions) {
                        $submission = $userSubmissions->get($assignment->id);
                        return $assignment->due_date &&
                               $assignment->due_date->isPast() &&
                               (!$submission || $submission->status === 'draft');
                    });
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Tugas -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $totalAssignments }}</h3>
                                    <p class="text-blue-100">Total Tugas</p>
                                </div>
                                <div class="text-blue-200">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dikumpulkan -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $submittedCount }}</h3>
                                    <p class="text-green-100">Dikumpulkan</p>
                                </div>
                                <div class="text-green-200">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dinilai -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $gradedCount }}</h3>
                                    <p class="text-purple-100">Dinilai</p>
                                </div>
                                <div class="text-purple-200">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terlambat -->
                    <div class="bg-gradient-to-r from-red-500 to-red-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $overdueTasks->count() }}</h3>
                                    <p class="text-red-100">Terlambat</p>
                                </div>
                                <div class="text-red-200">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('all')" id="tab-all"
                                class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm active">
                            Semua Tugas
                        </button>
                        <button onclick="showTab('pending')" id="tab-pending"
                                class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Belum Dikumpulkan
                        </button>
                        <button onclick="showTab('submitted')" id="tab-submitted"
                                class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Sudah Dikumpulkan
                        </button>
                        <button onclick="showTab('graded')" id="tab-graded"
                                class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Sudah Dinilai
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- All Assignments -->
                    <div id="content-all" class="tab-content">
                        @if($assignments->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($assignments as $assignment)
                                    @php
                                        $userSubmission = Auth::check() ? $userSubmissions->get($assignment->id) : null;
                                        $isOverdue = $assignment->due_date && $assignment->due_date->isPast() && (!$userSubmission || $userSubmission->status === 'draft');
                                        $isDueSoon = $assignment->due_date && !$assignment->due_date->isPast() && $assignment->due_date->diffInHours() <= 24;
                                    @endphp
                                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 {{ $isOverdue ? 'ring-2 ring-red-500' : ($isDueSoon ? 'ring-2 ring-yellow-500' : '') }}">
                                        <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $assignment->title }}</h3>
                                                <div class="flex items-center space-x-1">
                                                    @if($assignment->submission_type === 'file')
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    @elseif($assignment->submission_type === 'link')
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-4 space-y-3">
                                            @if($assignment->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($assignment->description, 100) }}</p>
                                            @endif

                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Poin:</span>
                                                    <div class="font-semibold text-green-600 dark:text-green-400">{{ $assignment->max_points }}</div>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Pembuat:</span>
                                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->creator->name }}</div>
                                                </div>
                                            </div>

                                            @if($assignment->due_date)
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Tenggat:</span>
                                                    <div class="font-semibold {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isDueSoon ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400') }}">
                                                        {{ $assignment->due_date->format('d M Y H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment->due_date->diffForHumans() }}</div>
                                                </div>
                                            @endif

                                            @if($userSubmission)
                                                <div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                                        @if($userSubmission->status === 'graded')
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                Dinilai
                                                            </span>
                                                        @elseif($userSubmission->status === 'submitted')
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                Dikumpulkan
                                                            </span>
                                                        @elseif($userSubmission->status === 'returned')
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Dikembalikan
                                                            </span>
                                                        @else
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                                Draft
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($userSubmission->points_earned !== null)
                                                        <div class="mt-2">
                                                            <div class="flex justify-between text-sm">
                                                                <span>Nilai:</span>
                                                                <span class="font-semibold text-green-600 dark:text-green-400">{{ $userSubmission->points_earned }}/{{ $assignment->max_points }}</span>
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $userSubmission->grade }}%"></div>
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($userSubmission->grade, 1) }}%</div>
                                                        </div>
                                                    @endif

                                                    @if($userSubmission->submitted_at)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            Dikumpulkan: {{ $userSubmission->submitted_at->format('d M Y H:i') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-4 border-t border-gray-200 dark:border-gray-600 space-y-2">
                                            <a href="{{ route('assignments.show', $assignment) }}"
                                               class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat Detail
                                            </a>

                                            @if($userSubmission)
                                                <a href="{{ route('assignments.submissions.show', [$assignment, $userSubmission]) }}"
                                                   class="w-full inline-flex justify-center items-center px-3 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-700 dark:hover:bg-blue-800">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Lihat Pengumpulan
                                                </a>

                                                @if($userSubmission->canEdit())
                                                    @if($assignment->canSubmit())
                                                        <a href="{{ route('assignments.submissions.create', $assignment) }}"
                                                           class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit Pengumpulan
                                                        </a>
                                                    @endif
                                                @endif
                                            @else
                                                @if($assignment->canSubmit())
                                                    <a href="{{ route('assignments.submissions.create', $assignment) }}"
                                                       class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                        </svg>
                                                        Kumpulkan
                                                    </a>
                                                @else
                                                    <button disabled
                                                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-500 bg-gray-100 cursor-not-allowed dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                        </svg>
                                                        Ditutup
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada tugas</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tugas akan muncul di sini setelah pengajar membuatnya.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pending Assignments -->
                    <div id="content-pending" class="tab-content hidden">
                        @php
                            $pendingAssignments = $assignments->filter(function($assignment) use ($userSubmissions) {
                                $submission = $userSubmissions->get($assignment->id);
                                return !$submission || $submission->status === 'draft';
                            });
                        @endphp

                        @if($pendingAssignments->count() > 0)
                            @include('assignments.partials.assignment-grid', [
                                'assignments' => $pendingAssignments,
                                'userSubmissions' => $userSubmissions,
                                'emptyMessage' => 'Semua tugas sudah dikumpulkan!',
                                'emptyDescription' => 'Tidak ada tugas yang perlu dikumpulkan.',
                                'emptyIcon' => 'fas fa-check-circle'
                            ])
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-green-900 dark:text-green-100">Semua tugas sudah dikumpulkan!</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada tugas yang perlu dikumpulkan.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Submitted Assignments -->
                    <div id="content-submitted" class="tab-content hidden">
                        @php
                            $submittedAssignments = $assignments->filter(function($assignment) use ($userSubmissions) {
                                $submission = $userSubmissions->get($assignment->id);
                                return $submission && in_array($submission->status, ['submitted', 'graded', 'returned']);
                            });
                        @endphp

                        @include('assignments.partials.assignment-grid', [
                            'assignments' => $submittedAssignments,
                            'userSubmissions' => $userSubmissions,
                            'emptyMessage' => 'Belum ada tugas yang dikumpulkan',
                            'emptyIcon' => 'fas fa-upload'
                        ])
                    </div>

                    <!-- Graded Assignments -->
                    <div id="content-graded" class="tab-content hidden">
                        @php
                            $gradedAssignments = $assignments->filter(function($assignment) use ($userSubmissions) {
                                $submission = $userSubmissions->get($assignment->id);
                                return $submission && $submission->status === 'graded';
                            });
                        @endphp

                        @include('assignments.partials.assignment-grid', [
                            'assignments' => $gradedAssignments,
                            'userSubmissions' => $userSubmissions,
                            'emptyMessage' => 'Belum ada tugas yang dinilai',
                            'emptyIcon' => 'fas fa-star'
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
        }

        // Auto-refresh every 5 minutes to update due dates
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 300000);
    </script>
    @endpush

    @push('styles')
    <style>
        .tab-button.active {
            border-color: #3b82f6 !important;
            color: #2563eb !important;
        }
    </style>
    @endpush
</x-app-layout>