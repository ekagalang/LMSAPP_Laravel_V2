<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                            Buku Nilai
                        </h2>
                        <p class="text-indigo-600 font-medium text-sm">{{ $course->title }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 max-w-2xl">
                    Kelola penilaian esai dan berikan feedback komprehensif untuk semua peserta kursus Anda.
                </p>
            </div>
            <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Kursus
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            {{-- Enhanced Search & Filter Section --}}
            <div class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="course_filter" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                            </svg>
                            Pindah ke Gradebook Kursus Lain
                        </label>
                        <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm transition-all duration-200">
                            @foreach ($allCoursesForFilter as $filterCourse)
                                <option value="{{ route('courses.gradebook', $filterCourse) }}" @selected($filterCourse->id == $course->id)>
                                    {{ $filterCourse->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <form action="{{ route('courses.gradebook', $course) }}" method="GET" class="space-y-2">
                        <label for="search" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Peserta di Kursus Ini
                        </label>
                        <div class="flex space-x-3">
                            <div class="relative flex-1">
                                <x-text-input type="text" name="search" id="search" class="w-full pl-4 pr-4 py-3 rounded-lg shadow-sm" placeholder="Masukkan nama atau email peserta..." value="{{ request('search') }}" />
                            </div>
                            <x-primary-button class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </x-primary-button>
                            @if(request('search'))
                                <a href="{{ route('courses.gradebook', $course) }}" class="inline-flex items-center px-4 py-3 text-sm text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div x-data="{ currentTab: 'essays' }" class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-200">
                {{-- Enhanced Tab Navigation --}}
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex px-6" aria-label="Tabs">
                        <button @click="currentTab = 'essays'" :class="{'border-indigo-500 text-indigo-600 bg-white': currentTab === 'essays', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'essays'}" class="relative whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all duration-200 rounded-t-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Penilaian Esai</span>
                            </div>
                        </button>
                        <button @click="currentTab = 'feedback'" :class="{'border-indigo-500 text-indigo-600 bg-white': currentTab === 'feedback', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'feedback'}" class="relative whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all duration-200 rounded-t-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Feedback Umum</span>
                            </div>
                        </button>
                    </nav>
                </div>

                {{-- Enhanced Essays Tab Content --}}
                <div x-show="currentTab === 'essays'" class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-2xl font-bold text-gray-900">Daftar Peserta</h3>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                                {{ $participantsWithEssays->count() }} Peserta
                            </span>
                        </div>
                    </div>
                    
                    @if ($participantsWithEssays->isEmpty())
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Esai</h4>
                            <p class="text-gray-500 max-w-md mx-auto">Tidak ada peserta yang cocok dengan pencarian atau belum ada yang mengumpulkan esai untuk kursus ini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($participantsWithEssays as $participant)
                                <a href="{{ route('gradebook.user_essays', ['course' => $course, 'user' => $participant]) }}" class="group block p-6 bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl hover:shadow-lg hover:border-indigo-300 transition-all duration-200 transform hover:-translate-y-1">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                                            <span class="font-bold text-white text-lg">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200 truncate">
                                                {{ $participant->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate mt-1">{{ $participant->email }}</p>
                                            <div class="flex items-center mt-3 text-xs text-indigo-600">
                                                <span>Lihat esai</span>
                                                <svg class="w-3 h-3 ml-1 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Enhanced Feedback Tab Content --}}
                <div x-show="currentTab === 'feedback'" x-cloak class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-2xl font-bold text-gray-900">Feedback Umum</h3>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                {{ $participants->count() }} Peserta
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse ($participants as $participant)
                            <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                                <button @click="open = !open" class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 rounded-xl transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-sm">
                                            <span class="font-bold text-white text-sm">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $participant->name }}</span>
                                            <p class="text-sm text-gray-500">{{ $participant->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($participant->feedback->first()?->feedback)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                Ada Feedback
                                            </span>
                                        @endif
                                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </button>
                                <div x-show="open" x-collapse class="px-6 pb-6">
                                    <div class="border-t border-gray-100 pt-4">
                                        <form action="{{ route('gradebook.storeFeedback', ['course' => $course, 'user' => $participant]) }}" method="POST" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Feedback untuk {{ $participant->name }}
                                                </label>
                                                <textarea 
                                                    name="feedback" 
                                                    rows="4" 
                                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 resize-none" 
                                                    placeholder="Berikan feedback konstruktif untuk {{ $participant->name }}. Misalnya: poin kuat, area yang perlu diperbaiki, saran pengembangan..."
                                                >{{ $participant->feedback->first()->feedback ?? '' }}</textarea>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Feedback akan tersimpan otomatis</span>
                                                </div>
                                                <x-primary-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Simpan Feedback
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Peserta</h4>
                                <p class="text-gray-500 max-w-md mx-auto">Tidak ada peserta yang cocok dengan pencarian Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>