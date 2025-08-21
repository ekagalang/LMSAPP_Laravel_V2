{{-- resources/views/contents/partials/discussion-section.blade.php --}}
<div class="mt-8 pt-8 border-t" x-data="{ newTopic: false, activeReply: null }">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-2xl font-bold text-gray-800">Forum Diskusi</h3>
        <button @click="newTopic = !newTopic" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Mulai Topik Baru</button>
    </div>

    <div x-show="newTopic" x-collapse class="mb-6">
        <form action="{{ route('discussions.store', $content) }}" method="POST" class="bg-gray-50 p-4 rounded-lg">
            @csrf
            <div class="mb-3">
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Topik</label>
                <x-text-input id="title" name="title" class="w-full mt-1" required />
            </div>
            <div class="mb-3">
                <label for="body" class="block text-sm font-medium text-gray-700">Isi Pertanyaan</label>
                <textarea name="body" rows="4" class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
            </div>
            <div class="text-right">
                <x-secondary-button @click="newTopic = false">Batal</x-secondary-button>
                <x-primary-button type="submit" class="ml-2">Kirim</x-primary-button>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        @forelse ($content->discussions as $discussion)
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        {{-- Placeholder untuk avatar --}}
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center font-bold text-gray-600">
                            {{ strtoupper(substr($discussion->user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-grow">
                        <p class="font-bold text-gray-900">{{ $discussion->title }}</p>
                        <p class="text-sm text-gray-600">
                            Oleh <span class="font-semibold">{{ $discussion->user->name }}</span> - <span class="text-gray-400">{{ $discussion->created_at->diffForHumans() }}</span>
                        </p>
                    </div>
                    <button 
                        @click="activeReply === {{ $discussion->id }} ? activeReply = null : activeReply = {{ $discussion->id }}" 
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow hover:bg-indigo-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        Balas ({{ $discussion->replies->count() }})
                    </button>
                </div>

                <div class="mt-4 pl-14 prose prose-sm max-w-none text-gray-700">
                    <p>{{ $discussion->body }}</p>
                </div>

                <div x-show="activeReply === {{ $discussion->id }}" x-collapse class="mt-4 pl-14">
                    <div class="space-y-4">
                        @foreach ($discussion->replies as $reply)
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
                        @endforeach
                    </div>
                    <form action="{{ route('discussions.replies.store', $discussion) }}" method="POST" class="mt-4">
                        @csrf
                        <textarea name="body" rows="2" class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis balasan..."></textarea>
                        <div class="text-right mt-2">
                            <x-primary-button type="submit" class="text-xs">Kirim Balasan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-6">
                <p class="text-gray-500">Belum ada diskusi untuk materi ini. Jadilah yang pertama!</p>
            </div>
        @endforelse
    </div>
</div>