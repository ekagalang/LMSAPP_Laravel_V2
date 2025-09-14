<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Konfirmasi Bergabung') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Konfirmasi untuk bergabung ke periode: {{ $period->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-500 to-blue-600 px-6 py-8 text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Token Valid!</h3>
                    <p class="text-green-100">Token yang Anda masukkan valid dan periode tersedia untuk bergabung</p>
                </div>

                <div class="p-8">
                    @if($errors->has('general'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-red-700 font-medium">{{ $errors->first('general') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Period Information -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Detail Periode</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Nama Periode</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $period->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Kursus</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $period->course->title }}</p>
                                        @if($period->course->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($period->course->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Status</label>
                                        <div class="mt-1">
                                            {!! $period->status_badge !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Periode Belajar</label>
                                        <p class="text-gray-900">
                                            <i class="fas fa-calendar mr-1 text-blue-500"></i>
                                            {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Durasi: {{ $period->getDurationInDays() }} hari
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Peserta</label>
                                        <p class="text-gray-900">
                                            <i class="fas fa-users mr-1 text-green-500"></i>
                                            {{ $period->getParticipantCount() }}{{ $period->max_participants ? '/' . $period->max_participants : '' }} peserta
                                        </p>
                                        @if($period->max_participants)
                                            <p class="text-sm text-gray-500 mt-1">
                                                Slot tersisa: {{ $period->getAvailableSlots() }}
                                            </p>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Instructor</label>
                                        @if($period->instructors->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($period->instructors->take(3) as $instructor)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $instructor->name }}
                                                    </span>
                                                @endforeach
                                                @if($period->instructors->count() > 3)
                                                    <span class="text-xs text-gray-500">+{{ $period->instructors->count() - 3 }} lainnya</span>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-gray-500 text-sm">Belum ada instructor</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Token Display -->
                    <div class="mb-8 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-sm font-medium text-indigo-900 mb-1">Token yang digunakan:</p>
                                <p class="text-2xl font-mono font-bold text-indigo-600">{{ $token }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="{{ route('join.confirm-period', $token) }}" method="POST">
                        @csrf

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('join.form') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Ya, Bergabung Sekarang!
                            </button>
                        </div>
                    </form>

                    <!-- Important Notes -->
                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h5 class="text-yellow-900 font-medium text-sm mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Penting untuk Diketahui:
                        </h5>
                        <ul class="text-xs text-yellow-800 space-y-1">
                            <li>• Setelah bergabung, Anda akan mendapat akses penuh ke semua materi kursus</li>
                            <li>• Anda dapat berinteraksi dengan instructor dan peserta lain dalam periode ini</li>
                            <li>• Pastikan Anda dapat mengikuti jadwal periode dari {{ $period->start_date->format('d M Y') }} hingga {{ $period->end_date->format('d M Y') }}</li>
                            @if($period->max_participants)
                                <li>• Periode ini memiliki batas maksimal {{ $period->max_participants }} peserta</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>