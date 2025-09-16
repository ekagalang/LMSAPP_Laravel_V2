@extends('layouts.app')

@section('title', 'Detail Pengumpulan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Modern Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div class="flex items-start space-x-4">
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-3 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Pengumpulan</h1>
                    <p class="text-gray-600 mt-1 font-medium">{{ $assignment->title }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if($isInstructor)
                    <a href="{{ route('assignments.show', $assignment) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Tugas
                    </a>
                @else
                    @if($submission->canEdit())
                        @if($submission->status === 'draft' && $assignment->canSubmit())
                            <form action="{{ route('assignments.submissions.submit', [$assignment, $submission]) }}" method="POST" class="inline-block mr-2" onsubmit="return confirm('Apakah Anda yakin ingin mengumpulkan tugas ini? Setelah dikumpulkan, Anda tidak dapat mengubahnya lagi.')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:shadow-lg transform hover:scale-105 transition-all duration-200 shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kumpulkan
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('assignments.submissions.create', $assignment) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('assignments.show', $assignment) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div id="success-toast" class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Submission Details -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Detail Pengumpulan</h3>
                            </div>
                            <div>
                                @if($submission->status === 'graded')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Dinilai
                                    </span>
                                @elseif($submission->status === 'submitted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                        </svg>
                                        Dikumpulkan
                                    </span>
                                @elseif($submission->status === 'returned')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                        Draft
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <!-- Student Info -->
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-blue-700 mb-2 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Siswa
                                </h6>
                                <p class="font-semibold text-gray-800">{{ $submission->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $submission->user->email }}</p>
                            </div>
                            <!-- Submission Time -->
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-green-700 mb-2 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Waktu Pengumpulan
                                </h6>
                                @if($submission->submitted_at)
                                    <p class="font-semibold text-gray-800">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                                    @if($assignment->due_date && $submission->submitted_at->gt($assignment->due_date))
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Tepat Waktu
                                        </span>
                                    @endif
                                @else
                                    <p class="text-gray-500 font-medium">Belum dikumpulkan</p>
                                @endif
                            </div>
                            <!-- Attempt Number -->
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-purple-700 mb-2 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                    Percobaan
                                </h6>
                                <p class="text-xl font-bold text-purple-600">#{{ $submission->attempt_number }}</p>
                            </div>
                            <!-- Last Updated -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Terakhir Diupdate
                                </h6>
                                <p class="font-semibold text-gray-800">{{ $submission->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        @if($submission->submission_text)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Teks Pengumpulan
                                </h6>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                    <p class="text-gray-800 leading-relaxed">{{ $submission->submission_text }}</p>
                                </div>
                            </div>
                        @endif

                        @if($submission->submission_link)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    Link Pengumpulan
                                </h6>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $submission->submission_link }}</p>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ $submission->submission_link }}" target="_blank"
                                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                Buka Link
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($submission->file_paths)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    File yang Diunggah
                                </h6>
                                <div class="space-y-3">
                                    @foreach($submission->file_paths as $index => $filePath)
                                        @php
                                            $metadata = $submission->file_metadata[$index] ?? [];
                                            $fileName = $metadata['original_name'] ?? basename($filePath);
                                            $fileSize = isset($metadata['size']) ? round($metadata['size'] / 1024, 1) . ' KB' : '';
                                            $mimeType = $metadata['mime_type'] ?? '';
                                        @endphp
                                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center flex-1 min-w-0">
                                                    <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="font-semibold text-gray-800 truncate">{{ $fileName }}</p>
                                                        <div class="flex items-center space-x-3 mt-1">
                                                            @if($fileSize)
                                                                <span class="text-sm text-gray-600">{{ $fileSize }}</span>
                                                            @endif
                                                            @if($mimeType)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ strtoupper(explode('/', $mimeType)[1] ?? '') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="{{ route('assignments.submissions.download', [$assignment, $submission, $index]) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200 shadow-md hover:shadow-lg ml-4">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 13.5l5 5 5-5" />
                                                    </svg>
                                                    Unduh
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Grading Section (Instructor Only) -->
                @if($isInstructor && $submission->status !== 'draft')
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Penilaian</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($submission->status === 'graded')
                                <!-- Show Existing Grade -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div class="bg-green-50 p-4 rounded-lg text-center">
                                        <h6 class="text-sm font-semibold text-green-700 mb-2 uppercase tracking-wide">Nilai</h6>
                                        <div class="text-3xl font-bold text-green-600 mb-1">{{ $submission->points_earned }}/{{ $assignment->max_points }}</div>
                                        <p class="text-sm text-green-600 font-medium">{{ number_format($submission->grade, 1) }}%</p>
                                    </div>
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h6 class="text-sm font-semibold text-blue-700 mb-2 uppercase tracking-wide">Dinilai oleh</h6>
                                        <p class="font-semibold text-gray-800">{{ $submission->grader->name ?? 'Unknown' }}</p>
                                        <p class="text-sm text-gray-600">{{ $submission->graded_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <h6 class="text-sm font-semibold text-purple-700 mb-2 uppercase tracking-wide">Status</h6>
                                        @if($submission->status === 'graded')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Dinilai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                                </svg>
                                                Dikembalikan
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($submission->instructor_feedback)
                                    <div class="mb-6">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            Feedback
                                        </h6>
                                        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                                            <p class="text-gray-800 leading-relaxed">{{ $submission->instructor_feedback }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Edit Grade Form -->
                                <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg" type="button" onclick="toggleEditForm()">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Nilai
                                </button>

                                <div class="hidden mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6" id="editGradeForm">
                                    <form action="{{ route('assignments.submissions.grade', [$assignment, $submission]) }}" method="POST">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                            <div>
                                                <label for="edit_points_earned" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Poin yang Diperoleh</label>
                                                <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" id="edit_points_earned" name="points_earned"
                                                       value="{{ $submission->points_earned }}" min="0" max="{{ $assignment->max_points }}" required>
                                            </div>
                                            <div>
                                                <label for="edit_status" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Status</label>
                                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" id="edit_status" name="status" required>
                                                    <option value="graded" {{ $submission->status === 'graded' ? 'selected' : '' }}>Dinilai</option>
                                                    <option value="returned" {{ $submission->status === 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label for="edit_instructor_feedback" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Feedback</label>
                                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" id="edit_instructor_feedback" name="instructor_feedback" rows="4">{{ $submission->instructor_feedback }}</textarea>
                                        </div>
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            Update Nilai
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- Grade Form -->
                                <form action="{{ route('assignments.submissions.grade', [$assignment, $submission]) }}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                        <div>
                                            <label for="points_earned" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Poin yang Diperoleh <span class="text-red-500">*</span></label>
                                            <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('points_earned') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                                   id="points_earned" name="points_earned" value="{{ old('points_earned') }}"
                                                   min="0" max="{{ $assignment->max_points }}" required>
                                            <p class="mt-2 text-sm text-gray-500">Maksimal: {{ $assignment->max_points }} poin</p>
                                            @error('points_earned')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Status <span class="text-red-500">*</span></label>
                                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('status') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" id="status" name="status" required>
                                                <option value="">Pilih status</option>
                                                <option value="graded" {{ old('status') === 'graded' ? 'selected' : '' }}>Dinilai</option>
                                                <option value="returned" {{ old('status') === 'returned' ? 'selected' : '' }}>Dikembalikan untuk Revisi</option>
                                            </select>
                                            @error('status')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Persentase</label>
                                            <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 font-medium text-gray-800" id="percentage-display">-</div>
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                        <label for="instructor_feedback" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Feedback untuk Siswa</label>
                                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('instructor_feedback') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                                  id="instructor_feedback" name="instructor_feedback" rows="4"
                                                  placeholder="Berikan feedback yang konstruktif...">{{ old('instructor_feedback') }}</textarea>
                                        @error('instructor_feedback')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Simpan Nilai
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @elseif(!$isInstructor && $submission->instructor_feedback)
                    <!-- Student View of Feedback -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Feedback Pengajar</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-4">
                                <h6 class="font-semibold text-blue-800 mb-2">Feedback:</h6>
                                <p class="text-gray-800 leading-relaxed">{{ $submission->instructor_feedback }}</p>
                            </div>
                            @if($submission->graded_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>
                                        Dinilai pada {{ $submission->graded_at->format('d M Y H:i') }}
                                        @if($submission->grader)
                                            oleh {{ $submission->grader->name }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Grade Summary -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Ringkasan Nilai</h3>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        @if($submission->points_earned !== null)
                            <div class="mb-4">
                                <div class="text-4xl font-bold text-emerald-600 mb-2">{{ $submission->points_earned }}</div>
                                <div class="text-lg text-gray-600 mb-2">dari {{ $assignment->max_points }} poin</div>
                                <div class="text-base text-gray-500">{{ number_format($submission->grade, 1) }}%</div>
                            </div>

                            @php
                                $percentage = $submission->grade;
                                if ($percentage >= 80) {
                                    $gradeClass = 'bg-green-100 text-green-800';
                                    $gradeLetter = 'A';
                                } elseif ($percentage >= 70) {
                                    $gradeClass = 'bg-blue-100 text-blue-800';
                                    $gradeLetter = 'B';
                                } elseif ($percentage >= 60) {
                                    $gradeClass = 'bg-yellow-100 text-yellow-800';
                                    $gradeLetter = 'C';
                                } else {
                                    $gradeClass = 'bg-red-100 text-red-800';
                                    $gradeLetter = 'D';
                                }
                            @endphp

                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $gradeClass }}">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Grade {{ $gradeLetter }}
                            </span>
                        @else
                            <div class="text-gray-500">
                                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h6 class="text-lg font-semibold text-gray-600">Belum Dinilai</h6>
                                <p class="text-sm text-gray-500 mt-1">Menunggu penilaian dari pengajar</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assignment Info -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Info Tugas</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="border-l-4 border-blue-400 pl-4">
                            <h6 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Judul</h6>
                            <p class="font-medium text-gray-800">{{ $assignment->title }}</p>
                        </div>
                        <div class="border-l-4 border-green-400 pl-4">
                            <h6 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Poin Maksimal</h6>
                            <p class="text-xl font-bold text-green-600">{{ $assignment->max_points }} poin</p>
                        </div>
                        @if($assignment->due_date)
                            <div class="border-l-4 border-amber-400 pl-4">
                                <h6 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Tenggat Waktu</h6>
                                <p class="font-medium text-gray-800">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                                @if($assignment->due_date->isPast())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Sudah lewat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $assignment->due_date->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        <div class="border-l-4 border-purple-400 pl-4">
                            <h6 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Pembuat</h6>
                            <p class="font-medium text-gray-800">{{ $assignment->creator->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Aksi Cepat</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        @if(!$isInstructor && $submission->canEdit())
                            @if($submission->status === 'draft' && $assignment->canSubmit())
                                <form action="{{ route('assignments.submissions.submit', [$assignment, $submission]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengumpulkan tugas ini? Setelah dikumpulkan, Anda tidak dapat mengubahnya lagi.')">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-teal-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Kumpulkan Tugas
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('assignments.submissions.create', $assignment) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Pengumpulan
                            </a>
                        @endif

                        @if($submission->file_paths)
                            <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 transition-colors duration-200" id="download-all-btn">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 13.5l5 5 5-5" />
                                </svg>
                                Download Semua File
                            </button>
                        @endif

                        @if($isInstructor)
                            <a href="{{ route('assignments.show', $assignment) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Tugas
                            </a>
                        @else
                            <a href="{{ route('assignments.show', $assignment) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Detail Tugas
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Auto-hide -->
@if(session('success'))
<script>
    setTimeout(() => {
        document.getElementById('success-toast')?.remove();
    }, 5000);
</script>
@endif

@push('scripts')
<script>
@if($isInstructor)
// Calculate percentage when points change
document.getElementById('points_earned')?.addEventListener('input', function() {
    const points = parseFloat(this.value) || 0;
    const maxPoints = {{ $assignment->max_points }};
    const percentage = (points / maxPoints * 100).toFixed(1);
    document.getElementById('percentage-display').textContent = percentage + '%';
});

// Toggle edit grade form
function toggleEditForm() {
    const form = document.getElementById('editGradeForm');
    form.classList.toggle('hidden');
}
@endif

// Download all files
document.getElementById('download-all-btn')?.addEventListener('click', function() {
    @if($submission->file_paths)
        @foreach($submission->file_paths as $index => $filePath)
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = '{{ route('assignments.submissions.download', [$assignment, $submission, $index]) }}';
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, {{ $index * 500 }});
        @endforeach
    @endif
});
</script>
@endpush
@endsection