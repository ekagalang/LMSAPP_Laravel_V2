@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">✨ Refleksi Diri</h1>
            <p class="text-gray-600">Tuliskan refleksi pembelajaran dan pengalaman Anda</p>
        </div>

        <a href="{{ route('reflections.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tulis Refleksi Baru
        </a>
    </div>

    @if(Auth::user()->hasRole(['super-admin', 'instructor']))
        <!-- Filter untuk Instructor -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reflections.index', ['filter' => 'my']) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ $filter === 'my' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Refleksi Saya
                </a>
                <a href="{{ route('reflections.index', ['filter' => 'needs_response']) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ $filter === 'needs_response' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Perlu Respon
                </a>
                <a href="{{ route('reflections.index', ['filter' => 'all']) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ $filter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Semua Refleksi
                </a>
                <a href="{{ route('reflections.index', ['filter' => 'public']) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ $filter === 'public' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Public
                </a>
                <a href="{{ route('reflections.analytics') }}"
                   class="px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-orange-500 to-yellow-500 text-white hover:from-orange-600 hover:to-yellow-600 transition-colors">
                    📊 Analytics
                </a>
            </div>
        </div>
    @endif

    <!-- Reflections Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($reflections as $reflection)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-l-4
                        {{ $reflection->needsResponse() ? 'border-red-500' : 'border-purple-500' }}">

                <!-- Header dengan Mood dan Tanggal -->
                <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">{{ $reflection->mood_emoji }}</span>
                            <span class="text-xs text-gray-500">{{ $reflection->mood_label }}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $reflection->created_at->diffForHumans() }}
                        </div>
                    </div>

                    <!-- Author Info (untuk instructor) -->
                    @if(Auth::user()->hasRole(['super-admin', 'instructor']) && $reflection->user_id !== Auth::id())
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs">
                                {{ substr($reflection->user->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $reflection->user->name }}</span>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $reflection->title }}</h3>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                        {{ Str::limit($reflection->content, 150) }}
                    </p>

                    <!-- Tags -->
                    @if($reflection->tags && count($reflection->tags) > 0)
                        <div class="flex flex-wrap gap-1 mb-4">
                            @foreach(array_slice($reflection->tags, 0, 3) as $tag)
                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                            @if(count($reflection->tags) > 3)
                                <span class="text-xs text-gray-500">+{{ count($reflection->tags) - 3 }} lainnya</span>
                            @endif
                        </div>
                    @endif

                    <!-- Status Badges -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <!-- Visibility Badge -->
                            <span class="text-xs px-2 py-1 rounded-full
                                        {{ $reflection->visibility === 'public' ? 'bg-green-100 text-green-800' :
                                           ($reflection->visibility === 'private' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $reflection->visibility_label }}
                            </span>

                            <!-- Response Status -->
                            @if($reflection->requires_response)
                                @if($reflection->hasResponse())
                                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                                        ✅ Direspon
                                    </span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">
                                        ⏳ Perlu Respon
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Response Preview -->
                    @if($reflection->hasResponse())
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l1.405-5.094A8.959 8.959 0 013 12a8 8 0 018-8 8 8 0 018 8z"></path>
                                </svg>
                                <span class="text-xs font-medium text-blue-800">
                                    Respon dari {{ $reflection->respondedBy->name }}
                                </span>
                            </div>
                            <p class="text-sm text-blue-700">
                                {{ Str::limit($reflection->instructor_response, 100) }}
                            </p>
                        </div>
                    @endif

                    <!-- Action Button -->
                    <a href="{{ route('reflections.show', $reflection) }}"
                       class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Baca Selengkapnya
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada refleksi</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Mulai dengan menulis refleksi pertama Anda tentang pengalaman pembelajaran.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('reflections.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tulis Refleksi
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reflections->hasPages())
        <div class="mt-8">
            {{ $reflections->links() }}
        </div>
    @endif
</div>
@endsection