{{-- resources/views/chat/index.blade.php --}}
<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Chat Navigation --}}
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
        </x-slot>
    </x-chat-navigation>
    
    {{-- Main Chat Container --}}
    <div class="h-[calc(100vh-8rem)] flex" id="chat-app" data-user-id="{{ auth()->id() }}">
        {{-- Sidebar - Chat List --}}
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
            {{-- Header dengan Search --}}
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search conversations..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Filter Tabs --}}
            <div class="flex border-b border-gray-200 bg-gray-50">
                <button class="flex-1 px-4 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-white" id="allChatsTab">
                    All Chats
                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full text-xs">{{ $chats->count() }}</span>
                </button>
                <button class="flex-1 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" id="unreadTab">
                    Unread
                    <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs" id="unreadCount">0</span>
                </button>
            </div>

            {{-- Chat List --}}
            <div class="flex-1 overflow-y-auto chat-scroll" id="chatList">
                @forelse($chats as $chat)
                    <div class="chat-item p-4 cursor-pointer hover:bg-gray-50 transition-all duration-200 {{ $loop->first ? 'active bg-gradient-to-r from-blue-500 to-purple-600 text-white' : '' }}" 
                         data-chat-id="{{ $chat->id }}" 
                         onclick="selectChat({{ $chat->id }})">
                        
                        <div class="flex items-start space-x-3">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0 relative">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($chat->getDisplayName(), 0, 1)) }}
                                    </span>
                                </div>
                                {{-- Status indicator --}}
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            
                            {{-- Chat Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold truncate {{ $loop->first ? 'text-white' : 'text-gray-900' }}">
                                        {{ $chat->getDisplayName() }}
                                    </p>
                                    <span class="text-xs {{ $loop->first ? 'text-white opacity-75' : 'text-gray-500' }}">
                                        {{ $chat->updated_at->format('H:i') }}
                                    </span>
                                </div>
                                
                                {{-- Last message preview --}}
                                <p class="text-sm truncate {{ $loop->first ? 'text-white opacity-75' : 'text-gray-600' }}">
                                    @if($chat->lastMessage)
                                        {{ Str::limit($chat->lastMessage->content, 40) }}
                                    @else
                                        No messages yet
                                    @endif
                                </p>
                                
                                {{-- Chat meta info --}}
                                <div class="flex items-center mt-1">
                                    @if($chat->type === 'group')
                                        <span class="text-xs {{ $loop->first ? 'text-white opacity-75' : 'text-gray-500' }}">
                                            {{ $chat->activeParticipants->count() }} members
                                        </span>
                                    @else
                                        {{-- Unread count indicator --}}
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2 hidden unread-indicator"></span>
                                        <span class="text-xs {{ $loop->first ? 'text-white opacity-75' : 'text-gray-500' }}">Direct message</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Empty state --}}
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Chats Yet</h3>
                        <p class="text-gray-600 mb-4">Start a conversation to begin chatting.</p>
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

        {{-- Main Chat Area --}}
        <div class="flex-1 flex flex-col" id="chatArea">
            @if($chats->count() > 0)
                {{-- Chat Header --}}
                <div class="p-4 border-b border-gray-200 bg-white" id="chatHeader">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm" id="chatHeaderAvatar">
                                    {{ strtoupper(substr($chats->first()->getDisplayName(), 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900" id="chatHeaderName">
                                    {{ $chats->first()->getDisplayName() }}
                                </h2>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-gray-500" id="chatHeaderStatus">
                                        @if($chats->first()->type === 'group')
                                            {{ $chats->first()->activeParticipants->count() }} members
                                        @else
                                            Online
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Chat Actions --}}
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </button>
                            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Messages Container --}}
                <div class="flex-1 overflow-y-auto chat-scroll p-4 bg-gray-50" id="messagesContainer" data-chat-id="{{ $chats->first()->id }}">
                    {{-- Messages will be loaded here --}}
                    <div class="text-center py-8">
                        <div class="animate-spin inline-block w-8 h-8 border-[3px] border-current border-t-transparent text-blue-600 rounded-full" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-gray-500 mt-2">Loading messages...</p>
                    </div>
                </div>

                {{-- Message Input --}}
                <div class="p-4 border-t border-gray-200 bg-white" id="messageInput">
                    <div class="flex items-end space-x-3">
                        <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </button>
                        <div class="flex-1">
                            <textarea id="messageTextarea" 
                                      placeholder="Type your message..." 
                                      class="w-full resize-none border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm max-h-32"
                                      rows="1"></textarea>
                        </div>
                        <button id="sendBtn" class="p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @else
                {{-- Empty state when no chats --}}
                <div class="flex-1 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
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
                            Start a conversation to connect with other participants and collaborate effectively.
                        </p>
                        @can('create', App\Models\Chat::class)
                            <button id="newChatBtnWelcome" 
                                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 shadow-lg hover:shadow-xl">
                                Start Your First Chat
                            </button>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- New Chat Modal --}}
    <div id="newChatModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Create New Chat</h3>
                    <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="createChatForm">
                    <div class="space-y-4">
                        {{-- Chat Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chat Type</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="direct" checked class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Direct Message</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="group" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Group Chat</span>
                                </label>
                            </div>
                        </div>

                        {{-- Course Period Selection - Only for admin/instructor/EO --}}
                        @if(auth()->user()->hasRole(['super-admin', 'instructor', 'event-organizer']))
                        <div id="coursePeriodSection">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Course <span class="text-gray-500">(Optional)</span>
                            </label>
                            <select name="course_period_id" id="coursePeriodSelect" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select a course...</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Select a course to create course-specific chat. Leave empty for general chat.
                            </p>
                        </div>
                        @else
                        {{-- Participants don't see course selection --}}
                        <input type="hidden" name="course_period_id" value="">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-700">
                                💬 You can chat with instructors, organizers, and other participants from your courses.
                            </p>
                        </div>
                        @endif

                        {{-- Participants Selection --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Participants</label>
                            <div class="border border-gray-300 rounded-lg max-h-48 overflow-y-auto p-2" id="participantsContainer">
                                <p class="text-gray-500 text-sm p-2">Loading participants...</p>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                @if(auth()->user()->hasRole(['super-admin', 'instructor', 'event-organizer']))
                                    Participants will be filtered based on the selected course.
                                @else
                                    You can chat with people from your enrolled courses.
                                @endif
                            </p>
                        </div>

                        {{-- Group Chat Title --}}
                        <div id="chatTitleField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Group Chat Name</label>
                            <input type="text" name="title" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Enter group chat name...">
                            <p class="mt-1 text-xs text-gray-500">
                                Leave empty to auto-generate name based on participants.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                            Create Chat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Global variables
        let currentChatId = {{ $chats->first()->id ?? 'null' }};
        let messagesPollingInterval;
        const userId = {{ auth()->id() }};

        // DOM Elements
        const messageTextarea = document.getElementById('messageTextarea');
        const sendBtn = document.getElementById('sendBtn');
        const messagesContainer = document.getElementById('messagesContainer');
        const chatList = document.getElementById('chatList');

        // Initialize chat interface
        document.addEventListener('DOMContentLoaded', function() {
            // Auto resize textarea
            if (messageTextarea) {
                messageTextarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 128) + 'px';
                });

                // Send message on Enter (but allow Shift+Enter for new lines)
                messageTextarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendMessage();
                    }
                });
            }

            // Send button click
            if (sendBtn) {
                sendBtn.addEventListener('click', sendMessage);
            }

            // Load initial chat if available
            if (currentChatId) {
                loadChatMessages(currentChatId);
                startMessagesPolling();
            }

            // Initialize modal handlers
            initializeModal();
        });

        // Select chat function
        function selectChat(chatId) {
            // Update UI
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-600', 'text-white');
                item.classList.add('hover:bg-gray-50');
            });
            
            const selectedItem = document.querySelector(`[data-chat-id="${chatId}"]`);
            if (selectedItem) {
                selectedItem.classList.add('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-600', 'text-white');
                selectedItem.classList.remove('hover:bg-gray-50');
            }
            
            currentChatId = chatId;
            
            // Load chat messages
            loadChatMessages(chatId);
            
            // Restart polling for new chat
            if (messagesPollingInterval) {
                clearInterval(messagesPollingInterval);
            }
            startMessagesPolling();
        }

        // Load chat messages
        async function loadChatMessages(chatId) {
            if (!chatId) return;
            
            try {
                // Update messages container
                messagesContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="animate-spin inline-block w-8 h-8 border-[3px] border-current border-t-transparent text-blue-600 rounded-full"></div>
                        <p class="text-gray-500 mt-2">Loading messages...</p>
                    </div>
                `;
                
                const response = await fetch(`/chat/${chatId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Update chat header
                    updateChatHeader(data.chat);
                    
                    // Load messages
                    displayMessages(data.messages || []);
                    
                    // Update container data
                    messagesContainer.dataset.chatId = chatId;
                } else {
                    throw new Error(data.message || 'Failed to load chat');
                }
            } catch (error) {
                console.error('Error loading chat:', error);
                messagesContainer.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-500">Error loading messages. Please try again.</p>
                    </div>
                `;
            }
        }

        // Update chat header
        function updateChatHeader(chat) {
            document.getElementById('chatHeaderName').textContent = chat.display_name || chat.name;
            document.getElementById('chatHeaderAvatar').textContent = chat.display_name ? chat.display_name.charAt(0).toUpperCase() : 'C';
            
            const statusText = chat.type === 'group' 
                ? `${chat.participants_count || 0} members`
                : 'Online';
            document.getElementById('chatHeaderStatus').textContent = statusText;
        }

        // Display messages
        function displayMessages(messages) {
            if (!Array.isArray(messages)) {
                messagesContainer.innerHTML = '<p class="text-center text-gray-500 py-8">No messages yet</p>';
                return;
            }
            
            messagesContainer.innerHTML = '';
            
            messages.forEach(message => {
                addMessageToDOM(message);
            });
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Add message to DOM
        function addMessageToDOM(message) {
            const isOwn = message.user_id === userId;
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message-bubble mb-4';
            
            const timeFormatted = new Date(message.created_at).toLocaleTimeString([], {
                hour: '2-digit', 
                minute: '2-digit'
            });
            
            if (isOwn) {
                messageDiv.innerHTML = `
                    <div class="flex items-start space-x-3 justify-end">
                        <div class="flex flex-col items-end">
                            <div class="flex items-baseline space-x-2 mb-1">
                                <span class="text-xs text-gray-500">${timeFormatted}</span>
                                <span class="text-sm font-semibold text-gray-800">You</span>
                            </div>
                            <div class="bg-blue-500 text-white rounded-2xl rounded-tr-md px-4 py-2 shadow-sm max-w-xs">
                                <p class="text-sm">${escapeHtml(message.content)}</p>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-semibold text-xs">Me</span>
                        </div>
                    </div>
                `;
            } else {
                const avatarText = message.user && message.user.name 
                    ? message.user.name.charAt(0).toUpperCase()
                    : 'U';
                    
                messageDiv.innerHTML = `
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-semibold text-xs">${avatarText}</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-baseline space-x-2 mb-1">
                                <span class="text-sm font-semibold text-gray-800">${escapeHtml(message.user?.name || 'Unknown')}</span>
                                <span class="text-xs text-gray-500">${timeFormatted}</span>
                            </div>
                            <div class="bg-white rounded-2xl rounded-tl-md px-4 py-2 shadow-sm max-w-xs">
                                <p class="text-sm text-gray-800">${escapeHtml(message.content)}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            messagesContainer.appendChild(messageDiv);
        }

        // Send message
        async function sendMessage() {
            const content = messageTextarea.value.trim();
            if (!content || !currentChatId) return;
            
            // Disable send button
            sendBtn.disabled = true;
            
            try {
                const response = await fetch(`/chats/${currentChatId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: content
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Clear input
                    messageTextarea.value = '';
                    messageTextarea.style.height = 'auto';
                    
                    // Add message to DOM
                    addMessageToDOM(data.message);
                    
                    // Scroll to bottom
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                } else {
                    throw new Error(data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            } finally {
                sendBtn.disabled = false;
            }
        }

        // Start polling for new messages
        function startMessagesPolling() {
            if (!currentChatId) return;
            
            messagesPollingInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/chat/${currentChatId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.messages) {
                        // Check if we have new messages
                        const currentMessageCount = messagesContainer.querySelectorAll('.message-bubble').length;
                        if (data.messages.length > currentMessageCount) {
                            displayMessages(data.messages);
                        }
                    }
                } catch (error) {
                    console.error('Error polling messages:', error);
                }
            }, 3000); // Poll every 3 seconds
        }

        // Utility function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Initialize modal (keep existing modal functionality)
        function initializeModal() {
            const modal = document.getElementById('newChatModal');
            const newChatBtns = document.querySelectorAll('#newChatBtn, #newChatBtnEmpty, #newChatBtnWelcome');
            const closeModalBtn = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            
            // Open modal
            newChatBtns.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => {
                        modal.classList.remove('hidden');
                        loadAvailableUsers();
                    });
                }
            });
            
            // Close modal
            [closeModalBtn, cancelBtn].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                    });
                }
            });
            
            // Close modal on outside click
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }

        // Load available users for chat creation (updated dengan course filtering)
        async function loadAvailableUsers(coursePeriodId = null) {
            try {
                const url = new URL('/users/available', window.location.origin);
                if (coursePeriodId) {
                    url.searchParams.append('course_period_id', coursePeriodId);
                }
                
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                const container = document.getElementById('participantsContainer');
                
                if (response.ok && Array.isArray(data)) {
                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-gray-500 text-sm p-2">No users available for this course</p>';
                        return;
                    }
                    
                    container.innerHTML = data.map(user => {
                        const roleLabel = user.role_in_course ? ` (${user.role_in_course})` : '';
                        return `
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                                <input type="checkbox" name="participants[]" value="${user.id}" class="text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-2">
                                    <span class="text-sm text-gray-700">${escapeHtml(user.name)}${roleLabel}</span>
                                    <div class="text-xs text-gray-500">${escapeHtml(user.email)}</div>
                                </div>
                            </label>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-sm p-2">No users available</p>';
                }
            } catch (error) {
                console.error('Error loading users:', error);
                document.getElementById('participantsContainer').innerHTML = '<p class="text-red-500 text-sm p-2">Error loading users</p>';
            }
        }

        // Load available course periods
        async function loadAvailableCoursePeriods() {
            try {
                const response = await fetch('/course-periods/available', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                const select = document.getElementById('coursePeriodSelect');
                
                if (response.ok && Array.isArray(data)) {
                    select.innerHTML = '<option value="">Select a course...</option>' + 
                        data.map(period => `
                            <option value="${period.id}">${escapeHtml(period.display_name)}</option>
                        `).join('');
                } else {
                    select.innerHTML = '<option value="">No courses available</option>';
                }
            } catch (error) {
                console.error('Error loading course periods:', error);
                document.getElementById('coursePeriodSelect').innerHTML = '<option value="">Error loading courses</option>';
            }
        }

        // Handle course period change
        function handleCoursePeriodChange() {
            const coursePeriodSelect = document.getElementById('coursePeriodSelect');
            if (coursePeriodSelect) {
                coursePeriodSelect.addEventListener('change', function() {
                    const selectedCourseId = this.value;
                    // Reload users based on selected course
                    loadAvailableUsers(selectedCourseId);
                });
            }
        }

        // Initialize modal (keep existing modal functionality)
        function initializeModal() {
            const modal = document.getElementById('newChatModal');
            const newChatBtns = document.querySelectorAll('#newChatBtn, #newChatBtnEmpty, #newChatBtnWelcome');
            const closeModalBtn = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const createChatForm = document.getElementById('createChatForm');
            
            // Open modal
            newChatBtns.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => {
                        modal.classList.remove('hidden');
                        
                        // Only load course periods for admin/instructor/EO
                        const coursePeriodSection = document.getElementById('coursePeriodSection');
                        if (coursePeriodSection) {
                            loadAvailableCoursePeriods();
                        }
                        
                        // Load available users initially (no course filter)
                        loadAvailableUsers();
                    });
                }
            });
            
            // Close modal
            [closeModalBtn, cancelBtn].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        createChatForm.reset();
                    });
                }
            });
            
            // Close modal on outside click
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    createChatForm.reset();
                }
            });

            // Handle course period change (only if course selection exists)
            const coursePeriodSelect = document.getElementById('coursePeriodSelect');
            if (coursePeriodSelect) {
                coursePeriodSelect.addEventListener('change', function() {
                    const selectedCourseId = this.value;
                    // Reload users based on selected course
                    loadAvailableUsers(selectedCourseId);
                });
            }

            // Handle chat type change
            const chatTypeInputs = document.querySelectorAll('input[name="type"]');
            chatTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const chatTitleField = document.getElementById('chatTitleField');
                    if (this.value === 'group') {
                        chatTitleField.classList.remove('hidden');
                    } else {
                        chatTitleField.classList.add('hidden');
                    }
                });
            });

            // Handle form submission
            if (createChatForm) {
                createChatForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await createNewChat(new FormData(createChatForm));
                });
            }
        }

        // Create new chat
        async function createNewChat(formData) {
            try {
                const participants = Array.from(formData.getAll('participants[]'));
                
                if (participants.length === 0) {
                    alert('Please select at least one participant');
                    return;
                }

                const response = await fetch('/chats', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: formData.get('type'),
                        course_period_id: formData.get('course_period_id') || null,
                        participant_ids: participants,
                        name: formData.get('title') || null
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Close modal
                    document.getElementById('newChatModal').classList.add('hidden');
                    document.getElementById('createChatForm').reset();
                    
                    // Reload page to show new chat
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to create chat');
                }
            } catch (error) {
                console.error('Error creating chat:', error);
                alert('Failed to create chat: ' + error.message);
            }
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (messagesPollingInterval) {
                clearInterval(messagesPollingInterval);
            }
        });
    </script>

    <style>
        /* Custom scrollbar untuk area chat */
        .chat-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .chat-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .chat-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Animation untuk chat selection */
        .chat-item.active {
            transform: translateX(4px);
        }

        .chat-item:hover:not(.active) {
            transform: translateX(2px);
            transition: all 0.2s ease;
        }

        /* Message bubble animations */
        .message-bubble {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #chat-app {
                flex-direction: column;
            }
            
            .w-80 {
                width: 100%;
                max-height: 40vh;
            }
        }
    </style>
</x-app-layout>