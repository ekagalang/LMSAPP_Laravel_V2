<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Kursus') }}
            </h2>
            @can('create', App\Models\Course::class)
                <a href="{{ route('courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Tambah Kursus Baru') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($courses->isEmpty())
                        <p class="text-center text-gray-500">Belum ada kursus yang dibuat.</p>
                        @can('create', App\Models\Course::class)
                            <p class="text-center mt-4">
                                <a href="{{ route('courses.create') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Mulai buat kursus pertama Anda!</a>
                            </p>
                        @endcan
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($courses as $course)
                                <div class="bg-gray-50 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                    @if ($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                            Tidak Ada Gambar
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                                        <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
                                            {{-- ✅ PERUBAHAN DI SINI --}}
                                            <span>Oleh: {{ $course->instructors->pluck('name')->join(', ') }}</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Lihat
                                            </a>
                                            @can('update', $course)
                                                <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Edit
                                                </a>
                                            @endcan
                                            @can('duplicate', App\Models\Course::class)
                                                <form action="{{ route('courses.duplicate', $course) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Anda yakin ingin menduplikasi kursus ini?');">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Duplikat</button>
                                                </form>
                                            @endcan
                                            @can('delete', $course)
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>