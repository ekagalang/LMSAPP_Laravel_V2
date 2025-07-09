<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pemantauan Progres Kursus
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($courses as $course)
                            <div class="border rounded-lg p-4 flex flex-col justify-between hover:shadow-md transition">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">{{ $course->title }}</h3>
                                    {{-- ✅ PERUBAHAN DI SINI --}}
                                    <p class="text-sm text-gray-500">Oleh: {{ $course->instructors->pluck('name')->join(', ') }}</p>
                                </div>
                                <div class="mt-4 text-right">
                                    <a href="{{ route('courses.progress', $course) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        Lihat Progres
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 col-span-full text-center">Belum ada kursus yang dipublikasikan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>