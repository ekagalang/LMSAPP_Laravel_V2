@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">📊 Analytics Refleksi</h1>
                <p class="text-gray-600">Insight dan statistik dari refleksi participant</p>
            </div>
            <a href="{{ route('reflections.index') }}"
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Refleksi
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Refleksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_reflections'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V17a2 2 0 01-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v0"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Perlu Respon</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['needs_response'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Mood</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            $moodValues = ['very_sad' => 1, 'sad' => 2, 'neutral' => 3, 'happy' => 4, 'very_happy' => 5];
                            $totalMood = 0;
                            $totalCount = array_sum($stats['mood_distribution']);

                            foreach ($stats['mood_distribution'] as $mood => $count) {
                                $totalMood += ($moodValues[$mood] ?? 3) * $count;
                            }

                            $averageMood = $totalCount > 0 ? $totalMood / $totalCount : 3;
                            $averageMoodEmoji = match(round($averageMood)) {
                                1 => '😢',
                                2 => '😔',
                                3 => '😐',
                                4 => '😊',
                                5 => '😄',
                                default => '😐'
                            };
                        @endphp
                        {{ $averageMoodEmoji }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Mood Distribution Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Mood</h3>
            @if(array_sum($stats['mood_distribution']) > 0)
                <div class="space-y-4">
                    @foreach(['very_happy' => ['😄', 'Sangat Senang', 'bg-green-500'], 'happy' => ['😊', 'Senang', 'bg-blue-500'], 'neutral' => ['😐', 'Netral', 'bg-gray-500'], 'sad' => ['😔', 'Sedih', 'bg-orange-500'], 'very_sad' => ['😢', 'Sangat Sedih', 'bg-red-500']] as $mood => $config)
                        @php
                            $count = $stats['mood_distribution'][$mood] ?? 0;
                            $percentage = array_sum($stats['mood_distribution']) > 0 ?
                                ($count / array_sum($stats['mood_distribution'])) * 100 : 0;
                        @endphp
                        <div class="flex items-center space-x-4">
                            <span class="text-2xl">{{ $config[0] }}</span>
                            <div class="flex-1">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium">{{ $config[1] }}</span>
                                    <span class="text-gray-600">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $config[2] }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Belum ada data mood</p>
                </div>
            @endif
        </div>

        <!-- Recent Reflections -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Refleksi Terbaru</h3>
            @if($stats['recent_reflections']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['recent_reflections'] as $reflection)
                        <div class="border-l-4 border-purple-500 pl-4 py-2">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">{{ Str::limit($reflection->title, 40) }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $reflection->user->name }}</p>
                                    <div class="flex items-center space-x-2 mt-2">
                                        @if($reflection->mood)
                                            <span class="text-lg">{{ $reflection->mood_emoji }}</span>
                                        @endif
                                        @if($reflection->requires_response && !$reflection->hasResponse())
                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full">
                                                Perlu Respon
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $reflection->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <a href="{{ route('reflections.show', $reflection) }}"
                               class="text-sm text-purple-600 hover:text-purple-800 mt-2 inline-block">
                                Lihat detail →
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p>Belum ada refleksi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('reflections.index', ['filter' => 'needs_response']) }}"
               class="flex items-center justify-between p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="font-medium text-red-800">Respon Refleksi</span>
                </div>
                <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full">{{ $stats['needs_response'] }}</span>
            </a>

            <a href="{{ route('reflections.index', ['filter' => 'all']) }}"
               class="flex items-center justify-between p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span class="font-medium text-blue-800">Lihat Semua</span>
                </div>
                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">{{ $stats['total_reflections'] }}</span>
            </a>

            <a href="{{ route('reflections.create') }}"
               class="flex items-center justify-between p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-medium text-purple-800">Tulis Refleksi</span>
                </div>
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection