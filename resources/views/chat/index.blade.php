<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- ✅ PHASE 2.2 IMPLEMENTATION: Navigation Component with Back Button --}}
    <x-chat-navigation 
        title="Chat Center"
        :breadcrumbs="[
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Chats']
        ]">
        
        {{-- Action Buttons in Navigation --}}
        <x-slot name="actions">
            @can('create', App\Models\Chat::class)
                <button id="newChatBtn" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Chat
                </button>
            @endcan
            
            <button class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search
            </button>
        </x-slot>
    </x-chat-navigation>
    
    <div class="container mx-auto px-4 py-6">
        <div class="flex h-[calc(100vh-12rem)]">
            <!-- Enhanced Chat List Sidebar -->
            <div class="w-1/3 bg-white rounded-l-lg shadow-lg border-r border-gray-200 flex flex-col">
                <!-- Search Bar -->
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search conversations..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Chat Filter Tabs -->
                <div class="flex border-b border-gray-200 bg-gray-50">
                    <button class="flex-1 px-4 py-3 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 bg-white">
                        All Chats
                        <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-full text-xs">{{ $chats->count() }}</span>
                    </button>
                    <button class="flex-1 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Unread
                        <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs">3</span>
                    </button>
                </div>

                <!-- Chat List -->
                <div class="flex-1 overflow-y-auto">
                    @forelse($chats as $chat)
                        <a href="{{ route('chat.show', $chat) }}" 
                           class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 relative group">
                            
                            {{-- Unread Indicator --}}
                            <div class="absolute left-2 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-indigo-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="flex items-start space-x-3 ml-2">
                                <!-- Enhanced Chat Avatar -->
                                <div class="flex-shrink-0 relative">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                        <span class="text-white font-semibold text-sm">
                                            {{ strtoupper(substr($chat->getDisplayName(), 0, 1)) }}
                                        </span>
                                    </div>
                                    {{-- Online Status --}}
                                    <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></div>
                                </div>

                                <!-- Chat Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $chat->getDisplayName() }}
                                        </h4>
                                        <span class="text-xs text-gray-500 flex-shrink-0 ml-2">
                                            {{ $chat->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-600 truncate">
                                            @if($chat->latestMessage)
                                                <span class="font-medium">{{ $chat->latestMessage->user->name }}:</span>
                                                {{ Str::limit($chat->latestMessage->content, 30) }}
                                            @else
                                                <span class="italic text-gray-400">No messages yet</span>
                                            @endif
                                        </p>
                                        
                                        {{-- Chat Type Badge --}}
                                        <div class="flex items-center space-x-1 flex-shrink-0 ml-2">
                                            @if($chat->type === 'group')
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            @endif
                                            
                                            {{-- Unread Count --}}
                                            @if(rand(0, 3) > 1) {{-- Simulate unread messages --}}
                                                <span class="px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full min-w-[18px] text-center">
                                                    {{ rand(1, 9) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Course Period Info --}}
                                    @if($chat->course_period_id)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                {{ Str::limit($chat->coursePeriod->course->title, 20) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Chats Yet</h3>
                            <p class="text-gray-600 mb-4">You don't have any chat conversations yet.</p>
                            @can('create', App\Models\Chat::class)
                                <button id="newChatBtnEmpty" 
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    Start Your First Chat
                                </button>
                            @endcan
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Enhanced Main Content Area -->
            <div class="flex-1 bg-gradient-to-br from-gray-50 to-gray-100 rounded-r-lg shadow-lg flex items-center justify-center">
                <div class="text-center max-w-md">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Welcome to Chat Center</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Select a conversation from the sidebar to start messaging, or create a new chat to connect with other participants.
                    </p>
                    
                    @can('create', App\Models\Chat::class)
                        <button id="newChatBtnCenter" 
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Start New Conversation
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced New Chat Modal -->
    <div id="newChatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Start New Chat</h3>
                        <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="newChatForm" class="space-y-4">
                        @csrf
                        
                        <!-- Chat Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Chat Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="type" value="direct" checked class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Direct</div>
                                            <div class="text-xs text-gray-500">1-on-1 chat</div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="type" value="group" class="sr-only">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Group</div>
                                            <div class="text-xs text-gray-500">Multiple people</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Course Period Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Course (Optional)</label>
                            <select name="course_period_id" id="coursePeriodSelect" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select a course...</option>
                                <!-- Options will be loaded via JavaScript -->
                            </select>
                        </div>

                        <!-- Participants Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Participants</label>
                            <select name="participants[]" id="participantsSelect" multiple 
                                    class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <!-- Options will be loaded via JavaScript -->
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple participants</p>
                        </div>

                        <!-- Chat Title (for group chats) -->
                        <div id="chatTitleField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Group Chat Name</label>
                            <input type="text" name="title" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Enter group chat name...">
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" id="cancelBtn" 
                                    class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200">
                                Create Chat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Enhanced Chat Management JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('newChatModal');
        const newChatBtns = ['newChatBtn', 'newChatBtnEmpty', 'newChatBtnCenter'];
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const newChatForm = document.getElementById('newChatForm');
        const chatTypeInputs = document.querySelectorAll('input[name="type"]');
        const chatTitleField = document.getElementById('chatTitleField');

        // Open modal event listeners
        newChatBtns.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                btn.addEventListener('click', openModal);
            }
        });

        // Close modal event listeners
        closeModalBtn?.addEventListener('click', closeModal);
        cancelBtn?.addEventListener('click', closeModal);
        
        // Close modal when clicking outside
        modal?.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Chat type change handler
        chatTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'group') {
                    chatTitleField.classList.remove('hidden');
                } else {
                    chatTitleField.classList.add('hidden');
                }
                
                // Update radio button styling
                updateRadioStyling();
            });
        });

        // Form submission
        newChatForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Creating...';
            submitBtn.disabled = true;
            
            fetch('/chats', {
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
                    // Redirect to the new chat
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error(data.message || 'Failed to create chat');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating chat: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        function openModal() {
            modal.classList.remove('hidden');
            loadCoursePeriods();
            loadParticipants();
            updateRadioStyling();
        }

        function closeModal() {
            modal.classList.add('hidden');
            newChatForm.reset();
            chatTitleField.classList.add('hidden');
            updateRadioStyling();
        }

        function updateRadioStyling() {
            chatTypeInputs.forEach(input => {
                const label = input.closest('label');
                if (input.checked) {
                    label.classList.add('border-indigo-500', 'bg-indigo-50');
                    label.classList.remove('border-gray-300', 'bg-white');
                } else {
                    label.classList.remove('border-indigo-500', 'bg-indigo-50');
                    label.classList.add('border-gray-300', 'bg-white');
                }
            });
        }

        function loadCoursePeriods() {
            fetch('/course-periods/available', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('coursePeriodSelect');
                select.innerHTML = '<option value="">Select a course...</option>';
                
                data.forEach(period => {
                    const option = document.createElement('option');
                    option.value = period.id;
                    option.textContent = period.course.title;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading course periods:', error);
            });
        }

        function loadParticipants() {
            fetch('/users/available', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('participantsSelect');
                select.innerHTML = '';
                
                data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading participants:', error);
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape key to close modal
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
            
            // Ctrl/Cmd + N to open new chat modal
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && modal.classList.contains('hidden')) {
                e.preventDefault();
                openModal();
            }
        });

        // Auto-refresh chat list every 30 seconds
        setInterval(function() {
            // Simple way to refresh - reload the page
            // In production, you might want to use WebSocket or fetch updates
            if (document.visibilityState === 'visible') {
                console.log('Auto-refreshing chat list...');
                // window.location.reload();
            }
        }, 30000);

        // Mark page as active for real-time updates
        let isPageActive = true;
        
        document.addEventListener('visibilitychange', function() {
            isPageActive = !document.hidden;
            console.log('Page visibility changed:', isPageActive ? 'active' : 'hidden');
        });

        // Initialize radio button styling
        updateRadioStyling();
    });

    // Global functions for navigation component
    function goBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = "{{ route('dashboard') }}";
        }
    }
    </script>

    <style>
    /* Custom styling for enhanced chat interface */
    .chat-list-item:hover {
        transform: translateX(2px);
        transition: transform 0.2s ease;
    }
    
    /* Custom scrollbar for chat list */
    .chat-list::-webkit-scrollbar {
        width: 4px;
    }
    
    .chat-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .chat-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
    
    .chat-list::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    /* Radio button animation */
    input[type="radio"]:checked + div {
        animation: radioSelect 0.2s ease-in-out;
    }
    
    @keyframes radioSelect {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Modal entrance animation */
    #newChatModal:not(.hidden) .bg-white {
        animation: modalEnter 0.3s ease-out;
    }
    
    @keyframes modalEnter {
        0% {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    </style>
</x-app-layout>