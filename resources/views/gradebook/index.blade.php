`<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buku Nilai (Gradebook): {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola penilaian esai dan feedback umum untuk peserta.
                </p>
            </div>
            <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                &larr; Kembali ke Kursus
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            
            {{-- Form Pencarian & Filter--}}
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="course_filter" class="block text-sm font-medium text-gray-700">Pindah ke Gradebook Kursus Lain</label>
                    <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach ($allCoursesForFilter as $filterCourse)
                            <option value="{{ route('courses.gradebook', $filterCourse) }}" @selected($filterCourse->id == $course->id)>
                                {{ $filterCourse->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <form action="{{ route('courses.gradebook', $course) }}" method="GET">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari Peserta di Kursus Ini</label>
                        <div class="mt-1 flex">
                            <x-text-input type="text" name="search" id="search" class="w-full" placeholder="Nama atau email..." value="{{ request('search') }}" />
                            <x-primary-button class="ml-2">Cari</x-primary-button>
                            @if(request('search'))
                                <a href="{{ route('courses.gradebook', $course) }}" class="ml-2 self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div x-data="{ currentTab: 'essays' }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Kontrol Tabs --}}
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="currentTab = 'essays'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'essays', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'essays'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Penilaian Esai
                        </button>
                        <button @click="currentTab = 'feedback'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'feedback', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'feedback'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Feedback Umum
                        </button>
                    </nav>
                </div>

                {{-- Konten Tab Penilaian Esai --}}
                <div x-show="currentTab === 'essays'" class="p-6">
                    <h3 class="text-xl font-bold mb-4">Daftar Peserta (Esai)</h3>
                    @if ($participantsWithEssays->isEmpty())
                        <div class="text-center py-10"><p class="text-gray-500">Tidak ada peserta yang cocok atau belum ada yang mengumpulkan esai.</p></div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($participantsWithEssays as $participant)
                                <a href="{{ route('gradebook.user_essays', ['course' => $course, 'user' => $participant]) }}" class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <p class="font-semibold text-indigo-600">{{ $participant->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $participant->email }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Konten Tab Feedback Umum --}}
                <div x-show="currentTab === 'feedback'" x-cloak class="p-6">
                    <h3 class="text-xl font-bold mb-4">Feedback Umum per Peserta</h3>
                    <div class="space-y-4">
                        @forelse ($participants as $participant)
                            <div x-data="{ open: false }" class="border rounded-lg">
                                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left">
                                    <span class="font-semibold text-gray-800">{{ $participant->name }}</span>
                                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" x-collapse class="p-4 border-t">
                                    <form action="{{ route('gradebook.storeFeedback', ['course' => $course, 'user' => $participant]) }}" method="POST">
                                        @csrf
                                        <textarea name="feedback" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Tulis feedback umum untuk {{ $participant->name }}...">{{ $participant->feedback->first()->feedback ?? '' }}</textarea>
                                        <div class="text-right mt-2">
                                            <x-primary-button type="submit">Simpan</x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10"><p class="text-gray-500">Tidak ada peserta yang cocok dengan pencarian Anda.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>`