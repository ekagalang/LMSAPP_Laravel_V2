<div class="space-y-4">
    @forelse ($replies as $reply)
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500 text-sm">
                     {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-grow bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-800">{{ $reply->user->name }}</span> - <span class="text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                </p>
                <p class="mt-1 text-sm text-gray-700">{{ $reply->body }}</p>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500">Belum ada balasan.</p>
    @endforelse
</div>

<form action="{{ route('discussions.replies.store', $discussion) }}" method="POST" class="mt-5">
    @csrf
    <textarea name="body" rows="2" class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis balasan cepat..."></textarea>
    <div class="text-right mt-2">
        <x-primary-button type="submit" class="text-xs">Kirim Balasan</x-primary-button>
    </div>
</form>