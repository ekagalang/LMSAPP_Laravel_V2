<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-slate-800 to-indigo-800 text-white">
            <div class="max-w-6xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Back Button -->
                        <a href="{{ route('courses.show', $quiz->lesson->course) }}" 
                           class="inline-flex items-center p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        
                        <div>
                            <h1 class="text-3xl font-bold">{{ $quiz->title }}</h1>
                            <div class="flex items-center space-x-2 mt-2 text-slate-200">
                                <span class="text-sm">{{ $quiz->lesson?->title }}</span>
                                <span>•</span>
                                <span class="text-sm">{{ $quiz->lesson?->course?->title }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Status Badge -->
                    <div class="flex items-center space-x-3">
                        @if($quiz->status === 'published')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Draft
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        @if (session('success') || session('error'))
            <div class="max-w-6xl mx-auto px-6 py-4">
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong class="font-semibold">Sukses!</strong>
                            <span class="ml-2">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong class="font-semibold">Error!</strong>
                            <span class="ml-2">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-6 py-8">
            <div class="grid lg:grid-cols-3 gap-8">
                
                <!-- Quiz Description Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Deskripsi Kuis</h2>
                                    <p class="text-indigo-100 text-sm">Informasi detail tentang kuis ini</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                            @if($quiz->description)
                                <div class="prose prose-gray max-w-none">
                                    <div class="text-gray-700 leading-relaxed">
                                        {!! $quiz->description !!}
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-500 italic text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>Tidak ada deskripsi untuk kuis ini.</p>
                                </div>
                            @endif

                            <!-- Creator Info -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Dibuat oleh</p>
                                        <p class="font-semibold text-gray-900">{{ $quiz->instructor->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Stats & Actions Sidebar -->
                <div class="space-y-6">
                    <!-- Stats Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-4 text-white">
                            <h3 class="font-bold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Statistik Kuis
                            </h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <!-- Total Questions -->
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Total Soal</span>
                                </div>
                                <span class="font-bold text-lg text-blue-600">{{ $quiz->questions->count() }}</span>
                            </div>

                            <!-- Total Points -->
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Total Nilai</span>
                                </div>
                                <span class="font-bold text-lg text-purple-600">{{ $quiz->total_marks }}</span>
                            </div>

                            <!-- Pass Marks -->
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Nilai Lulus</span>
                                </div>
                                <span class="font-bold text-lg text-green-600">{{ $quiz->pass_marks }}</span>
                            </div>

                            <!-- Time Limit -->
                            @if($quiz->time_limit)
                                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Batas Waktu</span>
                                    </div>
                                    <span class="font-bold text-lg text-orange-600">{{ $quiz->time_limit }} menit</span>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Batas Waktu</span>
                                    </div>
                                    <span class="font-bold text-lg text-gray-600">Tidak ada</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button for Participants -->
                    @auth
                        @can('attempt quizzes')
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                                @if ($quiz->status == 'published')
                                    <div class="text-center mb-4">
                                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-bold text-gray-900 text-lg">Siap Mengerjakan?</h3>
                                        <p class="text-gray-600 text-sm mt-1">Klik tombol di bawah untuk memulai kuis</p>
                                    </div>
                                    
                                    <form action="{{ route('quizzes.start_attempt', $quiz) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ __('Mulai Kuis') }}</span>
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-bold text-gray-900 text-lg mb-2">Kuis Belum Tersedia</h3>
                                        <p class="text-red-600 text-sm">Kuis ini belum dipublikasikan dan tidak dapat dikerjakan.</p>
                                    </div>
                                @endif
                            </div>
                        @endcan
                    @endauth
                </div>
            </div>

            <!-- Questions Preview for Admin/Instructors -->
            @auth
                @can('update quizzes')
                    <div class="mt-12">
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-gray-800 to-slate-700 p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold">Preview Pertanyaan</h3>
                                            <p class="text-gray-300 text-sm">Tampilan khusus untuk instruktur</p>
                                        </div>
                                    </div>
                                    <div class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $quiz->questions->count() }} Pertanyaan
                                    </div>
                                </div>
                            </div>

                            <!-- Questions List -->
                            <div class="p-6">
                                @if ($quiz->questions->isEmpty())
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pertanyaan</h4>
                                        <p class="text-gray-500">Kuis ini belum memiliki pertanyaan. Tambahkan pertanyaan untuk melengkapi kuis.</p>
                                    </div>
                                @else
                                    <div class="space-y-6">
                                        @foreach ($quiz->questions as $question)
                                            <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                                                <!-- Question Header -->
                                                <div class="flex items-start justify-between mb-4">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-sm">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="font-semibold text-gray-900 text-lg leading-relaxed">{{ $question->question_text }}</p>
                                                            <div class="flex items-center space-x-4 mt-2">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                                                </span>
                                                                <span class="text-sm text-gray-500">{{ $question->marks }} poin</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Question Options -->
                                                @if ($question->type === 'multiple_choice')
                                                    <div class="ml-11">
                                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Pilihan Jawaban:</h5>
                                                        <div class="space-y-2">
                                                            @foreach ($question->options as $option)
                                                                <div class="flex items-center p-3 {{ $option->is_correct ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                                                    <div class="w-6 h-6 rounded-full border-2 {{ $option->is_correct ? 'border-green-500 bg-green-500' : 'border-gray-300' }} flex items-center justify-center mr-3 flex-shrink-0">
                                                                        @if($option->is_correct)
                                                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                        @endif
                                                                    </div>
                                                                    <span class="{{ $option->is_correct ? 'text-green-800 font-semibold' : 'text-gray-700' }}">
                                                                        {{ $option->option_text }}
                                                                    </span>
                                                                    @if($option->is_correct)
                                                                        <span class="ml-2 text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Jawaban Benar</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @elseif ($question->type === 'true_false')
                                                    <div class="ml-11">
                                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Jawaban Benar:</h5>
                                                        @php
                                                            $correctTFOption = $question->options->where('is_correct', true)->first();
                                                        @endphp
                                                        <div class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="font-semibold text-green-800">
                                                                {{ $correctTFOption ? ($correctTFOption->option_text === 'True' ? 'True' : 'False') : 'Tidak ditentukan' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endcan
            @endauth
        </div>
    </div>

    <style>
        .prose {
            color: #374151;
        }
        
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #111827;
            font-weight: 600;
        }
        
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .prose ul, .prose ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }
        
        .prose li {
            margin: 0.5rem 0;
        }

        /* Hover animations */
        .hover\:shadow-md:hover {
            transform: translateY(-2px);
        }
        
        /* Button hover effects */
        button:hover {
            transform: translateY(-1px);
        }
        
        /* Card animations */
        .bg-white {
            transition: all 0.3s ease;
        }
    </style>

    <script>
        // Add interactive feedback for question cards
        document.addEventListener('DOMContentLoaded', function() {
            const questionCards = document.querySelectorAll('.border.border-gray-200.rounded-xl');
            
            questionCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.borderColor = '#3B82F6';
                    this.style.transition = 'border-color 0.3s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.borderColor = '#E5E7EB';
                });
            });

            // Add hover effects to stat cards
            const statCards = document.querySelectorAll('.bg-blue-50, .bg-purple-50, .bg-green-50, .bg-orange-50, .bg-gray-50');
            
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</x-app-layout>
