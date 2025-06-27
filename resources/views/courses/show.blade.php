{{-- resources/views/courses/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Kembali ke Daftar Kursus') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-2">
                    Detail Kursus: {{ $course->title }}
                </h2>
            </div>
            @can('update', $course)
                <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Kursus') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div x-data="{ currentTab: 'lessons' }" class="mt-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="currentTab = 'lessons'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'lessons', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'lessons'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pelajaran & Konten
                        </button>
                        @can('update', $course)
                            <button @click="currentTab = 'participants'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'participants', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'participants'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Peserta Kursus
                            </button>
                        @endcan
                    </nav>
                </div>

                <div x-show="currentTab === 'lessons'" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Daftar Pelajaran</h3>
                            @can('update', $course)
                                <a href="{{ route('courses.lessons.create', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Tambah Pelajaran') }}
                                </a>
                            @endcan
                        </div>

                        @if ($course->lessons->isEmpty())
                            <p class="text-center text-gray-500">Belum ada pelajaran dalam kursus ini.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($course->lessons as $lesson)
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <div class="flex justify-between items-center">
                                            <h4 class="text-lg font-semibold text-gray-800">
                                                {{ $lesson->order }}. {{ $lesson->title }}
                                            </h4>
                                            @can('update', $course)
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('courses.lessons.edit', [$course, $lesson]) }}" class="text-purple-600 hover:text-purple-900 text-sm">Edit</a>
                                                    <form action="{{ route('courses.lessons.destroy', [$course, $lesson]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelajaran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                                    </form>
                                                    <a href="{{ route('lessons.contents.create', $lesson) }}" class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-md hover:bg-green-600">
                                                        Tambah Konten
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                        <p class="text-gray-600 text-sm mt-1">{{ $lesson->description }}</p>

                                        <div class="mt-4 border-t border-gray-200 pt-4">
                                            <h5 class="text-md font-semibold text-gray-700 mb-2">Konten Pelajaran:</h5>
                                            @if ($lesson->contents->isEmpty())
                                                <p class="text-sm text-gray-500">Belum ada konten dalam pelajaran ini.</p>
                                            @else
                                                <ul class="space-y-2">
                                                    @foreach ($lesson->contents as $content)
                                                        <li class="flex justify-between items-center bg-white p-3 rounded-md shadow-sm border border-gray-200">
                                                            <div class="flex items-center">
                                                                <span class="font-medium text-gray-800 mr-2">{{ $content->order }}.</span>
                                                                <span class="capitalize text-indigo-700 mr-2">[{{ $content->type }}]</span>
                                                                <a href="{{ route('contents.show', [$lesson, $content]) }}" class="text-blue-600 hover:text-blue-900">{{ $content->title }}</a>
                                                            </div>
                                                            @can('update', $course)
                                                                <div class="flex space-x-2">
                                                                    <a href="{{ route('lessons.contents.edit', [$lesson, $content]) }}" class="text-purple-600 hover:text-purple-900 text-sm">Edit</a>
                                                                    <form action="{{ route('lessons.contents.destroy', [$lesson, $content]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus konten ini?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                                                    </form>
                                                                </div>
                                                            @endcan
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @can('update', $course)
                    <div x-show="currentTab === 'participants'" x-data="{
                        selectedEnrollUsers: [],
                        selectedUnenrollUsers: [],
                        searchTermEnroll: '', // Search term untuk mendaftarkan peserta baru
                        searchTermUnenroll: '', // Search term untuk mencabut akses peserta terdaftar
                        
                        // Data mentah untuk peserta yang belum terdaftar (PHP ke JS)
                        unEnrolledParticipantsData: {{ \App\Models\User::where('role', 'participant')->whereNotIn('id', $course->participants->pluck('id'))->orderBy('name')->get()->toJson() }},
                        filteredUnEnrolledParticipants: [],

                        // Data mentah untuk peserta yang sudah terdaftar (PHP ke JS)
                        enrolledParticipantsData: {{ $course->participants->toJson() }},
                        filteredEnrolledParticipants: [],

                        init() {
                            // Pastikan data selalu array
                            this.unEnrolledParticipantsData = Array.isArray(this.unEnrolledParticipantsData) ? this.unEnrolledParticipantsData : [];
                            this.enrolledParticipantsData = Array.isArray(this.enrolledParticipantsData) ? this.enrolledParticipantsData : [];

                            // Filter awal saat komponen diinisialisasi
                            this.filterUnEnrolledParticipants(); // Ini untuk daftar yang BELUM terdaftar
                            this.filterEnrolledParticipants();   // Ini untuk daftar yang SUDAH terdaftar

                            // Pantau perubahan search terms
                            this.$watch('searchTermEnroll', () => this.filterUnEnrolledParticipants());
                            this.$watch('searchTermUnenroll', () => this.filterEnrolledParticipants());
                        },

                        filterUnEnrolledParticipants() {
                            // Fungsi ini memfilter daftar yang BELUM terdaftar, menggunakan searchTermEnroll
                            if (this.searchTermEnroll === '') {
                                this.filteredUnEnrolledParticipants = this.unEnrolledParticipantsData;
                            } else {
                                this.filteredUnEnrolledParticipants = this.unEnrolledParticipantsData.filter(user => {
                                    const userName = user.name ? user.name.toLowerCase() : '';
                                    const userEmail = user.email ? user.email.toLowerCase() : '';
                                    const lowerCaseSearchTerm = this.searchTermEnroll.toLowerCase();
                                    return userName.includes(lowerCaseSearchTerm) || userEmail.includes(lowerCaseSearchTerm);
                                });
                            }
                        },

                        filterEnrolledParticipants() {
                            // Fungsi ini memfilter daftar yang SUDAH terdaftar, menggunakan searchTermUnenroll
                            if (this.searchTermUnenroll === '') {
                                this.filteredEnrolledParticipants = this.enrolledParticipantsData;
                            } else {
                                this.filteredEnrolledParticipants = this.enrolledParticipantsData.filter(user => {
                                    const userName = user.name ? user.name.toLowerCase() : '';
                                    const userEmail = user.email ? user.email.toLowerCase() : '';
                                    const lowerCaseSearchTerm = this.searchTermUnenroll.toLowerCase();
                                    return userName.includes(lowerCaseSearchTerm) || userEmail.includes(lowerCaseSearchTerm);
                                });
                            }
                        }
                    }" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Daftar Peserta Terdaftar</h3>

                            @if ($course->participants->isEmpty())
                                <p class="text-center text-gray-500 mb-4">Belum ada peserta yang terdaftar di kursus ini.</p>
                            @else
                                <div class="mb-4">
                                    {{-- Search input untuk peserta terdaftar --}}
                                    <input type="text" x-model="searchTermUnenroll" placeholder="Cari nama atau email..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <form id="unenroll-form" method="POST" action="{{ route('courses.unenroll_mass', $course) }}" onsubmit="return confirm('Apakah Anda yakin ingin mencabut akses peserta terpilih?');">
                                    @csrf
                                    @method('DELETE')
                                    <div class="mb-4">
                                        <button type="submit" :disabled="selectedUnenrollUsers.length === 0" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Cabut Akses Terpilih (<span x-text="selectedUnenrollUsers.length"></span>)
                                        </button>
                                    </div>
                                    {{-- Scrolling window untuk peserta terdaftar --}}
                                    <div class="space-y-2 mb-6 max-h-60 overflow-y-auto border p-2 rounded-md">
                                        {{-- Menggunakan filteredEnrolledParticipants --}}
                                        <template x-for="participant in filteredEnrolledParticipants" :key="participant.id">
                                            <div class="flex items-center bg-gray-50 p-3 rounded-md shadow-sm border border-gray-200">
                                                <input type="checkbox" name="user_ids[]" :value="participant.id" x-model="selectedUnenrollUsers" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 mr-2">
                                                <span class="font-medium text-gray-800 flex-grow" x-text="`${participant.name} (${participant.email})`"></span>
                                            </div>
                                        </template>
                                        <template x-if="filteredEnrolledParticipants.length === 0 && searchTermUnenroll === ''">
                                            <p class="text-gray-500 text-sm">Tidak ada peserta yang terdaftar.</p>
                                        </template>
                                        <template x-if="filteredEnrolledParticipants.length === 0 && searchTermUnenroll !== ''">
                                            <p class="text-gray-500 text-sm">Tidak ada peserta terdaftar yang cocok dengan pencarian Anda.</p>
                                        </template>
                                    </div>
                                </form>
                            @endif

                            <h4 class="text-lg font-bold text-gray-800 mb-3">Daftarkan Peserta Baru</h4>
                            <div class="mb-4">
                                {{-- Search input untuk peserta baru --}}
                                <input type="text" x-model="searchTermEnroll" placeholder="Cari nama atau email..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <form id="enroll-form" method="POST" action="{{ route('courses.enroll', $course) }}">
                                @csrf
                                {{-- Scrolling window untuk peserta yang belum terdaftar --}}
                                <div class="space-y-2 mb-4 max-h-60 overflow-y-auto border p-2 rounded-md">
                                    {{-- Menggunakan filteredUnEnrolledParticipants --}}
                                    <template x-for="user in filteredUnEnrolledParticipants" :key="user.id">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="user_ids[]" :value="user.id" x-model="selectedEnrollUsers" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2">
                                            <label class="text-sm text-gray-700" x-text="`${user.name} (${user.email})`"></label>
                                        </div>
                                    </template>
                                    <template x-if="filteredUnEnrolledParticipants.length === 0 && searchTermEnroll === ''">
                                        <p class="text-gray-500 text-sm">Tidak ada peserta yang tersedia untuk didaftarkan.</p>
                                    </template>
                                    <template x-if="filteredUnEnrolledParticipants.length === 0 && searchTermEnroll !== ''">
                                        <p class="text-gray-500 text-sm">Tidak ada peserta yang cocok dengan pencarian Anda.</p>
                                    </template>
                                </div>
                                @error('user_ids')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                                <button type="submit" :disabled="selectedEnrollUsers.length === 0" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ __('Daftarkan Terpilih') }} (<span x-text="selectedEnrollUsers.length"></span>)
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>