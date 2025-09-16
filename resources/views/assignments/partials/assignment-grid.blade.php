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
            @if(isset($emptyIcon) && $emptyIcon === 'fas fa-check-circle')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @elseif(isset($emptyIcon) && $emptyIcon === 'fas fa-upload')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            @elseif(isset($emptyIcon) && $emptyIcon === 'fas fa-star')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            @endif
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $emptyMessage ?? 'Belum ada tugas' }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyDescription ?? 'Tugas akan muncul di sini.' }}</p>
    </div>
@endif