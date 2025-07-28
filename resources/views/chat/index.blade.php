{{-- resources/views/chat/index.blade.php - dengan debug logs --}}
<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="container mx-auto px-4 py-6">
        <div class="flex h-[calc(100vh-8rem)]">
            <!-- Chat List Sidebar -->
            <div class="w-1/3 bg-white rounded-l-lg shadow-lg border-r border-gray-200 flex flex-col">
                <!-- Header with New Chat Button -->
                <div class="p-4 bg-gray-50 rounded-tl-lg border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-gray-800">Chats</h2>
                        @can('create', App\Models\Chat::class)
                            <button id="newChatBtn" 
                                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Chat
                            </button>
                        @endcan
                    </div>
                    <p class="text-sm text-gray-600">{{ $chats->count() }} conversations</p>
                </div>

                <!-- Chat List -->
                <div class="flex-1 overflow-y-auto">
                    @forelse($chats as $chat)
                        <a href="{{ route('chat.show', $chat) }}" 
                           class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start space-x-3">
                                <!-- Chat Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ strtoupper(substr($chat->getDisplayName(), 0, 1)) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Chat Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $chat->getDisplayName() }}
                                        </h3>
                                        @if($chat->last_message_at)
                                            <span class="text-xs text-gray-500">
                                                {{ $chat->last_message_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Context Info -->
                                    @php $context = $chat->getContextInfo(); @endphp
                                    <p class="text-xs text-gray-500 mb-1">
                                        {{ $context['context'] }}
                                    </p>

                                    <!-- Latest Message -->
                                    @if($chat->latestMessage)
                                        <p class="text-sm text-gray-600 truncate">
                                            <span class="font-medium">{{ $chat->latestMessage->user->name }}:</span>
                                            {{ Str::limit($chat->latestMessage->content, 50) }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400 italic">No messages yet</p>
                                    @endif

                                    <!-- Participants -->
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-400">
                                            {{ $chat->activeParticipants->count() }} participants
                                        </span>
                                    </div>
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

            <!-- Main Content Area -->
            <div class="flex-1 bg-gray-50 rounded-r-lg shadow-lg flex items-center justify-center">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Select a Chat</h3>
                    <p class="text-gray-600">Choose a conversation from the sidebar to start messaging.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div id="newChatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Start New Chat</h3>
                        <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="newChatForm">
                        @csrf
                        
                        <!-- Chat Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chat Type</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="direct" checked class="mr-2">
                                    <span class="text-sm">Direct Chat</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="group" class="mr-2">
                                    <span class="text-sm">Group Chat</span>
                                </label>
                            </div>
                        </div>

                        <!-- Course Period Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Course Period <span id="courseOptionalText">(Optional)</span>
                            </label>
                            <select name="course_period_id" id="coursePeriodSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select Course Period (Optional)</option>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>

                        <!-- Group Name (for direct chat with course period or custom group) -->
                        <div id="groupNameField" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Group Name</label>
                            <input type="text" name="name" id="groupNameInput" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <!-- Direct Chat Participants Selection -->
                        <div id="directChatUsers" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Participants</label>
                            <div class="border border-gray-300 rounded-lg max-h-40 overflow-y-auto">
                                <div id="usersList" class="p-2">
                                    <div class="text-center py-4 text-gray-500">Loading users...</div>
                                </div>
                            </div>
                            <div id="selectedUsers" class="mt-2 flex flex-wrap gap-2">
                                <!-- Selected users will appear here -->
                            </div>
                        </div>

                        <!-- Group Chat Info -->
                        <div id="groupChatInfo" class="mb-6 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Group Chat:</strong> All participants from the selected course period will be automatically added to this group chat.
                                </p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit" id="createChatBtn" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-medium transition-colors duration-200" disabled>
                                Create Chat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Global variables
        let selectedUserIds = [];
        let users = [];
        let coursePeriods = [];

        // Modal elements
        const modal = document.getElementById('newChatModal');
        const form = document.getElementById('newChatForm');
        const createBtn = document.getElementById('createChatBtn');

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 DOM Content Loaded - Starting to load data...');
            loadCoursePeriods();
            loadUsers();
            setupEventListeners();
        });

        // Setup all event listeners
        function setupEventListeners() {
            console.log('🎯 Setting up event listeners...');
            
            // Modal triggers
            document.getElementById('newChatBtn')?.addEventListener('click', openModal);
            document.getElementById('newChatBtnEmpty')?.addEventListener('click', openModal);
            document.getElementById('closeModalBtn').addEventListener('click', closeModal);
            document.getElementById('cancelBtn').addEventListener('click', closeModal);

            // Chat type change
            document.querySelectorAll('input[name="type"]').forEach(radio => {
                radio.addEventListener('change', handleChatTypeChange);
            });

            // Course period change
            document.getElementById('coursePeriodSelect').addEventListener('change', handleCoursePeriodChange);

            // Form submission
            form.addEventListener('submit', handleFormSubmit);

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        }

        // Open modal
        function openModal() {
            console.log('📝 Opening modal...');
            modal.classList.remove('hidden');
            resetForm();
        }

        // Close modal
        function closeModal() {
            console.log('❌ Closing modal...');
            modal.classList.add('hidden');
            resetForm();
        }

        // Reset form
        function resetForm() {
            form.reset();
            selectedUserIds = [];
            updateSelectedUsers();
            updateCreateButton();
            handleChatTypeChange();
        }

        // Handle chat type change
        function handleChatTypeChange() {
            const chatType = document.querySelector('input[name="type"]:checked').value;
            console.log('🔄 Chat type changed to:', chatType);
            
            const directChatUsers = document.getElementById('directChatUsers');
            const groupChatInfo = document.getElementById('groupChatInfo');
            const courseOptionalText = document.getElementById('courseOptionalText');
            const groupNameField = document.getElementById('groupNameField');
            
            if (chatType === 'group') {
                directChatUsers.classList.add('hidden');
                groupChatInfo.classList.remove('hidden');
                courseOptionalText.textContent = '(Required for Group Chat)';
                groupNameField.classList.remove('hidden');
                selectedUserIds = []; // Clear selections for group chat
            } else {
                directChatUsers.classList.remove('hidden');
                groupChatInfo.classList.add('hidden');
                courseOptionalText.textContent = '(Optional)';
                groupNameField.classList.add('hidden');
                loadUsers(); // Reload users for direct chat
            }
            
            updateCreateButton();
        }

        // Handle course period change
        function handleCoursePeriodChange() {
            const coursePeriodId = document.getElementById('coursePeriodSelect').value;
            const chatType = document.querySelector('input[name="type"]:checked').value;
            const groupNameInput = document.getElementById('groupNameInput');
            
            console.log('📚 Course period changed to:', coursePeriodId);
            
            if (coursePeriodId) {
                // Auto-fill group name for group chat
                if (chatType === 'group') {
                    const selectedPeriod = coursePeriods.find(p => p.id == coursePeriodId);
                    if (selectedPeriod) {
                        groupNameInput.value = selectedPeriod.full_name;
                    }
                }
                
                // Load users for this course period
                loadUsersForCoursePeriod(coursePeriodId);
            } else {
                // Load all available users
                loadUsers();
                if (chatType === 'group') {
                    groupNameInput.value = '';
                }
            }
            
            updateCreateButton();
        }

        // Load users from API
        async function loadUsers() {
            console.log('👥 Loading users...');
            
            try {
                const response = await fetch('/users/available', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                console.log('👥 Users response status:', response.status);
                console.log('👥 Users response headers:', Object.fromEntries(response.headers.entries()));
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Users API error:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('👥 Users data received:', data);
                
                users = data.users || [];
                console.log('👥 Total users loaded:', users.length);
                
                renderUsers();
                updateCreateButton();
            } catch (error) {
                console.error('❌ Error loading users:', error);
                document.getElementById('usersList').innerHTML = '<div class="text-center py-4 text-red-500">Error loading users: ' + error.message + '</div>';
            }
        }

        // Load users for specific course period
        async function loadUsersForCoursePeriod(coursePeriodId) {
            console.log('👥 Loading users for course period:', coursePeriodId);
            
            try {
                const response = await fetch(`/users/available?course_period_id=${coursePeriodId}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                console.log('👥 Course users response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Course users API error:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('👥 Course users data received:', data);
                
                users = data.users || [];
                console.log('👥 Total course users loaded:', users.length);
                
                renderUsers();
                updateCreateButton();
            } catch (error) {
                console.error('❌ Error loading users for course period:', error);
                document.getElementById('usersList').innerHTML = '<div class="text-center py-4 text-red-500">Error loading users: ' + error.message + '</div>';
            }
        }

        // Load course periods from API
        async function loadCoursePeriods() {
            console.log('📚 Loading course periods...');
            
            try {
                const response = await fetch('/course-periods/available', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                console.log('📚 Course periods response status:', response.status);
                console.log('📚 Course periods response headers:', Object.fromEntries(response.headers.entries()));
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Course periods API error:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('📚 Course periods data received:', data);
                
                coursePeriods = data.periods || [];
                console.log('📚 Total course periods loaded:', coursePeriods.length);
                
                renderCoursePeriods();
            } catch (error) {
                console.error('❌ Error loading course periods:', error);
            }
        }

        // Render users list
        function renderUsers() {
            console.log('🎨 Rendering users list...');
            const usersList = document.getElementById('usersList');
            
            if (users.length === 0) {
                usersList.innerHTML = '<div class="text-center py-4 text-gray-500">No users available</div>';
                return;
            }

            usersList.innerHTML = '';

            users.forEach(user => {
                const userElement = document.createElement('div');
                userElement.className = 'flex items-center p-2 hover:bg-gray-50 cursor-pointer rounded';
                userElement.innerHTML = `
                    <input type="checkbox" class="mr-2" value="${user.id}" onchange="toggleUser(${user.id})">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center mr-3">
                        <span class="text-white text-xs font-medium">${user.name.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">${user.name}</p>
                        <p class="text-sm text-gray-500">${user.email}</p>
                    </div>
                `;
                usersList.appendChild(userElement);
            });
            
            console.log('🎨 Users rendered successfully');
        }

        // Render course periods
        function renderCoursePeriods() {
            console.log('🎨 Rendering course periods...');
            const select = document.getElementById('coursePeriodSelect');
            select.innerHTML = '<option value="">Select Course Period (Optional)</option>';

            coursePeriods.forEach(period => {
                const option = document.createElement('option');
                option.value = period.id;
                option.textContent = period.full_name;
                select.appendChild(option);
            });
            
            console.log('🎨 Course periods rendered successfully');
        }

        // Toggle user selection
        function toggleUser(userId) {
            const checkbox = document.querySelector(`input[value="${userId}"]`);
            
            if (checkbox.checked) {
                if (!selectedUserIds.includes(userId)) {
                    selectedUserIds.push(userId);
                }
            } else {
                selectedUserIds = selectedUserIds.filter(id => id !== userId);
            }
            
            console.log('👤 Selected users:', selectedUserIds);
            updateSelectedUsers();
            updateCreateButton();
        }

        // Update selected users display
        function updateSelectedUsers() {
            const container = document.getElementById('selectedUsers');
            container.innerHTML = '';

            selectedUserIds.forEach(userId => {
                const user = users.find(u => u.id === userId);
                if (user) {
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800';
                    badge.innerHTML = `
                        ${user.name}
                        <button type="button" class="ml-1 text-indigo-600 hover:text-indigo-800" onclick="removeUser(${userId})">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    `;
                    container.appendChild(badge);
                }
            });
        }

        // Remove user from selection
        function removeUser(userId) {
            selectedUserIds = selectedUserIds.filter(id => id !== userId);
            const checkbox = document.querySelector(`input[value="${userId}"]`);
            if (checkbox) checkbox.checked = false;
            updateSelectedUsers();
            updateCreateButton();
        }

        // Update create button state
        function updateCreateButton() {
            const chatType = document.querySelector('input[name="type"]:checked').value;
            const coursePeriodId = document.getElementById('coursePeriodSelect').value;
            let isValid = false;
            
            if (chatType === 'group') {
                // Group chat requires course period
                isValid = coursePeriodId !== '';
            } else {
                // Direct chat requires at least one selected user
                isValid = selectedUserIds.length > 0;
            }
            
            console.log('🔘 Button validation - Chat type:', chatType, 'Course period:', coursePeriodId, 'Selected users:', selectedUserIds.length, 'Is valid:', isValid);
            
            createBtn.disabled = !isValid;
        }

        // Handle form submission
        // Ganti function handleFormSubmit dengan ini untuk debug lebih lengkap:

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    console.log('🚀 Submitting form...');
    
    const formData = new FormData(form);
    const chatType = formData.get('type');
    const coursePeriodId = formData.get('course_period_id') || null;
    
    let data = {
        type: chatType,
        course_period_id: coursePeriodId,
        name: chatType === 'group' ? formData.get('name') : null
    };

    if (chatType === 'group' && coursePeriodId) {
        // For group chat, get all users from course period
        data.participant_ids = users.map(u => u.id);
    } else {
        // For direct chat, use selected users
        data.participant_ids = selectedUserIds;
    }

    console.log('📤 Submitting data:', data);

    try {
        createBtn.disabled = true;
        createBtn.textContent = 'Creating...';

        const response = await fetch('/chats', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        console.log('📥 Create chat response status:', response.status);
        console.log('📥 Create chat response ok:', response.ok);
        console.log('📥 Create chat response headers:', Object.fromEntries(response.headers.entries()));

        // ✅ Get raw response text first
        const responseText = await response.text();
        console.log('📥 Raw response text length:', responseText.length);
        console.log('📥 Raw response text:', responseText);

        // ✅ Check if response is empty
        if (!responseText.trim()) {
            console.error('❌ Empty response from server');
            alert('Server returned empty response');
            return;
        }

        let result;
        try {
            result = JSON.parse(responseText);
            console.log('📥 Parsed result type:', typeof result);
            console.log('📥 Parsed result:', result);
            console.log('📥 Result keys:', Object.keys(result || {}));
        } catch (parseError) {
            console.error('❌ JSON parse error:', parseError);
            console.error('❌ Response text that failed to parse:', responseText);
            alert('Error parsing server response. Check console for details.');
            return;
        }

        if (response.ok) {
            console.log('✅ Response is OK (200-299)');
            
            // ✅ Debug response structure thoroughly
            console.log('🔍 Checking response structure...');
            console.log('🔍 result exists:', !!result);
            console.log('🔍 result.chat exists:', !!(result && result.chat));
            console.log('🔍 result.chat.id exists:', !!(result && result.chat && result.chat.id));
            
            if (result && result.chat) {
                console.log('✅ Chat object found in response');
                console.log('🔍 Chat object:', result.chat);
                console.log('🔍 Chat keys:', Object.keys(result.chat));
                
                if (result.chat.id) {
                    console.log('✅ Chat ID found:', result.chat.id);
                    console.log('🔄 Redirecting to chat...');
                    // Redirect to the new chat
                    window.location.href = `/chat/${result.chat.id}`;
                } else {
                    console.error('❌ Chat ID not found in chat object');
                    console.error('❌ Chat object contents:', result.chat);
                    alert('Chat created but ID is missing. Check console for details.');
                }
            } else {
                console.error('❌ Chat object not found in response');
                console.error('❌ Full response:', result);
                alert('Chat object missing from response. Check console for details.');
            }
        } else {
            console.error('❌ Response not OK. Status:', response.status);
            console.error('❌ Error response:', result);
            
            // Handle different error scenarios
            let errorMessage = 'Unknown error occurred';
            if (result) {
                if (result.message) {
                    errorMessage = result.message;
                } else if (result.error) {
                    errorMessage = result.error;
                } else if (result.errors) {
                    errorMessage = 'Validation errors: ' + JSON.stringify(result.errors);
                }
            }
            
            alert('Error creating chat: ' + errorMessage);
        }
    } catch (error) {
        console.error('❌ Network/Fetch error:', error);
        console.error('❌ Error stack:', error.stack);
        alert('Network error: ' + error.message);
    } finally {
        createBtn.disabled = false;
        createBtn.textContent = 'Create Chat';
    }
}

        // Auto-refresh chat list every 30 seconds
        setInterval(function() {
            if (window.location.pathname === '/chat') {
                window.location.reload();
            }
        }, 30000);
    </script>
    @endpush
</x-app-layout>