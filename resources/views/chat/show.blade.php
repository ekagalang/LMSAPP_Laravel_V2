<x-app-layout>
<div class="chat-container" id="chat-container" data-chat-id="{{ $chat->id }}" data-user-id="{{ auth()->id() }}">
    <div class="chat-header">
        <h3>{{ $chat->getDisplayName() }}</h3>
        <div class="participants">
            @foreach($chat->activeParticipants as $participant)
                <span class="participant-badge">{{ $participant->name }}</span>
            @endforeach
        </div>
    </div>

    <div class="messages-container" id="messages-container">
        @foreach($messages as $message)
            <div class="message {{ $message->user_id === auth()->id() ? 'own' : 'other' }}">
                <div class="message-header">
                    <span class="username">{{ $message->user->name }}</span>
                    <span class="timestamp">{{ $message->created_at->format('H:i') }}</span>
                </div>
                <div class="message-content">{{ $message->content }}</div>
            </div>
        @endforeach
    </div>

    <div class="typing-indicator" id="typing-indicator" style="display: none;"></div>

    <div class="message-input-container">
        <textarea id="message-input" placeholder="Type a message..." rows="1"></textarea>
        <button id="send-button">Send</button>
    </div>
</div>

@push('scripts')
@vite(['resources/js/app.js'])

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sendBtn = document.getElementById('send-button');
    const messageInput = document.getElementById('message-input');
    const chatContainer = document.getElementById('chat-container');
    const messagesContainer = document.getElementById('messages-container');

    const chatId = chatContainer.dataset.chatId;

    sendBtn.addEventListener('click', async () => {
        const content = messageInput.value.trim();
        if (!content) return;

        try {
            const response = await fetch(`/chats/${chatId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            });

            if (response.ok) {
                const result = await response.json();

                // Tambahkan pesan ke container
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message', 'own');
                messageDiv.innerHTML = `
                    <div class="message-header">
                        <span class="username">${result.message.user.name}</span>
                        <span class="timestamp">baru saja</span>
                    </div>
                    <div class="message-content">${result.message.content}</div>
                `;
                messagesContainer.appendChild(messageDiv);

                messageInput.value = '';
            } else {
                console.error('Gagal kirim pesan');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endpush
</x-app-layout>
