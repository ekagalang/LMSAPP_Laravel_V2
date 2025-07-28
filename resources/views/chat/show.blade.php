<x-app-layout>
    {{-- Atribut id dan data-* sudah ditambahkan kembali di sini --}}
    <div id="chat-container" data-chat-id="{{ $chat->id }}" data-user-id="{{ auth()->id() }}" class="flex flex-col h-[85vh] max-w-4xl mx-auto my-8 bg-white rounded-lg shadow-xl overflow-hidden border border-gray-200">
        
        {{-- Header Chat --}}
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">{{ $chat->getDisplayName() }}</h3>
            <div class="text-sm text-gray-500">
                @foreach($chat->activeParticipants as $participant)
                    {{ $participant->name }}{{ !$loop->last ? ',' : '' }}
                @endforeach
            </div>
        </div>

        {{-- Container Pesan --}}
        <div class="flex-grow p-4 space-y-4 overflow-y-auto bg-gray-50" id="messages-container">
            @foreach($messages as $message)
                <div class="flex flex-col {{ $message->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                    <div class="max-w-[70%]">
                        {{-- Header Pesan (Nama & Waktu) --}}
                        <div class="flex items-baseline gap-2 text-xs {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                             <span class="font-semibold {{ $message->user_id === auth()->id() ? 'text-gray-600' : 'text-gray-800' }}">
                                {{ $message->user->id === auth()->id() ? 'Anda' : $message->user->name }}
                            </span>
                            <span class="text-gray-400">
                                {{ $message->created_at->format('H:i') }}
                            </span>
                        </div>
                        {{-- Konten Pesan --}}
                        <div class="px-4 py-2 mt-1 break-words rounded-2xl {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white rounded-br-lg' : 'bg-gray-200 text-gray-800 rounded-bl-lg' }}">
                            {{ $message->content }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Indikator Mengetik --}}
        <div class="h-8 px-4 pt-2 text-sm italic text-gray-500 hidden" id="typing-indicator"></div>

        {{-- Input Pesan --}}
        <div class="flex items-center p-3 bg-white border-t border-gray-200">
            <textarea id="message-input" placeholder="Ketik pesan..." rows="1" class="flex-grow block w-full px-4 py-2 text-gray-700 bg-gray-100 border border-transparent rounded-full resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white"></textarea>
            <button id="send-button" class="flex items-center justify-center flex-shrink-0 w-10 h-10 ml-3 text-white bg-blue-500 rounded-full hover:bg-blue-600 focus:outline-none">
                {{-- pointer-events-none ditambahkan ke SVG --}}
                <svg class="w-6 h-6 transform rotate-90 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>


@push('scripts')
@vite(['resources/js/app.js'])

{{-- Skrip JavaScript Anda tidak perlu diubah, karena masalahnya ada di HTML --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.getElementById('chat-container');
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-button');
    const typingIndicator = document.getElementById('typing-indicator');

    const chatId = chatContainer.dataset.chatId;
    const authUserId = parseInt(chatContainer.dataset.userId);

    // Scroll ke bawah saat halaman dimuat
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    function appendMessage(message) {
        const isOwnMessage = message.user_id === authUserId;
        const messageTime = new Date(message.created_at);
        const now = new Date();
        const diffInSeconds = (now - messageTime) / 1000;
        const timestamp = diffInSeconds < 60 ? 'Baru saja' : messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const messageWrapper = document.createElement('div');
        messageWrapper.className = `flex flex-col ${isOwnMessage ? 'items-end' : 'items-start'}`;

        const messageBubble = `
            <div class="max-w-[70%]">
                <div class="flex items-baseline gap-2 text-xs ${isOwnMessage ? 'justify-end' : 'justify-start'}">
                    <span class="font-semibold ${isOwnMessage ? 'text-gray-600' : 'text-gray-800'}">
                        ${isOwnMessage ? 'Anda' : message.user.name}
                    </span>
                    <span class="text-gray-400">${timestamp}</span>
                </div>
                <div class="px-4 py-2 mt-1 break-words rounded-2xl ${isOwnMessage ? 'bg-blue-500 text-white rounded-br-lg' : 'bg-gray-200 text-gray-800 rounded-bl-lg'}">
                    ${message.content}
                </div>
            </div>
        `;

        messageWrapper.innerHTML = messageBubble;
        messagesContainer.appendChild(messageWrapper);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    const sendMessage = async () => {
        const content = messageInput.value.trim();
        if (!content) return;
        try {
            await fetch(`/chats/${chatId}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ content })
            });
            messageInput.value = '';
        } catch (error) {
            console.error('Error:', error);
        }
    };

    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // --- Fitur User Typing ---
    messageInput.addEventListener('input', () => {
        fetch(`/chats/${chatId}/typing`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ is_typing: true })
        });
    });

    let typingUsers = {};
    function updateTypingIndicator() {
        const users = Object.values(typingUsers).map(u => u.name);
        if (users.length === 0) {
            typingIndicator.classList.add('hidden');
        } else {
            typingIndicator.textContent = users.length === 1 ? `${users[0]} is typing...` : `${users.join(', ')} are typing...`;
            typingIndicator.classList.remove('hidden');
        }
    }

    Echo.private(`chat.${chatId}`)
        .listen('MessageSent', (e) => {
            if (typingUsers[e.message.user.id]) {
                clearTimeout(typingUsers[e.message.user.id].timer);
                delete typingUsers[e.message.user.id];
                updateTypingIndicator();
            }
            appendMessage(e.message);
        })
        .listen('UserTyping', (e) => {
            if (e.user.id !== authUserId) {
                if (typingUsers[e.user.id]) {
                    clearTimeout(typingUsers[e.user.id].timer);
                }
                typingUsers[e.user.id] = {
                    name: e.user.name,
                    timer: setTimeout(() => {
                        delete typingUsers[e.user.id];
                        updateTypingIndicator();
                    }, 3000)
                };
                updateTypingIndicator();
            }
        });
});
</script>
@endpush
</x-app-layout>