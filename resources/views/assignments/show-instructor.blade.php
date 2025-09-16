<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $assignment->title }}
                </h2>
                <div class="flex items-center space-x-2 mt-2">
                    @if($assignment->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                            Tidak Aktif
                        </span>
                    @endif

                    @if(!$assignment->show_to_students)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Tersembunyi
                        </span>
                    @endif

                    @if($assignment->due_date)
                        @if($assignment->due_date->isPast())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1"></div>
                                Terlambat
                            </span>
                        @elseif($assignment->due_date->diffInDays() <= 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1"></div>
                                Segera
                            </span>
                        @endif
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('assignments.edit', $assignment) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('assignments.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Assignment Details -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Detail Tugas
                            </h3>
                        </div>
                        <div class="px-6 py-4 space-y-6">
                            @if($assignment->description)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                        </svg>
                                        Deskripsi
                                    </h4>
                                    <p class="text-gray-600 bg-gray-50 p-3 rounded-md">{{ $assignment->description }}</p>
                                </div>
                            @endif

                            @if($assignment->instructions)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Instruksi
                                    </h4>
                                    <div class="text-gray-700 bg-blue-50 p-4 rounded-md border-l-4 border-blue-500">
                                        {{ $assignment->instructions }}
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Tipe Pengumpulan
                                    </h4>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        @if($assignment->submission_type === 'file')
                                            📁 File saja
                                        @elseif($assignment->submission_type === 'link')
                                            🔗 Link saja
                                        @else
                                            📁🔗 File atau Link
                                        @endif
                                    </span>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        Poin Maksimal
                                    </h4>
                                    <p class="text-2xl font-bold text-blue-600">{{ $assignment->max_points }}</p>
                                </div>
                            </div>

                            @if($assignment->submission_type === 'file' || $assignment->submission_type === 'both')
                                <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Pengaturan File
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-600 w-32">Maksimal file:</span>
                                            <span class="text-gray-900">{{ $assignment->max_files ?? 1 }}</span>
                                        </div>
                                        @if($assignment->max_file_size)
                                            <div class="flex items-center text-sm">
                                                <span class="font-medium text-gray-600 w-32">Ukuran maksimal:</span>
                                                <span class="text-gray-900">{{ $assignment->getFileSizeFormatted() }}</span>
                                            </div>
                                        @endif
                                        @if($assignment->allowed_file_types)
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-600">Tipe file yang diizinkan:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($assignment->allowed_file_types as $type)
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ strtoupper($type) }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submissions -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Pengumpulan ({{ $submissions->count() }})
                            </h3>
                            <div class="relative">
                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        onclick="toggleDropdown()">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Ekspor
                                    <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Excel
                                        </a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden">
                            @if($submissions->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pengumpulan</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($submissions as $submission)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-white">{{ substr($submission->user->name, 0, 1) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">{{ $submission->user->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $submission->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($submission->submitted_at)
                                                        <div class="text-sm text-gray-900">{{ $submission->submitted_at->format('d M Y H:i') }}</div>
                                                        @if($assignment->due_date && $submission->submitted_at->gt($assignment->due_date))
                                                            <div class="text-xs text-red-600">Terlambat</div>
                                                        @endif
                                                    @else
                                                        <span class="text-sm text-gray-500">Draft</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($submission->status === 'graded')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            Dinilai
                                                        </span>
                                                    @elseif($submission->status === 'submitted')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Menunggu
                                                        </span>
                                                    @elseif($submission->status === 'returned')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            Dikembalikan
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Draft
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($submission->points_earned !== null)
                                                        <div class="text-sm font-medium text-gray-900">{{ $submission->points_earned }}/{{ $assignment->max_points }}</div>
                                                        <div class="text-xs text-gray-500">{{ number_format($submission->grade, 1) }}%</div>
                                                    @else
                                                        <span class="text-sm text-gray-500">Belum dinilai</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('assignments.submissions.show', [$assignment, $submission]) }}"
                                                       class="text-blue-600 hover:text-blue-900 transition-colors">
                                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada pengumpulan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Siswa belum mengumpulkan tugas ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Statistik
                            </h3>
                        </div>
                        <div class="px-6 py-4">
                            @php
                                $totalSubmissions = $submissions->count();
                                $submittedCount = $submissions->where('status', '!=', 'draft')->count();
                                $gradedCount = $submissions->where('status', 'graded')->count();
                                $averageGrade = $submissions->where('status', 'graded')->avg('grade');
                            @endphp

                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $totalSubmissions }}</div>
                                    <div class="text-xs text-gray-500">Total Pengumpulan</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $submittedCount }}</div>
                                    <div class="text-xs text-gray-500">Dikumpulkan</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ $gradedCount }}</div>
                                    <div class="text-xs text-gray-500">Dinilai</div>
                                </div>
                                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">
                                        {{ $averageGrade ? number_format($averageGrade, 1) . '%' : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Rata-rata Nilai</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Info -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Jadwal
                            </h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            @if($assignment->due_date)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <h4 class="text-sm font-semibold text-gray-700">Tenggat Waktu</h4>
                                    <p class="text-sm text-gray-900 font-medium">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                                    @if($assignment->due_date->isPast())
                                        <p class="text-xs text-red-600">Sudah lewat</p>
                                    @else
                                        <p class="text-xs text-green-600">{{ $assignment->due_date->diffForHumans() }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($assignment->allow_late_submission && $assignment->late_submission_until)
                                <div class="border-l-4 border-yellow-500 pl-4">
                                    <h4 class="text-sm font-semibold text-gray-700">Batas Akhir Terlambat</h4>
                                    <p class="text-sm text-gray-900 font-medium">{{ $assignment->late_submission_until->format('d M Y H:i') }}</p>
                                    @if($assignment->late_penalty > 0)
                                        <p class="text-xs text-yellow-600">Penalti: {{ $assignment->late_penalty }}%</p>
                                    @endif
                                </div>
                            @endif

                            <div class="border-l-4 border-gray-500 pl-4">
                                <h4 class="text-sm font-semibold text-gray-700">Dibuat</h4>
                                <p class="text-sm text-gray-900 font-medium">{{ $assignment->created_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-gray-500">oleh {{ $assignment->creator->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Aksi Cepat
                            </h3>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <a href="{{ route('assignments.edit', $assignment) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Tugas
                            </a>

                            @if($assignment->is_active)
                                <form action="{{ route('assignments.update', $assignment) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_active" value="0">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Nonaktifkan
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('assignments.update', $assignment) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M9 10v4a2 2 0 002 2h2a2 2 0 002-2v-4M9 10V9a2 2 0 012-2h2a2 2 0 012 2v1"></path>
                                        </svg>
                                        Aktifkan
                                    </button>
                                </form>
                            @endif

                            <button type="button" onclick="confirmDelete({{ $assignment->id }})"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus Tugas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed top-4 right-4 z-50 max-w-sm w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed.top-4.right-4')?.remove();
        }, 5000);
    </script>
    @endif

    @push('scripts')
    <script>
        function confirmDelete(assignmentId) {
            if (confirm('Apakah Anda yakin ingin menghapus tugas ini? Semua pengumpulan akan ikut terhapus dan tidak dapat dikembalikan.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/assignments/${assignmentId}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('exportDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('exportDropdown');
            const button = event.target.closest('button');

            if (!button || button.onclick !== toggleDropdown) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>