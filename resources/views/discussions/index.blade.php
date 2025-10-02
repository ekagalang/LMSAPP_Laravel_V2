<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center">
                        💬 Manajemen Diskusi
                    </h2>
                    <p class="text-lg font-medium text-indigo-600 mt-1">{{ $course->title }}</p>
                    <p class="text-sm text-gray-600 mt-1">
                        Tinjau dan balas semua diskusi yang ada di kursus ini dari satu tempat
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (!$discussions->isEmpty())
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-white bg-opacity-20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-blue-100">Total Diskusi</p>
                                <p class="text-2xl font-bold">{{ $discussions->total() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-white bg-opacity-20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-green-100">Total Balasan</p>
                                <p class="text-2xl font-bold">{{ $discussions->sum('replies_count') ?? $discussions->sum(fn($d) => $d->replies->count()) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-white bg-opacity-20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-purple-100">Peserta Aktif</p>
                                <p class="text-2xl font-bold">{{ $discussions->pluck('user_id')->unique()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                <div class="p-6 lg:p-8">
                    @if ($discussions->isEmpty())
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="flex justify-center mb-6">
                                <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Diskusi</h3>
                            <p class="text-gray-600 text-lg mb-6">Belum ada diskusi yang dimulai di kursus ini.</p>
                            <p class="text-sm text-gray-500">Peserta dapat memulai diskusi pada setiap materi pembelajaran.</p>
                        </div>
                    @else
                        <!-- Discussion List -->
                        <div class="space-y-6">
                            @foreach ($discussions as $discussion)
                                <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border-2 border-gray-200 hover:border-indigo-300 hover:shadow-lg transition-all duration-300 overflow-hidden" x-data="{ showReplies: false }">
                                    <!-- Discussion Header -->
                                    <div class="p-6">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-grow">
                                                <!-- User Avatar and Info -->
                                                <div class="flex items-center space-x-4 mb-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                                                            <span class="text-white font-bold text-lg">
                                                                {{ strtoupper(substr($discussion->user->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">{{ $discussion->user->name }}</p>
                                                        <p class="text-sm text-gray-600 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            {{ $discussion->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Discussion Title and Content -->
                                                <a href="{{ route('contents.show', $discussion->content_id) }}#discussion-{{ $discussion->id }}" class="block group">
                                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200 mb-2">
                                                        {{ $discussion->title }}
                                                    </h3>
                                                </a>
                                                
                                                <!-- Content Info Badge -->
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mb-3">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    Materi: {{ $discussion->content->title }}
                                                </div>

                                                <!-- Discussion Body -->
                                                <div class="prose prose-gray max-w-none">
                                                    <p class="text-gray-700 leading-relaxed">{{ Str::limit($discussion->body, 250) }}</p>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex-shrink-0 ml-6 space-y-2">
                                                <!-- Reply Count Badge -->
                                                <div class="text-center">
                                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                                        </svg>
                                                        {{ $discussion->replies->count() }} balasan
                                                    </div>
                                                </div>
                                                
                                                <!-- Main Action Button -->
                                                <button @click="showReplies = !showReplies" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showReplies">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showReplies">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <span x-text="showReplies ? 'Tutup Form' : 'Balas Diskusi'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reply Management Section -->
                                    <div x-show="showReplies" x-collapse class="border-t border-gray-200 bg-gray-50">
                                        <div class="p-6">
                                            <!-- Existing Replies -->
                                            @if($discussion->replies->count() > 0)
                                                <div class="space-y-4 mb-6">
                                                    <h6 class="text-md font-semibold text-gray-800 flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                        </svg>
                                                        Balasan Sebelumnya ({{ $discussion->replies->count() }})
                                                    </h6>
                                                    
                                                    {{-- Tampilkan balasan yang ada --}}
                                                    <div class="space-y-3">
                                                        @foreach($discussion->replies as $reply)
                                                            <div class="flex items-start space-x-3">
                                                                <div class="flex-shrink-0">
                                                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center font-bold text-gray-600 text-sm">
                                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow bg-white p-4 rounded-lg shadow-sm">
                                                                    <div class="flex items-center space-x-2 mb-2">
                                                                        <span class="font-semibold text-gray-800">{{ $reply->user->name }}</span>
                                                                        <span class="text-gray-400 text-sm">{{ $reply->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                    <p class="text-gray-700">{{ $reply->body }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-6 mb-6">
                                                    <div class="flex justify-center mb-3">
                                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <p class="text-gray-500 font-medium">Belum ada balasan untuk diskusi ini</p>
                                                    <p class="text-sm text-gray-400 mt-1">Jadilah yang pertama memberikan jawaban!</p>
                                                </div>
                                            @endif

                                            {{-- FORM BALAS - BAGIAN YANG DITAMBAHKAN! --}}
                                            <div class="border-t border-gray-200 pt-6">
                                                <form action="{{ route('discussions.replies.store', $discussion) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    
                                                    <!-- Form header -->
                                                    <div class="flex items-center space-x-2 mb-3">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                                            <span class="font-bold text-white text-xs">
                                                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-700">Tulis balasan Anda</span>
                                                    </div>
                                                    
                                                    <!-- Textarea -->
                                                    <div class="relative">
                                                        <textarea 
                                                            name="body" 
                                                            rows="4" 
                                                            class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-20 rounded-lg shadow-sm resize-none transition-all duration-200 placeholder-gray-400" 
                                                            placeholder="Bagikan pemikiran, pertanyaan, atau masukan Anda..."
                                                            required
                                                        ></textarea>
                                                    </div>
                                                    
                                                    <!-- Form actions -->
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>Bersikaplah sopan dan konstruktif</span>
                                                        </div>
                                                        
                                                        <div class="flex items-center space-x-3">
                                                            <button 
                                                                type="button" 
                                                                class="text-xs text-gray-500 hover:text-gray-700 transition-colors duration-200"
                                                                onclick="this.closest('form').querySelector('textarea[name=body]').value = ''"
                                                            >
                                                                Bersihkan
                                                            </button>
                                                            <button 
                                                                type="submit"
                                                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                            >
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                                </svg>
                                                                Kirim Balasan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($discussions->hasPages())
                            <div class="mt-8 flex justify-center">
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                    {{ $discussions->links() }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>