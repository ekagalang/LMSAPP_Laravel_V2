@extends('layouts.app')

@section('title', 'Kumpulkan Tugas: ' . $assignment->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div class="flex items-start space-x-4">
                <div class="bg-gradient-to-br from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $existingSubmission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas' }}
                    </h1>
                    <p class="text-gray-600 mt-1 font-medium">{{ $assignment->title }}</p>
                </div>
            </div>
            <a href="{{ route('assignments.show', $assignment) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
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

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h6 class="font-medium mb-2">Terjadi kesalahan:</h6>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <!-- Assignment Info -->
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Tipe Pengumpulan:</h6>
                                <div class="flex items-center">
                                    @if($assignment->submission_type === 'file')
                                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-800">Unggah File</span>
                                    @elseif($assignment->submission_type === 'link')
                                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-800">Kirim Link</span>
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
                    </div>
                </div>

                <!-- Submission Form -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">
                                {{ $existingSubmission ? 'Edit Pengumpulan' : 'Form Pengumpulan' }}
                            </h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data" id="submission-form">
                            @csrf

                            <!-- Text Submission -->
                            <div class="mb-6">
                                <label for="submission_text" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Teks Pengumpulan
                                </label>
                                <div class="relative">
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('submission_text') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                              id="submission_text" name="submission_text" rows="5"
                                              placeholder="Tuliskan penjelasan atau keterangan mengenai pengumpulan Anda (opsional)">{{ old('submission_text', $existingSubmission->submission_text ?? '') }}</textarea>
                                    <div class="absolute inset-y-0 right-0 flex items-start pr-3 pt-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('submission_text')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Link Submission -->
                            @if($assignment->submission_type === 'link' || $assignment->submission_type === 'both')
                                <div class="mb-6">
                                    <label for="submission_link" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                        Link Pengumpulan
                                        @if($assignment->submission_type === 'link')
                                            <span class="text-red-500 ml-1">*</span>
                                        @endif
                                    </label>
                                    <div class="relative">
                                        <input type="url" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('submission_link') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                               id="submission_link" name="submission_link"
                                               value="{{ old('submission_link', $existingSubmission->submission_link ?? '') }}"
                                               placeholder="https://example.com/your-submission"
                                               {{ $assignment->submission_type === 'link' ? 'required' : '' }}>
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('submission_link')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Masukkan link ke Google Drive, Dropbox, GitHub, atau platform lainnya
                                    </p>
                                </div>
                            @endif

                            <!-- File Upload -->
                            @if($assignment->submission_type === 'file' || $assignment->submission_type === 'both')
                                <div class="mb-6">
                                    <label for="files" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                        Unggah File
                                        @if($assignment->submission_type === 'file')
                                            <span class="text-red-500 ml-1">*</span>
                                        @endif
                                    </label>

                                    <!-- Beautiful File Upload Area -->
                                    <div class="file-upload-area relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center mb-4 transition-all duration-300 hover:border-blue-400 hover:bg-blue-50/30 cursor-pointer group"
                                         ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
                                        <div class="upload-content">
                                            <div class="mx-auto mb-4 w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </div>
                                            <h5 class="text-xl font-bold text-gray-700 mb-2 group-hover:text-blue-600 transition-colors duration-300">Drag & Drop File di Sini</h5>
                                            <p class="text-gray-500 mb-4">File akan langsung terunggah secara otomatis</p>
                                            <div class="flex items-center justify-center mb-4">
                                                <div class="flex-1 border-t border-gray-300"></div>
                                                <span class="px-4 text-sm font-medium text-gray-500 bg-white">atau</span>
                                                <div class="flex-1 border-t border-gray-300"></div>
                                            </div>
                                            <button type="button" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200" onclick="document.getElementById('files').click()">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Pilih File dari Perangkat
                                            </button>
                                        </div>
                                    </div>

                                    <input type="file" class="hidden @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror"
                                           id="files" name="files[]"
                                           {{ $assignment->max_files > 1 ? 'multiple' : '' }}
                                           {{ $assignment->submission_type === 'file' ? 'required' : '' }}
                                           accept="{{ $assignment->allowed_file_types ? '.' . implode(',.', $assignment->allowed_file_types) : '' }}"
                                           onchange="handleFiles(this.files)">

                                    @error('files')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('files.*')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <!-- File Requirements -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
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
                                                    <strong>Ukuran maksimal:</strong> <span class="ml-1">{{ $assignment->getFileSizeFormatted() }}</span>
                                                </div>
                                            @endif
                                            @if($assignment->allowed_file_types)
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start text-blue-700">
                                                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <div>
                                                            <strong>Tipe file diizinkan:</strong>
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

                                    <!-- Selected Files Display -->
                                    <div id="selected-files" class="hidden mb-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            File yang Dipilih
                                        </h6>
                                        <div id="file-list" class="space-y-2"></div>
                                    </div>

                                    <!-- Existing Files -->
                                    @if($existingSubmission && $existingSubmission->file_paths)
                                        <div class="mb-4">
                                            <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                File yang Sudah Diunggah
                                            </h6>
                                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-amber-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-amber-800 text-sm font-medium">Jika Anda mengunggah file baru, file lama akan tergantikan.</span>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach($existingSubmission->file_paths as $index => $filePath)
                                                    @php
                                                        $metadata = $existingSubmission->file_metadata[$index] ?? [];
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
                                                        <a href="{{ route('assignments.submissions.download', [$assignment, $existingSubmission, $index]) }}"
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
                                </div>
                            @endif

                            <!-- Divider -->
                            <div class="border-t border-gray-200 my-6"></div>

                            <!-- Submit Options -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <button type="submit" name="action" value="save_draft" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Simpan sebagai Draft
                                </button>
                                <button type="submit" name="submit_now" value="1" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" id="submit-btn">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kumpulkan Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
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
                    </div>
                </div>

                @if($existingSubmission)
                    <!-- Current Status -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-blue-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Status Saat Ini</h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Status:</h6>
                                @if($existingSubmission->status === 'graded')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Dinilai
                                    </span>
                                @elseif($existingSubmission->status === 'submitted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                        </svg>
                                        Dikumpulkan
                                    </span>
                                @elseif($existingSubmission->status === 'returned')
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

                            @if($existingSubmission->submitted_at)
                                <div>
                                    <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Dikumpulkan:</h6>
                                    <p class="text-gray-800 font-medium">{{ $existingSubmission->submitted_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif

                            <div>
                                <h6 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Percobaan:</h6>
                                <p class="text-gray-800 font-medium">#{{ $existingSubmission->attempt_number }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tips -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">Tips</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-yellow-100 p-2 rounded-lg mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Simpan sebagai draft untuk mengedit nanti</p>
                                    <p class="text-gray-500 text-sm mt-1">Jika belum siap submit, simpan dulu sebagai draft</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-lg mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Periksa kembali sebelum mengirim</p>
                                    <p class="text-gray-500 text-sm mt-1">Pastikan semua file dan teks sudah benar</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-lg mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Kirim sebelum tenggat waktu</p>
                                    <p class="text-gray-500 text-sm mt-1">Jangan tunggu hingga menit terakhir</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Pastikan file sesuai ketentuan</p>
                                    <p class="text-gray-500 text-sm mt-1">Cek format, ukuran, dan jumlah file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// File upload handling with modern styling
function dragOverHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('border-blue-400', 'bg-blue-50');
    ev.currentTarget.classList.remove('border-gray-300');
}

function dragLeaveHandler(ev) {
    ev.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    ev.currentTarget.classList.add('border-gray-300');
}

function dropHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    ev.currentTarget.classList.add('border-gray-300');

    const files = ev.dataTransfer.files;
    document.getElementById('files').files = files;
    handleFiles(files);
}

function handleFiles(files) {
    const fileList = document.getElementById('file-list');
    const selectedFiles = document.getElementById('selected-files');

    if (files.length === 0) {
        selectedFiles.classList.add('hidden');
        return;
    }

    selectedFiles.classList.remove('hidden');
    fileList.innerHTML = '';

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-4';
        fileItem.innerHTML = `
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">${file.name}</p>
                    <p class="text-sm text-gray-500">${fileSize} MB</p>
                </div>
            </div>
            <button type="button" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200" onclick="removeFile(${i})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        fileList.appendChild(fileItem);
    }
}

function removeFile(index) {
    const fileInput = document.getElementById('files');
    const dt = new DataTransfer();

    for (let i = 0; i < fileInput.files.length; i++) {
        if (i !== index) {
            dt.items.add(fileInput.files[i]);
        }
    }

    fileInput.files = dt.files;
    handleFiles(fileInput.files);
}

// Countdown timer
@if($assignment->due_date && !$assignment->due_date->isPast())
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
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').innerHTML = `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>Waktu Habis`;
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
@endif

// Form submission confirmation
document.getElementById('submission-form').addEventListener('submit', function(e) {
    const submitBtn = e.submitter;
    if (submitBtn && submitBtn.name === 'submit_now') {
        if (!confirm('Apakah Anda yakin ingin mengumpulkan tugas ini? Setelah dikumpulkan, Anda tidak dapat mengubahnya lagi.')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
@endsection