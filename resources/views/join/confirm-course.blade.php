@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Bergabung</h1>
                <p class="text-gray-600">Token valid ditemukan untuk kursus</p>
            </div>

            <!-- Course Info Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <!-- Course Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($course->thumbnail)
                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}">
                            @else
                                <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-bold text-white">{{ $course->title }}</h2>
                            <p class="text-indigo-100 text-sm">{{ $course->instructors->pluck('name')->implode(', ') }}</p>
                        </div>
                        <div class="ml-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Kursus Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Course Details -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi Kursus:</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                {{ $course->description ?: 'Tidak ada deskripsi tersedia.' }}
                            </p>
                        </div>

                        <!-- Objectives -->
                        @if($course->objectives)
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Tujuan Pembelajaran:</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                {{ $course->objectives }}
                            </p>
                        </div>
                        @endif

                        <!-- Stats -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Statistik:</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">{{ $course->participants->count() }} peserta terdaftar</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-gray-600">{{ $course->lessons->count() }} materi pembelajaran</span>
                                </div>
                            </div>
                        </div>

                        <!-- Token Info -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Token yang Digunakan:</h3>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 01-2 2m2-2a2 2 0 002 2M9 5a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                                    </svg>
                                    <span class="text-sm font-mono font-bold text-green-700">{{ $token }}</span>
                                </div>
                                <p class="text-xs text-green-600 mt-1">Token Kursus - Bergabung ke seluruh kursus</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="border-t pt-6">
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <a href="{{ route('join.form') }}"
                               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali
                            </a>

                            <form action="{{ route('join.confirm-course', $token) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-md shadow-md transition-all duration-200 transform hover:scale-[1.02]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ya, Bergabung ke Kursus Ini
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-900 mb-1">Yang akan terjadi setelah bergabung:</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Anda akan menjadi peserta dalam kursus ini</li>
                            <li>• Anda dapat mengakses semua materi pembelajaran yang tersedia</li>
                            <li>• Anda dapat berpartisipasi dalam diskusi dan aktivitas kursus</li>
                            <li>• Progress belajar Anda akan dipantau secara otomatis</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection