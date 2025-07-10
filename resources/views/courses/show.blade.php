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
            <div class="flex space-x-2">
            @can('grade quizzes')
                <a href="{{ route('courses.gradebook', $course) }}" class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600">
                    {{ __('Buku Nilai & Feedback') }}
                </a
            @endcan
            @can('view progress reports')
                <a href="{{ route('courses.progress', $course) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    {{ __('Lihat Progres') }}
                </a>
            @endcan
            @can('update', $course)
                <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Kursus') }}
                </a>
            @endcan
            </div>
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
                        <button @click="currentTab = 'lessons'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'lessons', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'lessons'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pelajaran & Konten</button>
                        
                        {{-- Munculkan tab ini hanya jika user punya izin --}}
                        @can('update', $course)
                            {{-- ✅ TAB BARU UNTUK MANAJEMEN PENGELOLA --}}
                            <button @click="currentTab = 'managers'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'managers', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'managers'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pengelola Kursus</button>

                            <button @click="currentTab = 'participants'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'participants', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'participants'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Peserta Kursus</button>
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
                                                    <a href="{{ route('courses.lessons.edit', [$course, $lesson]) }}"
                                                    class="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-sm font-semibold rounded hover:bg-purple-700">
                                                        Edit
                                                    </a>

                                                    <form action="{{ route('courses.lessons.destroy', [$course, $lesson]) }}" method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus pelajaran ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm font-semibold rounded hover:bg-red-700">
                                                            Hapus
                                                        </button>
                                                    </form>

                                                    <a href="{{ route('lessons.contents.create', $lesson) }}"
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm font-semibold rounded hover:bg-green-700">
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
                                                                <div class="flex gap-2">
                                                                    <a href="{{ route('lessons.contents.edit', [$lesson, $content]) }}"
                                                                    class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2 px-4 rounded">
                                                                        Edit
                                                                    </a>

                                                                    <form action="{{ route('lessons.contents.destroy', [$lesson, $content]) }}" method="POST"
                                                                        onsubmit="return confirm('Yakin ingin menghapus konten ini?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 px-4 rounded">
                                                                            Hapus
                                                                        </button>
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
                    <div x-show="currentTab === 'managers'" x-cloak class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 mb-4">Instruktur Ditugaskan</h4>
                                <form action="{{ route('courses.removeInstructor', $course) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus instruktur terpilih?');">
                                    @csrf
                                    @method('DELETE')
                                    <div class="space-y-2 mb-4 max-h-60 overflow-y-auto border p-2 rounded-md">
                                        {{-- ✅ PERUBAHAN DI SINI --}}
                                        @forelse($course->instructors as $instructor)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="user_ids[]" value="{{ $instructor->id }}" id="instructor-{{$instructor->id}}">
                                                <label for="instructor-{{$instructor->id}}" class="ml-2">{{ $instructor->name }}</label>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 p-2">Belum ada instruktur.</p>
                                        @endforelse
                                    </div>
                                    @if($course->instructors->isNotEmpty())
                                        <x-danger-button type="submit">Hapus Terpilih</x-danger-button>
                                    @endif
                                </form>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 mb-4">Tambahkan Instruktur Baru</h4>
                                <form action="{{ route('courses.addInstructor', $course) }}" method="POST">
                                    @csrf
                                    <div class="space-y-2 mb-4 max-h-60 overflow-y-auto border p-2 rounded-md">
                                        @forelse($availableInstructors as $instructor)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="user_ids[]" value="{{ $instructor->id }}" id="avail-instructor-{{$instructor->id}}">
                                                <label for="avail-instructor-{{$instructor->id}}" class="ml-2">{{ $instructor->name }}</label>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 p-2">Semua instruktur sudah ditugaskan.</p>
                                        @endforelse
                                    </div>
                                    @if($availableInstructors->isNotEmpty())
                                        <x-primary-button type="submit">Tambahkan</x-primary-button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div x-show="currentTab === 'participants'" x-cloak class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg"
                         x-data="{
                            selectedEnrollUsers: [],
                            selectedUnenrollUsers: [],
                            searchTermEnroll: '',
                            searchTermUnenroll: '',
                            unEnrolledParticipantsData: {{ Js::from($unEnrolledParticipants) }},
                            enrolledParticipantsData: {{ Js::from($course->enrolledUsers) }},
                            get filteredUnEnrolledParticipants() {
                                if (this.searchTermEnroll === '') return this.unEnrolledParticipantsData;
                                return this.unEnrolledParticipantsData.filter(user => 
                                    user.name.toLowerCase().includes(this.searchTermEnroll.toLowerCase()) || 
                                    user.email.toLowerCase().includes(this.searchTermEnroll.toLowerCase())
                                );
                            },
                            get filteredEnrolledParticipants() {
                                if (this.searchTermUnenroll === '') return this.enrolledParticipantsData;
                                return this.enrolledParticipantsData.filter(user => 
                                    user.name.toLowerCase().includes(this.searchTermUnenroll.toLowerCase()) || 
                                    user.email.toLowerCase().includes(this.searchTermUnenroll.toLowerCase())
                                );
                            }
                        }">
                        <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 mb-4">Peserta Terdaftar</h4>
                                @if($course->enrolledUsers->isEmpty())
                                    <p class="text-center text-gray-500">Belum ada peserta.</p>
                                @else
                                    <form id="unenroll-form" method="POST" action="{{ route('courses.unenroll_mass', $course) }}" onsubmit="return confirm('Anda yakin ingin mencabut akses peserta terpilih?');">
                                        @csrf
                                        @method('DELETE')
                                        <div class="mb-4">
                                            <input type="text" x-model="searchTermUnenroll" placeholder="Cari peserta terdaftar..." class="block w-full rounded-md border-gray-300 shadow-sm">
                                        </div>
                                        <div class="space-y-2 mb-4 max-h-60 overflow-y-auto border p-2 rounded-md">
                                            <template x-for="participant in filteredEnrolledParticipants" :key="participant.id">
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="user_ids[]" :value="participant.id" x-model="selectedUnenrollUsers">
                                                    <label class="ml-2" x-text="`${participant.name} (${participant.email})`"></label>
                                                </div>
                                            </template>
                                            <template x-if="filteredEnrolledParticipants.length === 0">
                                                <p class="text-gray-500 p-2">Tidak ada hasil.</p>
                                            </template>
                                        </div>
                                        <x-danger-button type="submit" x-bind:disabled="selectedUnenrollUsers.length === 0">Cabut Akses Terpilih</x-danger-button>
                                    </form>
                                @endif
                            </div>
                            
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 mb-4">Daftarkan Peserta Baru</h4>
                                <form id="enroll-form" method="POST" action="{{ route('courses.enroll', $course) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <input type="text" x-model="searchTermEnroll" placeholder="Cari calon peserta..." class="block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div class="space-y-2 mb-4 max-h-60 overflow-y-auto border p-2 rounded-md">
                                        <template x-for="user in filteredUnEnrolledParticipants" :key="user.id">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="user_ids[]" :value="user.id" x-model="selectedEnrollUsers">
                                                <label class="ml-2" x-text="`${user.name} (${user.email})`"></label>
                                            </div>
                                        </template>
                                        <template x-if="filteredUnEnrolledParticipants.length === 0">
                                            <p class="text-gray-500 p-2">Tidak ada peserta yang tersedia.</p>
                                        </template>
                                    </div>
                                    <x-primary-button type="submit" x-bind:disabled="selectedEnrollUsers.length === 0">Daftarkan Terpilih</x-primary-button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>