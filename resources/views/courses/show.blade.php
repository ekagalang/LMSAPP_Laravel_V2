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
            @can('update', $course) {{-- Hanya instruktur pembuat kursus atau admin --}}
                <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Kursus') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- ... (bagian detail kursus seperti sebelumnya) ... --}}

            <div x-data="{ currentTab: 'lessons' }" class="mt-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="currentTab = 'lessons'" :class="{'border-indigo-500 text-indigo-600': currentTab === 'lessons', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'lessons'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pelajaran & Konten
                        </button>
                        @can('update', $course) {{-- Hanya instruktur/admin yang bisa melihat tab peserta --}}
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
                    <div x-show="currentTab === 'participants'" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Daftar Peserta Terdaftar</h3>

                            @if ($course->participants->isEmpty())
                                <p class="text-center text-gray-500 mb-4">Belum ada peserta yang terdaftar di kursus ini.</p>
                            @else
                                <ul class="space-y-2 mb-6">
                                    @foreach ($course->participants as $participant)
                                        <li class="flex justify-between items-center bg-gray-50 p-3 rounded-md shadow-sm border border-gray-200">
                                            <span class="font-medium text-gray-800">{{ $participant->name }} ({{ $participant->email }})</span>
                                            <form action="{{ route('courses.unenroll', [$course, $participant]) }}" method="POST" onsubmit="return confirm('Yakin ingin mencabut akses peserta ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Cabut Akses
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <h4 class="text-lg font-bold text-gray-800 mb-3">Daftarkan Peserta Baru</h4>
                            <form method="POST" action="{{ route('courses.enroll', $course) }}" class="flex flex-col sm:flex-row gap-4">
                                @csrf
                                <div class="flex-grow">
                                    <label for="user_id" class="sr-only">Pilih Peserta</label>
                                    <select name="user_id" id="user_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Pilih Peserta...</option>
                                        @foreach (\App\Models\User::where('role', 'participant')->orderBy('name')->get() as $user)
                                            {{-- Hanya tampilkan yang belum terdaftar --}}
                                            @if (!$course->participants->contains($user->id))
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Daftarkan Peserta') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>