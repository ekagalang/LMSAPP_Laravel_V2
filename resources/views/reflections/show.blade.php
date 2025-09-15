@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('reflections.index') }}"
                   class="text-purple-600 hover:text-purple-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">✨ Detail Refleksi</h1>
                    <p class="text-gray-600">{{ $reflection->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            @if($reflection->user_id === Auth::id())
                <div class="flex space-x-2">
                    <a href="{{ route('reflections.edit', $reflection) }}"
                       class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('reflections.destroy', $reflection) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Yakin ingin menghapus refleksi ini?')"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Reflection Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header dengan Mood dan Meta Info -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    @if($reflection->mood)
                        <div class="flex items-center space-x-2">
                            <span class="text-4xl">{{ $reflection->mood_emoji }}</span>
                            <span class="text-sm font-medium text-gray-600">{{ $reflection->mood_label }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Visibility Badge -->
                    <span class="px-3 py-1 text-sm rounded-full font-medium
                                {{ $reflection->visibility === 'public' ? 'bg-green-100 text-green-800' :
                                   ($reflection->visibility === 'private' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $reflection->visibility_label }}
                    </span>

                    <!-- Response Status -->
                    @if($reflection->requires_response)
                        @if($reflection->hasResponse())
                            <span class="px-3 py-1 text-sm rounded-full font-medium bg-green-100 text-green-800">
                                ✅ Direspon
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm rounded-full font-medium bg-red-100 text-red-800">
                                ⏳ Perlu Respon
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Author Info (untuk instructor view) -->
            @if(Auth::user()->hasRole(['super-admin', 'instructor']) && $reflection->user_id !== Auth::id())
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr($reflection->user->name, 0, 1) }}
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">{{ $reflection->user->name }}</span>
                        <p class="text-sm text-gray-600">{{ $reflection->user->email }}</p>
                    </div>
                </div>
            @endif

            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $reflection->title }}</h1>

            <!-- Tags -->
            @if($reflection->tags && count($reflection->tags) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($reflection->tags as $tag)
                        <span class="inline-block bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                            #{{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="prose max-w-none">
                <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $reflection->content }}</div>
            </div>
        </div>

        <!-- Instructor Response Section -->
        @if($reflection->hasResponse() || (Auth::user()->hasRole(['super-admin', 'instructor']) && $reflection->requires_response))
            <div class="border-t border-gray-200 p-6">
                @if($reflection->hasResponse())
                    <!-- Existing Response -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l1.405-5.094A8.959 8.959 0 013 12a8 8 0 018-8 8 8 0 018 8z"></path>
                                </svg>
                                <span class="font-semibold text-blue-800">Respon dari {{ $reflection->respondedBy->name }}</span>
                            </div>
                            <div class="text-sm text-blue-600">
                                {{ $reflection->responded_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-blue-800 leading-relaxed whitespace-pre-line">{{ $reflection->instructor_response }}</div>

                        @if(Auth::user()->hasRole(['super-admin', 'instructor']))
                            <div class="mt-4 flex space-x-2">
                                <button onclick="showEditResponseForm()"
                                        class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    Edit Respon
                                </button>
                                <form action="{{ route('reflections.remove-response', $reflection) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus respon ini?')"
                                            class="text-sm px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                        Hapus Respon
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Edit Response Form (Hidden by default) -->
                    @if(Auth::user()->hasRole(['super-admin', 'instructor']))
                        <div id="edit-response-form" class="hidden mt-4">
                            <form action="{{ route('reflections.respond', $reflection) }}" method="POST">
                                @csrf
                                <textarea name="instructor_response" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Berikan respon atau feedback untuk refleksi ini...">{{ $reflection->instructor_response }}</textarea>
                                <div class="mt-3 flex space-x-2">
                                    <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Update Respon
                                    </button>
                                    <button type="button" onclick="hideEditResponseForm()"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                @elseif(Auth::user()->hasRole(['super-admin', 'instructor']) && $reflection->requires_response)
                    <!-- New Response Form -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <div class="flex items-center space-x-2 mb-3">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="font-semibold text-yellow-800">Respon diperlukan</span>
                        </div>
                        <p class="text-yellow-700 mb-4">Participant meminta respon dari instruktur untuk refleksi ini.</p>

                        <form action="{{ route('reflections.respond', $reflection) }}" method="POST">
                            @csrf
                            <textarea name="instructor_response" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Berikan respon atau feedback untuk refleksi ini..." required></textarea>
                            <button type="submit"
                                    class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kirim Respon
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Meta Information -->
    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Informasi</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V17a2 2 0 01-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v0"></path>
                </svg>
                <span class="text-gray-600">Dibuat:</span>
                <span class="font-medium">{{ $reflection->created_at->format('d M Y, H:i') }}</span>
            </div>
            @if($reflection->updated_at != $reflection->created_at)
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="text-gray-600">Diupdate:</span>
                    <span class="font-medium">{{ $reflection->updated_at->format('d M Y, H:i') }}</span>
                </div>
            @endif
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <span class="text-gray-600">Penulis:</span>
                <span class="font-medium">{{ $reflection->user->name }}</span>
            </div>
        </div>
    </div>
</div>

<script>
function showEditResponseForm() {
    document.getElementById('edit-response-form').classList.remove('hidden');
}

function hideEditResponseForm() {
    document.getElementById('edit-response-form').classList.add('hidden');
}
</script>
@endsection