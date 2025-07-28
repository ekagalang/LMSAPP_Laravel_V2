<!-- resources/views/chat/show.blade.php -->

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
<script src="{{ asset('js/echo.js') }}"></script>
<script src="{{ asset('js/components/ChatComponent.js') }}"></script>
@endpush
</x-app-layout>
