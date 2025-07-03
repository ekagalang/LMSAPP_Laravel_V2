<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gradebook: {{ $course->title }}
                </h2>
            </div>
            <div>
                <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    &larr; Kembali ke Kursus
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('courses.gradebook', $course) }}" method="GET" class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-grow">
                                <x-text-input type="text" name="search" class="w-full" placeholder="Cari nama atau email peserta..." value="{{ request('search') }}" />
                            </div>
                            <div>
                                <x-primary-button type="submit">Cari</x-primary-button>
                            </div>
                            <div>
                                <a href="{{ route('courses.gradebook', $course) }}" class="text-sm text-gray-600 hover:text-gray-900">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Peserta
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($participants as $participant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $participant->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $participant->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('gradebook.feedback', ['course' => $course, 'user' => $participant]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Lihat Feedback
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($quizzes) + 1 }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                            @if(request('search'))
                                                Tidak ada peserta yang cocok dengan pencarian Anda.
                                            @else
                                                Belum ada peserta yang terdaftar di kursus ini.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
