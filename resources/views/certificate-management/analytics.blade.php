<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 -mx-4 -my-2 px-4 py-8 sm:px-6 lg:px-8 rounded-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-white text-3xl font-bold leading-tight">
                        {{ __('Analytics Sertifikat') }}
                    </h2>
                    <p class="text-blue-100 mt-2">
                        {{ __('Dashboard analytics untuk pembuatan dan penggunaan sertifikat') }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('certificate-management.index') }}" 
                       class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Main Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📜</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-blue-100 text-sm font-medium">Total Sertifikat</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['total_certificates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🎓</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-green-100 text-sm font-medium">Kursus dengan Sertifikat</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['total_courses_with_certificates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📋</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-purple-100 text-sm font-medium">Total Template</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['total_templates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-yellow-100 text-sm font-medium">Bulan Ini</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['certificates_this_month']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Period Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-emerald-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Hari Ini</div>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($analytics['certificates_today']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📈</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Minggu Ini</div>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($analytics['certificates_this_week']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📊</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Bulan Ini</div>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($analytics['certificates_this_month']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Statistics Chart -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistik Bulanan</h3>
                    <p class="text-sm text-gray-500 mt-1">Jumlah sertifikat yang dibuat per bulan (12 bulan terakhir)</p>
                </div>
                <div class="p-6">
                    @if($monthlyStats->count() > 0)
                        <div class="space-y-3">
                            @foreach($monthlyStats as $stat)
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::createFromDate($stat->year, $stat->month, 1)->format('M Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($stat->count / $monthlyStats->max('count')) * 100 }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $stat->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">📊</div>
                            <p>Belum ada data statistik bulanan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Kursus Terpopuler</h3>
                    <p class="text-sm text-gray-500 mt-1">10 kursus dengan sertifikat terbanyak</p>
                </div>
                <div class="p-6">
                    @if($courseStats->count() > 0)
                        <div class="space-y-4">
                            @foreach($courseStats as $index => $course)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $course->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $course->certificates_count }} sertifikat</div>
                                    </div>
                                    <div class="text-right">
                                        <a href="{{ route('certificate-management.by-course', $course) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Lihat →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">🎓</div>
                            <p>Belum ada data kursus</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Template Usage Statistics -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Penggunaan Template</h3>
                <p class="text-sm text-gray-500 mt-1">Statistik penggunaan template sertifikat</p>
            </div>
            <div class="p-6">
                @if($templateStats->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($templateStats as $template)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $template->name }}
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $template->certificates_count }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full" 
                                         style="width: {{ ($template->certificates_count / $templateStats->max('certificates_count')) * 100 }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ number_format(($template->certificates_count / $analytics['total_certificates']) * 100, 1) }}% dari total
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-12">
                        <div class="text-6xl mb-4">📋</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada template yang digunakan</h3>
                        <p class="text-sm text-gray-500">Template akan muncul di sini setelah digunakan untuk membuat sertifikat</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>