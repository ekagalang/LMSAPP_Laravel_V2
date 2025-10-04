@props([
    'title' => 'Chat',
    'showBackButton' => true,
    'backRoute' => null,
    'breadcrumbs' => [],
    'actions' => null
])

<div class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        {{-- Left Section: Back Button + Breadcrumbs --}}
        <div class="flex items-center space-x-4">
            @if($showBackButton)
                <button onclick="goBack()" 
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-all duration-200 group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            @endif
            
            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center space-x-2 text-sm">
                @if(!empty($breadcrumbs))
                    @foreach($breadcrumbs as $index => $breadcrumb)
                        @if($index > 0)
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        @endif
                        
                        @if(isset($breadcrumb['url']) && !$loop->last)
                            <a href="{{ $breadcrumb['url'] }}" 
                               class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                {{ $breadcrumb['title'] }}
                            </a>
                        @else
                            <span class="text-gray-900 font-medium">{{ $breadcrumb['title'] }}</span>
                        @endif
                    @endforeach
                @else
                    {{-- Default breadcrumb --}}
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        Dashboard
                    </a>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ $title }}</span>
                @endif
            </nav>
        </div>
        
        {{-- Center Section: Page Title --}}
        <div class="flex-1 text-center">
            <h1 class="text-xl font-semibold text-gray-900">{{ $title }}</h1>
        </div>
        
        {{-- Right Section: Actions --}}
        <div class="flex items-center space-x-3">
            {{ $actions ?? '' }}
        </div>
    </div>
</div>

{{-- Enhanced Chat Header for Individual Chat Pages --}}
@if(isset($chat))
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            {{-- Chat Info --}}
            <div class="flex items-center space-x-4">
                @if($showBackButton)
                    <button onclick="goBack()" 
                            class="flex items-center justify-center w-10 h-10 rounded-lg bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 transition-all duration-200 shadow-sm group">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                @endif
                
                {{-- Chat Avatar --}}
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <span class="text-white font-semibold text-lg">
                            {{ strtoupper(substr($chat->getDisplayName(), 0, 1)) }}
                        </span>
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $chat->getDisplayName() }}</h2>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            @if($chat->type === 'group')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Group Chat</span>
                                <span>•</span>
                                <span>{{ $chat->participants()->count() }} members</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Direct Message</span>
                            @endif
                            
                            @if($chat->course_class_id)
                                <span>•</span>
                                <span class="text-blue-600 font-medium">{{ $chat->courseClass->course->title }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Chat Actions --}}
            <div class="flex items-center space-x-2">
                {{-- Online Status Indicator --}}
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    <span>Online</span>
                </div>
                
                {{-- More Actions --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center justify-center w-10 h-10 rounded-lg bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                        
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Chat Info
                        </a>
                        
                        @if($chat->type === 'group')
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Manage Members
                            </a>
                        @endif
                        
                        <hr class="my-1">
                        
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Leave Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
function goBack() {
    // Enhanced back functionality with multiple fallbacks
    if (window.history.length > 1) {
        // If there's previous history, go back
        window.history.back();
    } else {
        // Fallback routes based on current page context
        @if(isset($backRoute))
            window.location.href = "{{ $backRoute }}";
        @elseif(Request::routeIs('chat.*'))
            window.location.href = "{{ route('chat.index') }}";
        @elseif(Request::routeIs('discussions.*'))
            window.location.href = "{{ route('discussions.index') }}";
        @else
            window.location.href = "{{ route('dashboard') }}";
        @endif
    }
}

// Keyboard shortcut for back navigation
document.addEventListener('keydown', function(e) {
    // Alt + Left Arrow or Ctrl/Cmd + Left Arrow
    if ((e.altKey || e.metaKey || e.ctrlKey) && e.key === 'ArrowLeft') {
        e.preventDefault();
        goBack();
    }
});

// Enhanced browser back button handling
window.addEventListener('popstate', function(e) {
    // Custom handling if needed
    console.log('Browser back button pressed');
});
</script>