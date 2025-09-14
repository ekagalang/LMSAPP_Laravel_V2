@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Bergabung</h1>
                <p class="text-gray-600">Token valid ditemukan untuk periode kursus</p>
            </div>

            <!-- Period Info Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <!-- Period Header -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($period->course->thumbnail)
                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ Storage::url($period->course->thumbnail) }}" alt="{{ $period->course->title }}">
                            @else
                                <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-bold text-white">{{ $period->name }}</h2>
                            <p class="text-purple-100 text-sm">{{ $period->course->title }}</p>
                        </div>
                        <div class="ml-4">
                            {!! $period->status_badge !!}
                        </div>
                    </div>
                </div>

                <!-- Period Details -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Course Info -->
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Kursus:</h3>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <h4 class="font-medium text-gray-900">{{ $period->course->title }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $period->course->description ?: 'Tidak ada deskripsi tersedia.' }}</p>
                            </div>
                        </div>

                        <!-- Period Schedule -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Jadwal Periode:</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-gray-600">
                                        <strong>Mulai:</strong> {{ $period->start_date->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-gray-600">
                                        <strong>Selesai:</strong> {{ $period->end_date->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">
                                        <strong>Durasi:</strong> {{ $period->getDurationInDays() }} hari
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Period Stats -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Statistik Periode:</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">
                                        {{ $period->participants->count() }}
                                        @if($period->max_participants)
                                            / {{ $period->max_participants }}
                                        @endif
                                        peserta terdaftar
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">{{ $period->instructors->count() }} instructor</span>
                                </div>
                                @if($period->max_participants)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-gray-600">
                                            {{ $period->getAvailableSlots() }} slot tersedia
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Period Description -->
                        @if($period->description)
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi Periode:</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                {{ $period->description }}
                            </p>
                        </div>
                        @endif

                        <!-- Token Info -->
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Token yang Digunakan:</h3>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 01-2 2m2-2a2 2 0 002 2M9 5a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                                    </svg>
                                    <span class="text-sm font-mono font-bold text-purple-700">{{ $token }}</span>
                                </div>
                                <p class="text-xs text-purple-600 mt-1">Token Periode - Bergabung ke periode/batch tertentu</p>
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

                            <form action="{{ route('join.confirm-period', $token) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold rounded-md shadow-md transition-all duration-200 transform hover:scale-[1.02]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ya, Bergabung ke Periode Ini
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
                            <li>• Anda akan otomatis terdaftar dalam kursus "{{ $period->course->title }}"</li>
                            <li>• Anda akan menjadi peserta dalam periode "{{ $period->name }}"</li>
                            <li>• Anda dapat berpartisipasi dalam chat grup periode ini</li>
                            <li>• Anda dapat mengakses semua materi pembelajaran dalam jadwal periode</li>
                            <li>• Progress belajar Anda akan dipantau dalam konteks periode ini</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection