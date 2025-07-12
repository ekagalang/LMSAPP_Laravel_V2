<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Diskusi: {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Tinjau dan balas semua diskusi yang ada di kursus ini dari satu tempat.
                </p>
            </div>
            <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                &larr; Kembali ke Kursus
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($discussions->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-gray-500">Belum ada diskusi di kursus ini.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($discussions as $discussion)
                                <div class="p-5 rounded-lg border hover:shadow-md transition-shadow duration-300" x-data="{ showReplies: false }">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <a href="{{ route('contents.show', $discussion->content_id) }}#discussion-{{ $discussion->id }}" class="text-lg font-bold text-indigo-700 hover:underline">{{ $discussion->title }}</a>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Di materi: <span class="font-semibold">{{ $discussion->content->title }}</span> | 
                                                Oleh: <span class="font-semibold">{{ $discussion->user->name }}</span> | 
                                                {{ $discussion->created_at->diffForHumans() }}
                                            </p>
                                            <p class="mt-3 text-gray-700">{{ Str::limit($discussion->body, 200) }}</p>
                                        </div>
                                        <button @click="showReplies = !showReplies" class="flex-shrink-0 ml-4 text-sm font-medium text-indigo-600">
                                            <span x-text="showReplies ? 'Sembunyikan' : 'Lihat'"></span> Balasan ({{ $discussion->replies->count() }})
                                        </button>
                                    </div>

                                    <div x-show="showReplies" x-collapse class="mt-4 pt-4 border-t pl-6">
                                        @include('discussions.partials.replies', ['replies' => $discussion->replies, 'discussion' => $discussion])
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $discussions->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>