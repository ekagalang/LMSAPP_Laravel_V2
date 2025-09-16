@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
            <div class="flex items-start space-x-4">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $assignment->title }}</h1>
                    <div class="flex flex-wrap gap-3">
                        @if($assignment->due_date)
                            @if($assignment->due_date->isPast())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    Terlambat
                                </span>
                            @elseif($assignment->due_date->diffInDays() <= 1)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Segera
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Tersedia
                                </span>
                            @endif
                        @endif

                        @if($userSubmission)
                            @if($userSubmission->status === 'graded')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Dinilai
                                </span>
                            @elseif($userSubmission->status === 'submitted')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                    </svg>
                                    Dikumpulkan
                                </span>
                            @elseif($userSubmission->status === 'returned')
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
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                @if(!$userSubmission || $userSubmission->canEdit())
                    @if($assignment->canSubmit())
                        <a href="{{ route('assignments.submissions.create', $assignment) }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            {{ $userSubmission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas' }}
                        </a>
                    @endif
                @endif
                <a href="{{ route('assignments.student.index') }}"
                   class="inline-flex items-center px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Success Messages -->
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

        @if(session('info'))
            <div id="info-toast" class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <!-- Assignment Details -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Detail Tugas</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($assignment->description)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Deskripsi:</h6>
                                <p class="text-gray-600 leading-relaxed">{{ $assignment->description }}</p>
                            </div>
                        @endif

                        @if($assignment->instructions)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Instruksi:</h6>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                    <p class="text-gray-800">{{ $assignment->instructions }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Tipe Pengumpulan:</h6>
                                <div class="flex items-center">
                                    @if($assignment->submission_type === 'file')
                                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-800">Unggah file</span>
                                    @elseif($assignment->submission_type === 'link')
                                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-800">Kirim link</span>
                                    @else
                                        <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-800">File atau Link</span>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Poin:</h6>
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <span class="text-xl font-bold text-green-600">{{ $assignment->max_points }}</span>
                                    <span class="text-gray-600 ml-1">poin</span>
                                </div>
                            </div>
                        </div>

                        @if($assignment->submission_type === 'file' || $assignment->submission_type === 'both')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <h6 class="text-sm font-semibold text-blue-800 uppercase tracking-wide">Ketentuan File</h6>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center text-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <strong>Maksimal file:</strong> <span class="ml-1">{{ $assignment->max_files ?? 1 }}</span>
                                    </div>
                                    @if($assignment->max_file_size)
                                        <div class="flex items-center text-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                            </svg>
                                            <strong>Ukuran maksimal per file:</strong> <span class="ml-1">{{ $assignment->getFileSizeFormatted() }}</span>
                                        </div>
                                    @endif
                                    @if($assignment->allowed_file_types)
                                        <div class="md:col-span-2">
                                            <div class="flex items-start text-blue-700">
                                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div>
                                                    <strong>Tipe file yang diizinkan:</strong>
                                                    <div class="flex flex-wrap gap-2 mt-2">
                                                        @foreach($assignment->allowed_file_types as $type)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ strtoupper($type) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submission Status -->
                @if($userSubmission)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-xl font-bold text-white">Status Pengumpulan</h3>
                                </div>
                                <div class="flex gap-3">
                                    @if($userSubmission->canEdit())
                                        <a href="{{ route('assignments.submissions.create', $assignment) }}"
                                           class="inline-flex items-center px-3 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                    @endif
                                    <a href="{{ route('assignments.submissions.show', [$assignment, $userSubmission]) }}"
                                       class="inline-flex items-center px-3 py-2 bg-white text-teal-600 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h6 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Status:</h6>
                                    @if($userSubmission->status === 'graded')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Dinilai
                                        </span>
                                    @elseif($userSubmission->status === 'submitted')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                            </svg>
                                            Dikumpulkan
                                        </span>
                                    @elseif($userSubmission->status === 'returned')
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
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h6 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Dikumpulkan:</h6>
                                    @if($userSubmission->submitted_at)
                                        <p class="font-semibold text-gray-800">{{ $userSubmission->submitted_at->format('d M Y H:i') }}</p>
                                        @if($assignment->due_date && $userSubmission->submitted_at->gt($assignment->due_date))
                                            <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full">Terlambat</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500 font-medium">Belum dikumpulkan</span>
                                    @endif
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h6 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Nilai:</h6>
                                    @if($userSubmission->points_earned !== null)
                                        <div class="text-lg font-bold text-green-600">{{ $userSubmission->points_earned }}/{{ $assignment->max_points }}</div>
                                        <div class="text-sm text-gray-500">{{ number_format($userSubmission->grade, 1) }}%</div>
                                    @else
                                        <span class="text-gray-500 font-medium">Belum dinilai</span>
                                    @endif
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h6 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Percobaan:</h6>
                                    <p class="text-lg font-bold text-gray-800">#{{ $userSubmission->attempt_number }}</p>
                                </div>
                            </div>

                            @if($userSubmission->submission_text)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Teks Pengumpulan:</h6>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <p class="text-gray-800 leading-relaxed">{{ $userSubmission->submission_text }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($userSubmission->submission_link)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Link:</h6>
                                    <a href="{{ $userSubmission->submission_link }}" target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Buka Link
                                    </a>
                                </div>
                            @endif

                            @if($userSubmission->file_paths)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h6 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">File yang Diunggah:</h6>
                                    <div class="space-y-3">
                                        @foreach($userSubmission->file_paths as $index => $filePath)
                                            @php
                                                $metadata = $userSubmission->file_metadata[$index] ?? [];
                                                $fileName = $metadata['original_name'] ?? basename($filePath);
                                                $fileSize = isset($metadata['size']) ? round($metadata['size'] / 1024, 1) . ' KB' : '';
                                            @endphp
                                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-800">{{ $fileName }}</p>
                                                        @if($fileSize)
                                                            <p class="text-sm text-gray-500">{{ $fileSize }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <a href="{{ route('assignments.submissions.download', [$assignment, $userSubmission, $index]) }}"
                                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 13.5l5 5 5-5" />
                                                    </svg>
                                                    Unduh
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($userSubmission->instructor_feedback)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Feedback Pengajar:</h6>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-blue-800">{{ $userSubmission->instructor_feedback }}</p>
                                        </div>
                                    </div>
                                    @if($userSubmission->graded_at)
                                        <p class="text-sm text-gray-500 mt-3">
                                            Dinilai pada {{ $userSubmission->graded_at->format('d M Y H:i') }}
                                            @if($userSubmission->grader)
                                                oleh {{ $userSubmission->grader->name }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- No Submission Yet -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="p-8 text-center">
                            <div class="mx-auto mb-6 w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <h5 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Pengumpulan</h5>
                            <p class="text-gray-500 mb-6">Anda belum mengumpulkan tugas ini. Segera kumpulkan sebelum tenggat waktu!</p>
                            @if($assignment->canSubmit())
                                <a href="{{ route('assignments.submissions.create', $assignment) }}"
                                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-teal-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Kumpulkan Sekarang
                                </a>
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-red-800 font-semibold">Pengumpulan sudah ditutup</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1 space-y-6">
                <!-- Schedule Info -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Jadwal</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($assignment->due_date)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Tenggat Waktu:</h6>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-lg font-bold text-gray-800 mb-1">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                                    @if($assignment->due_date->isPast())
                                        <div class="flex items-center text-red-600">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium">Sudah lewat</span>
                                        </div>
                                    @else
                                        <div class="flex items-center text-green-600">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium">{{ $assignment->due_date->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Countdown -->
                            @if(!$assignment->due_date->isPast())
                                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-4 mb-4">
                                    <div id="countdown" class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-bold text-amber-800">Sisa Waktu:</span>
                                        </div>
                                        <div id="countdown-timer" class="text-2xl font-bold text-amber-600"></div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-blue-800 font-medium">Tidak ada tenggat waktu</span>
                                </div>
                            </div>
                        @endif

                        @if($assignment->allow_late_submission && $assignment->late_submission_until)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <h6 class="text-sm font-semibold text-yellow-800 mb-2 uppercase tracking-wide">Batas Akhir Terlambat:</h6>
                                <p class="text-yellow-800 font-medium mb-1">{{ $assignment->late_submission_until->format('d M Y H:i') }}</p>
                                @if($assignment->late_penalty > 0)
                                    <div class="flex items-center text-yellow-700">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium">Penalti: {{ $assignment->late_penalty }}%</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Pembuat:</h6>
                            <div class="flex items-center">
                                <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $assignment->creator->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $assignment->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Aksi</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if(!$userSubmission || $userSubmission->canEdit())
                                @if($assignment->canSubmit())
                                    <a href="{{ route('assignments.submissions.create', $assignment) }}"
                                       class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        {{ $userSubmission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas' }}
                                    </a>
                                @else
                                    <button class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed" disabled>
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                        Pengumpulan Ditutup
                                    </button>
                                @endif
                            @endif

                            @if($userSubmission)
                                <a href="{{ route('assignments.submissions.show', [$assignment, $userSubmission]) }}"
                                   class="w-full inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Lihat Detail Pengumpulan
                                </a>
                            @endif

                            <a href="{{ route('assignments.student.index') }}"
                               class="w-full inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Daftar Tugas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($assignment->due_date && !$assignment->due_date->isPast())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dueDate = new Date('{{ $assignment->due_date->toISOString() }}').getTime();
    const countdownElement = document.getElementById('countdown-timer');

    if (countdownElement) {
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = dueDate - now;

            if (distance < 0) {
                clearInterval(timer);
                countdownElement.innerHTML = "<span class='text-danger'>Waktu Habis</span>";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `<span class="inline-block mx-1">${days}<span class="text-xs ml-1">hari</span></span> <span class="inline-block mx-1">${hours}<span class="text-xs ml-1">jam</span></span> <span class="inline-block mx-1">${minutes}<span class="text-xs ml-1">menit</span></span> <span class="inline-block mx-1">${seconds}<span class="text-xs ml-1">detik</span></span>`;
        }, 1000);
    }
});
</script>
@endpush
@endif
@endsection