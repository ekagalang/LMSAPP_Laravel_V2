<div class="space-y-6">
    @forelse ($replies as $reply)
        <div class="flex items-start space-x-4 group">
            <!-- Avatar with better styling -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="font-bold text-white text-sm">
                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                    </span>
                </div>
            </div>
            
            <!-- Reply content with improved card design -->
            <div class="flex-grow min-w-0">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <!-- Header with user info -->
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-gray-900 text-sm">{{ $reply->user->name }}</h4>
                        <time class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $reply->created_at->diffForHumans() }}
                        </time>
                    </div>
                    
                    <!-- Reply body -->
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {{ $reply->body }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <!-- Empty state with better design -->
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium">Belum ada balasan</p>
            <p class="text-gray-400 text-xs mt-1">Jadilah yang pertama memberikan balasan pada diskusi ini</p>
        </div>
    @endforelse
</div>

<!-- Reply form with improved styling -->
<div class="mt-8 pt-6 border-t border-gray-200">
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
        
        <!-- Textarea with better styling -->
        <div class="relative">
            <textarea 
                name="body" 
                rows="3" 
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
                    onclick="document.querySelector('textarea[name=body]').value = ''"
                >
                    Bersihkan
                </button>
                <x-primary-button 
                    type="submit" 
                    class="text-xs px-4 py-2 bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Kirim Balasan
                </x-primary-button>
            </div>
        </div>
    </form>
</div>