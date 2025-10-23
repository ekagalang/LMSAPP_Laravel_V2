<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- [BARU] Bagian Pengumuman -->
            @if(isset($announcements) && $announcements->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Pengumuman Terbaru</h3>
                    <div class="space-y-4">
                        @foreach($announcements as $announcement)
                            @php
                                $levelClasses = [
                                    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
                                    'success' => 'bg-green-50 border-green-200 text-green-800',
                                    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                                    'danger' => 'bg-red-50 border-red-200 text-red-800',
                                ];
                                $class = $levelClasses[$announcement->level] ?? $levelClasses['info'];
                            @endphp
                            <div class="p-4 rounded-lg border {{ $class }}">
                                <h4 class="font-bold">{{ $announcement->title }}</h4>
                                <p class="text-sm mt-1">{{ $announcement->content }}</p>
                                <p class="text-xs text-gray-500 mt-2">
                                    Diposting oleh {{ $announcement->user->name }} pada {{ $announcement->created_at->format('d M Y') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Konten Dasbor Berdasarkan Peran -->
            @php
                $user = Auth::user();
            @endphp

            @if (isset($stats) && Gate::check('admin-only'))
                @include('dashboard.admin', ['stats' => $stats])
            @elseif (isset($stats) && $user->can('manage own courses'))
                @include('dashboard.instructor', ['stats' => $stats])
            @elseif (isset($stats) && ($user->can('view progress reports') || $user->can('view certificate management')))
                @include('dashboard.eo', ['stats' => $stats])
            @elseif (isset($stats) && $user->can('attempt quizzes'))
                @include('dashboard.participant', ['stats' => $stats])
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
