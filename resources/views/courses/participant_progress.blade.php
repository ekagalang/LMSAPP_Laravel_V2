<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">
                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center">
                        📊 Rincian Progres Belajar
                    </h2>
                    <p class="text-lg font-medium text-indigo-600 mt-1">{{ $participant->name }}</p>
                    <p class="text-sm text-gray-600 flex items-center mt-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Kursus: {{ $course->title }}
                    </p>
                </div>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @php
                $totalContents = $lessons->sum(fn($lesson) => $lesson->contents->count());
                $completedContents = $completedContentsMap->count();
                $progressPercentage = $totalContents > 0 ? round(($completedContents / $totalContents) * 100) : 0;
            @endphp

            <!-- Progress Summary Card -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-8 mb-8 text-white shadow-2xl">
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">{{ $progressPercentage }}%</div>
                        <div class="text-blue-100">Progres Keseluruhan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">{{ $completedContents }}</div>
                        <div class="text-blue-100">Materi Selesai</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">{{ $totalContents }}</div>
                        <div class="text-blue-100">Total Materi</div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Progres Pembelajaran</span>
                        <span>{{ $completedContents }}/{{ $totalContents }} selesai</span>
                    </div>
                    <div class="w-full bg-blue-400 bg-opacity-30 rounded-full h-4">
                        <div class="bg-white h-4 rounded-full transition-all duration-700 ease-out shadow-lg" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Lessons Section -->
            <div class="space-y-6">
                @forelse ($lessons as $lesson)
                    @php
                        $lessonTotalContents = $lesson->contents->count();
                        $lessonCompletedContents = $lesson->contents->filter(fn($content) => $completedContentsMap->has($content->id))->count();
                        $lessonProgress = $lessonTotalContents > 0 ? round(($lessonCompletedContents / $lessonTotalContents) * 100) : 0;
                    @endphp

                    <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-gray-200 hover:shadow-2xl transition-shadow duration-300">
                        <!-- Lesson Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ $loop->iteration }}
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ $lesson->title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $lessonTotalContents }} materi tersedia</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-900">{{ $lessonProgress }}%</div>
                                        <div class="text-sm text-gray-600">{{ $lessonCompletedContents }}/{{ $lessonTotalContents }} selesai</div>
                                    </div>
                                    @if($lessonProgress == 100)
                                        <div class="flex items-center text-green-600">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Lesson Progress Bar -->
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-3 rounded-full transition-all duration-500 ease-out shadow-sm" style="width: {{ $lessonProgress }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Content List -->
                        <div class="p-8">
                            @forelse ($lesson->contents as $content)
                                @php
                                    // ✅ OPTIMASI: Gunakan data yang sudah di-pre-calculate
                                    $statusData = $contentStatusData->get($content->id, [
                                        'status' => 'not_started',
                                        'statusText' => 'Belum Dimulai',
                                        'badgeClass' => 'bg-gray-100 text-gray-800',
                                        'isCompleted' => false
                                    ]);

                                    $contentStatus = $statusData['status'];
                                    $statusText = $statusData['statusText'];
                                    $badgeClass = $statusData['badgeClass'];
                                    $isCompleted = $statusData['isCompleted'];
                                @endphp

                                <div class="flex items-center justify-between p-4 rounded-xl mb-3 last:mb-0 border-2 transition-all duration-200 hover:shadow-md {{ $isCompleted ?
                                    'bg-green-50 border-green-200 hover:bg-green-100' :
                                    ($contentStatus === 'pending_grade' ? 'bg-yellow-50 border-yellow-200 hover:bg-yellow-100' : 'bg-gray-50 border-gray-200 hover:bg-gray-100')
                                }}">

                                    <div class="flex items-center space-x-4">
                                        {{-- Content Type Icon --}}
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $isCompleted ? 'bg-green-100 text-green-600' : ($contentStatus === 'pending_grade' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600') }}">
                                            @switch($content->type)
                                                @case('video') 🎥 @break
                                                @case('document') 📄 @break
                                                @case('quiz') 🧠 @break
                                                @case('essay') ✍️ @break
                                                @default 📝
                                            @endswitch
                                        </div>

                                        {{-- Content Info --}}
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $content->title }}</h4>
                                            <p class="text-sm text-gray-600 capitalize">{{ ucfirst($content->type) }}</p>

                                            {{-- Enhanced Essay Progress Info --}}
                                            @if($content->type === 'essay' && $contentStatus === 'pending_grade')
                                                @php
                                                    // ✅ OPTIMASI: Gunakan data submission yang sudah di-load
                                                    $submission = $essaySubmissionsMap->get($content->id);
                                                    $totalQuestions = $content->essayQuestions->count();
                                                    $gradedAnswers = $submission ? $submission->answers->whereNotNull('score')->count() : 0;
                                                @endphp
                                                <p class="text-xs text-yellow-600 mt-1">
                                                    Dinilai: {{ $gradedAnswers }}/{{ $totalQuestions }} pertanyaan
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Enhanced Status Badge --}}
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                            {{ $statusText }}
                                        </span>

                                        {{-- Action Button --}}
                                        <a href="{{ route('contents.show', $content) }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            @if($contentStatus === 'completed')
                                                Review
                                            @elseif($contentStatus === 'pending_grade')
                                                Lihat Status
                                            @else
                                                Lihat
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Tidak ada konten dalam pelajaran ini.</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-xl p-12 text-center">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Pelajaran</h3>
                        <p class="text-gray-600 text-lg">Kursus ini belum memiliki pelajaran yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            <!-- Achievement Section -->
            @if($progressPercentage == 100)
                <div class="mt-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-8 text-center text-white shadow-2xl">
                    <div class="flex justify-center mb-4">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-2">🎉 Selamat!</h2>
                    <p class="text-xl">{{ $participant->name }} telah menyelesaikan seluruh kursus!</p>
                    <p class="text-yellow-100 mt-2">Semua materi pembelajaran telah berhasil diselesaikan dengan sempurna.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>