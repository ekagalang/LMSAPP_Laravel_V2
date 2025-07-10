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
                    </a>
                @endcan
                @can('view progress reports')
                    <a href="{{ route('courses.progress', $course) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        {{ __('Lihat Progres') }}
                    </a>
                @endcan
                @can('update', $course)
                    <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
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
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div x-data="{ currentTab: 'lessons' }" class="mt-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="currentTab = 'lessons'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'lessons', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'lessons'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pelajaran & Konten</button>
                        @can('update', $course)
                            <button @click="currentTab = 'managers'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'managers', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'managers'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pengelola Kursus</button>
                            <button @click="currentTab = 'participants'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'participants', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'participants'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Peserta Kursus</button>
                        @endcan
                    </nav>
                </div>

                <div x-show="currentTab === 'lessons'" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900" 
                         x-data="{
                            lessons: {{ Js::from($course->lessons->sortBy('order')->values()) }},
                            activeAccordion: null,
                            moveUp(index) {
                                if (index === 0) return;
                                [this.lessons[index - 1], this.lessons[index]] = [this.lessons[index], this.lessons[index - 1]];
                                this.updateOrderOnServer();
                            },
                            moveDown(index) {
                                if (index === this.lessons.length - 1) return;
                                [this.lessons[index], this.lessons[index + 1]] = [this.lessons[index + 1], this.lessons[index]];
                                this.updateOrderOnServer();
                            },
                            updateOrderOnServer() {
                                const orderedIds = this.lessons.map(lesson => lesson.id);
                                fetch('{{ route('lessons.update_order') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ lessons: orderedIds })
                                });
                            }
                         }">
                        
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Daftar Pelajaran</h3>
                            @can('update', $course)
                                <a href="{{ route('courses.lessons.create', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    {{ __('Tambah Pelajaran') }}
                                </a>
                            @endcan
                        </div>

                        <p x-show="lessons.length === 0" class="text-center text-gray-500">Belum ada pelajaran dalam kursus ini.</p>

                        <div class="space-y-3">
                            <template x-for="(lesson, index) in lessons" :key="lesson.id">
                                <div class="bg-gray-50 rounded-lg shadow-sm">
                                    <div class="p-4 flex justify-between items-center">
                                        <div class="flex items-center flex-grow">
                                            {{-- ✅ Tombol Naik/Turun --}}
                                            @can('update', $course)
                                                <div class="flex flex-col mr-4">
                                                    <button @click="moveUp(index)" :disabled="index === 0" :class="{'opacity-25 cursor-not-allowed': index === 0}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                    </button>
                                                    <button @click="moveDown(index)" :disabled="index === lessons.length - 1" :class="{'opacity-25 cursor-not-allowed': index === lessons.length - 1}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </button>
                                                </div>
                                            @endcan
                                            <h4 class="text-lg font-semibold text-gray-800" x-text="lesson.title"></h4>
                                        </div>
                                        <div class="flex items-center space-x-4 flex-shrink-0">
                                            @can('update', $course)
                                                <a :href="`/courses/{{$course->id}}/lessons/${lesson.id}/edit`" class="text-purple-600 hover:text-purple-900 text-sm">Edit</a>
                                                <form :action="`/courses/{{$course->id}}/lessons/${lesson.id}`" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelajaran ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                                </form>
                                                <a :href="`/lessons/${lesson.id}/contents/create`" class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-md hover:bg-green-600">Tambah Konten</a>
                                            @endcan
                                            <button @click="activeAccordion = (activeAccordion === lesson.id) ? null : lesson.id" class="p-1 rounded-full hover:bg-gray-200">
                                                <svg class="w-6 h-6 text-gray-600 transition-transform" :class="{'rotate-180': activeAccordion === lesson.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div x-show="activeAccordion === lesson.id" x-collapse.duration.300ms class="border-t border-gray-200">
                                        <div class="p-4">
                                            <p class="text-gray-600 text-sm mb-4" x-text="lesson.description || 'Tidak ada deskripsi.'"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
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