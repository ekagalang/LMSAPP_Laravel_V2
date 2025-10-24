<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(isset($announcements) && $announcements->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengumuman Terbaru</h3>
                        <div class="space-y-3">
                            @foreach($announcements as $announcement)
                                <div class="p-4 rounded-lg border bg-gray-50">
                                    <h4 class="font-bold">{{ $announcement->title }}</h4>
                                    <p class="text-sm mt-1">{{ $announcement->content }}</p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Diposting oleh {{ $announcement->user->name }} pada {{ $announcement->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Kursus Diikuti</p>
                    <p class="text-2xl font-bold">{{ isset($stats['courses']['total']) ? $stats['courses']['total'] : 0 }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Progress Rata-rata</p>
                    <p class="text-2xl font-bold">{{ isset($stats['courses']['overall_progress']) ? $stats['courses']['overall_progress'] : 0 }}%</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Diskusi</p>
                    <p class="text-2xl font-bold">{{ isset($stats['discussions']['total']) ? $stats['discussions']['total'] : (isset($stats['discussions']['started']) ? $stats['discussions']['started'] : 0) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Kuis Selesai</p>
                    <p class="text-2xl font-bold">{{ isset($stats['quizzes']['completed']) ? $stats['quizzes']['completed'] : 0 }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Akses Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @can('view courses')
                            <a href="{{ route('courses.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Kursus</div>
                                <div class="text-sm text-gray-500">Lihat daftar kursus</div>
                            </a>
                        @endcan
                        @can('view instructor analytics')
                            <a href="{{ route('instructor-analytics.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Analitik Instruktur</div>
                                <div class="text-sm text-gray-500">Lihat kinerja instruktur</div>
                            </a>
                        @endcan
                        @can('view certificate management')
                            <a href="{{ route('certificate-management.analytics') }}" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Sertifikat</div>
                                <div class="text-sm text-gray-500">Analitik & manajemen</div>
                            </a>
                        @endcan
                        @can('manage users')
                            <a href="{{ route('admin.users.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Pengguna</div>
                                <div class="text-sm text-gray-500">Kelola pengguna & peran</div>
                            </a>
                        @endcan
                        <a href="{{ route('announcements.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50">
                            <div class="font-semibold">Pengumuman</div>
                            <div class="text-sm text-gray-500">Lihat semua pengumuman</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

