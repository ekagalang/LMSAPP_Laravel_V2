@extends('layouts.app')

@section('title', 'Edit Tugas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
            <div class="flex items-start space-x-4">
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-4 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Edit Tugas</h1>
                    <p class="text-lg text-gray-600 font-medium">{{ $assignment->title }}</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('assignments.show', $assignment) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Lihat
                </a>
                <a href="{{ route('assignments.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
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

        <form action="{{ route('assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Informasi Dasar</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Judul Tugas <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                           id="title" name="title" value="{{ old('title', $assignment->title) }}" required
                                           placeholder="Masukkan judul tugas">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Deskripsi
                                </label>
                                <div class="relative">
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Berikan deskripsi singkat tentang tugas ini">{{ old('description', $assignment->description) }}</textarea>
                                    <div class="absolute top-3 right-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Instruksi Pengerjaan
                                </label>
                                <div class="relative">
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('instructions') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                              id="instructions" name="instructions" rows="6"
                                              placeholder="Berikan instruksi detail tentang bagaimana mengerjakan tugas ini">{{ old('instructions', $assignment->instructions) }}</textarea>
                                    <div class="absolute top-3 right-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('instructions')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Berikan instruksi detail tentang bagaimana mengerjakan tugas ini.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Settings -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Pengaturan Pengumpulan</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="submission_type" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Tipe Pengumpulan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('submission_type') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                            id="submission_type" name="submission_type" required>
                                        <option value="">Pilih tipe pengumpulan</option>
                                        <option value="file" {{ old('submission_type', $assignment->submission_type) === 'file' ? 'selected' : '' }}>File saja</option>
                                        <option value="link" {{ old('submission_type', $assignment->submission_type) === 'link' ? 'selected' : '' }}>Link saja</option>
                                        <option value="both" {{ old('submission_type', $assignment->submission_type) === 'both' ? 'selected' : '' }}>File atau Link</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('submission_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- File Settings -->
                            <div id="file-settings">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label for="max_files" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                            Maksimal File
                                        </label>
                                        <div class="relative">
                                            <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('max_files') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                                   id="max_files" name="max_files" value="{{ old('max_files', $assignment->max_files) }}"
                                                   min="1" max="10" placeholder="10">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('max_files')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="max_file_size" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                            Maksimal Ukuran File (MB)
                                        </label>
                                        <div class="relative">
                                            <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('max_file_size') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                                   id="max_file_size_mb" name="max_file_size_mb"
                                                   value="{{ old('max_file_size_mb', $assignment->max_file_size ? round($assignment->max_file_size / 1024 / 1024) : 20) }}"
                                                   min="1" max="1024" placeholder="20">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('max_file_size')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
                                        Tipe File yang Diizinkan
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @php
                                            $fileTypes = [
                                                'pdf' => 'PDF', 'doc' => 'Word (DOC)', 'docx' => 'Word (DOCX)',
                                                'xls' => 'Excel (XLS)', 'xlsx' => 'Excel (XLSX)',
                                                'ppt' => 'PowerPoint (PPT)', 'pptx' => 'PowerPoint (PPTX)',
                                                'txt' => 'Text', 'jpg' => 'JPEG', 'jpeg' => 'JPEG', 'png' => 'PNG',
                                                'gif' => 'GIF', 'mp4' => 'MP4', 'mov' => 'MOV', 'avi' => 'AVI',
                                                'mkv' => 'MKV', 'mp3' => 'MP3', 'wav' => 'WAV',
                                                'zip' => 'ZIP', 'rar' => 'RAR'
                                            ];
                                            $currentFileTypes = old('allowed_file_types', $assignment->allowed_file_types ?? []);
                                        @endphp
                                        @foreach($fileTypes as $ext => $label)
                                            <label class="relative flex items-start cursor-pointer group">
                                                <input type="checkbox" name="allowed_file_types[]" value="{{ $ext }}"
                                                       id="file_{{ $ext }}"
                                                       {{ in_array($ext, $currentFileTypes) ? 'checked' : '' }}
                                                       class="sr-only peer">
                                                <div class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 hover:border-purple-300 transition-all duration-200 text-center">
                                                    {{ $label }}
                                                </div>
                                                <div class="absolute top-1 right-1 w-4 h-4 rounded-full bg-purple-500 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('allowed_file_types')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <!-- Schedule & Assessment -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Jadwal & Penilaian</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Tenggat Waktu
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('due_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                           id="due_date" name="due_date"
                                           value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('due_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="max_points" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    Poin Maksimal <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('max_points') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                           id="max_points" name="max_points" value="{{ old('max_points', $assignment->max_points) }}"
                                           min="1" max="1000" required placeholder="100">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('max_points')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Late Submission Toggle -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex-1">
                                        <label for="allow_late_submission" class="block text-sm font-semibold text-gray-700 mb-1">
                                            Izinkan pengumpulan terlambat
                                        </label>
                                        <p class="text-xs text-gray-500">Siswa dapat mengumpulkan setelah tenggat waktu</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="allow_late_submission" name="allow_late_submission" value="1"
                                               {{ old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>
                            </div>

                            <div id="late-settings" class="space-y-6">
                                <div>
                                    <label for="late_submission_until" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                        Batas Akhir Pengumpulan Terlambat
                                    </label>
                                    <div class="relative">
                                        <input type="datetime-local" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('late_submission_until') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                               id="late_submission_until" name="late_submission_until"
                                               value="{{ old('late_submission_until', $assignment->late_submission_until ? $assignment->late_submission_until->format('Y-m-d\TH:i') : '') }}">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('late_submission_until')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="late_penalty" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                        Penalti Keterlambatan (%)
                                    </label>
                                    <div class="relative">
                                        <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('late_penalty') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                               id="late_penalty" name="late_penalty" value="{{ old('late_penalty', $assignment->late_penalty) }}"
                                               min="0" max="100" step="0.1" placeholder="10">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('late_penalty')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-yellow-600 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h3 class="text-xl font-bold text-white">Pengaturan</h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Status Toggle -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <label for="is_active" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Status Tugas
                                    </label>
                                    <p class="text-xs text-gray-500">Tugas dapat diakses dan dikerjakan siswa</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </div>

                            <!-- Visibility Toggle -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <label for="show_to_students" class="block text-sm font-semibold text-gray-700 mb-1">
                                        Tampilkan ke Siswa
                                    </label>
                                    <p class="text-xs text-gray-500">Tugas terlihat dalam daftar tugas siswa</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="show_to_students" name="show_to_students" value="1"
                                           {{ old('show_to_students', $assignment->show_to_students) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 mb-3">
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Perubahan
                                </div>
                            </button>
                            <a href="{{ route('assignments.show', $assignment) }}"
                               class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-100 border border-gray-300 rounded-lg shadow-sm text-gray-700 font-medium hover:bg-gray-200 hover:border-gray-400 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submissionType = document.getElementById('submission_type');
    const fileSettings = document.getElementById('file-settings');
    const allowLateSubmission = document.getElementById('allow_late_submission');
    const lateSettings = document.getElementById('late-settings');
    const successToast = document.getElementById('success-toast');

    // Enhanced toggle function with smooth animations
    function toggleElementWithAnimation(element, show) {
        if (show) {
            element.classList.remove('hidden');
            element.style.maxHeight = '0px';
            element.style.opacity = '0';
            element.style.overflow = 'hidden';

            // Force a reflow
            element.offsetHeight;

            element.style.transition = 'max-height 0.3s ease-out, opacity 0.3s ease-out';
            element.style.maxHeight = element.scrollHeight + 'px';
            element.style.opacity = '1';

            // Clean up after animation
            setTimeout(() => {
                element.style.maxHeight = '';
                element.style.overflow = '';
                element.style.transition = '';
            }, 300);
        } else {
            element.style.transition = 'max-height 0.3s ease-out, opacity 0.3s ease-out';
            element.style.maxHeight = element.scrollHeight + 'px';
            element.style.opacity = '1';

            // Force a reflow
            element.offsetHeight;

            element.style.maxHeight = '0px';
            element.style.opacity = '0';

            setTimeout(() => {
                element.classList.add('hidden');
                element.style.transition = '';
            }, 300);
        }
    }

    // Toggle file settings based on submission type with animation
    function toggleFileSettings() {
        const value = submissionType.value;
        const shouldShow = value === 'file' || value === 'both';

        if (shouldShow) {
            toggleElementWithAnimation(fileSettings, true);
        } else {
            toggleElementWithAnimation(fileSettings, false);
        }
    }

    // Toggle late submission settings with animation
    function toggleLateSettings() {
        const shouldShow = allowLateSubmission.checked;

        if (shouldShow) {
            toggleElementWithAnimation(lateSettings, true);
        } else {
            toggleElementWithAnimation(lateSettings, false);
        }
    }

    // Enhanced form validation
    function validateForm() {
        let isValid = true;
        const errors = [];

        // Check required fields
        const title = document.getElementById('title');
        const submissionTypeVal = submissionType.value;
        const maxPoints = document.getElementById('max_points');

        if (!title.value.trim()) {
            errors.push('Judul tugas harus diisi');
            isValid = false;
        }

        if (!submissionTypeVal) {
            errors.push('Tipe pengumpulan harus dipilih');
            isValid = false;
        }

        if (!maxPoints.value || maxPoints.value < 1) {
            errors.push('Poin maksimal harus diisi dan lebih dari 0');
            isValid = false;
        }

        // Validate late submission settings
        if (allowLateSubmission.checked) {
            const lateSubmissionUntil = document.getElementById('late_submission_until');
            const dueDate = document.getElementById('due_date');

            if (dueDate.value && lateSubmissionUntil.value) {
                const dueDateValue = new Date(dueDate.value);
                const lateUntilValue = new Date(lateSubmissionUntil.value);

                if (lateUntilValue <= dueDateValue) {
                    errors.push('Batas akhir pengumpulan terlambat harus setelah tenggat waktu');
                    isValid = false;
                }
            }
        }

        // Show validation errors if any
        if (!isValid) {
            alert('Terjadi kesalahan:\n- ' + errors.join('\n- '));
        }

        return isValid;
    }

    // Add smooth focus effects to form inputs
    function addFocusEffects() {
        const inputs = document.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2');
                if (this.classList.contains('border-gray-300')) {
                    this.classList.remove('border-gray-300');
                    this.classList.add('border-blue-500');
                }
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2');
                if (this.classList.contains('border-blue-500') && !this.classList.contains('border-red-300')) {
                    this.classList.remove('border-blue-500');
                    this.classList.add('border-gray-300');
                }
            });
        });
    }

    // Auto-hide success toast
    if (successToast) {
        setTimeout(() => {
            successToast.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            successToast.style.opacity = '0';
            successToast.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                successToast.remove();
            }, 300);
        }, 5000);
    }

    // Event listeners
    submissionType.addEventListener('change', toggleFileSettings);
    allowLateSubmission.addEventListener('change', toggleLateSettings);

    // Form submission handler
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Convert max_file_size from MB to bytes for backend
        // File size is now handled in MB format directly by the server

        // Show loading state on submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <div class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            </div>
        `;

        // Reset button if form submission fails
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });

    // Initialize functions
    addFocusEffects();
    toggleFileSettings();
    toggleLateSettings();

    // Add initial hidden class to elements that should be hidden
    const value = submissionType.value;
    if (value !== 'file' && value !== 'both') {
        fileSettings.classList.add('hidden');
    }

    if (!allowLateSubmission.checked) {
        lateSettings.classList.add('hidden');
    }
});
</script>
@endpush
@endsection