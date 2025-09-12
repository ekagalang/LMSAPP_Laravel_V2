@extends('layouts.app')

@section('title', 'Force Complete Contents')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Force Complete Contents</h1>

        <!-- Course Selection Form -->
        <form method="GET" action="{{ route('admin.force-complete.index') }}" class="mb-6">
            <div class="mb-4">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Kursus</label>
                <select id="course_id" name="course_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Pilih Kursus --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (isset($selectedCourse) && $selectedCourse && $selectedCourse->id == $course->id) ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tampilkan Peserta
            </button>
        </form>

        @if(isset($selectedCourse) && $selectedCourse)
            <div class="border-b border-gray-200 pb-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <h2 class="text-xl font-semibold text-gray-700">Kursus: {{ $selectedCourse->title }}</h2>

                    @if($participants->count() > 0)
                        <form method="POST" action="{{ route('admin.force-complete.complete-all') }}" class="inline-flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                            <label class="inline-flex items-center text-sm">
                                <input type="checkbox" name="generate_certificate" value="1" class="mr-2">
                                Generate sertifikat untuk peserta yang eligible
                            </label>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm" onclick="return confirm('Yakin ingin menandai SELESAI semua konten untuk SEMUA peserta di kursus ini?')">
                                Force Complete Semua Peserta
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($participants->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($participants as $p)
                        @php
                            $u = $p['user'];
                            $progress = $p['progress'];
                        @endphp
                        <div class="border rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $u->name }}</div>
                                <div class="text-gray-500 text-sm">{{ $u->email }}</div>
                                <div class="text-sm mt-1">Progres: <span class="font-medium">{{ $progress['progress_percentage'] ?? 0 }}%</span> ({{ $progress['completed_count'] ?? 0 }}/{{ $progress['total_count'] ?? 0 }})</div>
                            </div>
                            <form method="POST" action="{{ route('admin.force-complete.complete') }}" class="flex items-center gap-3">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                <label class="inline-flex items-center text-sm">
                                    <input type="checkbox" name="generate_certificate" value="1" class="mr-2">
                                    Generate sertifikat (jika memenuhi syarat)
                                </label>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm" onclick="return confirm('Yakin ingin menandai SELESAI semua konten untuk {{ $u->name }}?')">
                                    Force Complete Peserta
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Tidak ada peserta pada kursus ini.</p>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Silakan pilih kursus terlebih dahulu.</p>
            </div>
        @endif
    </div>
    <p class="text-xs text-gray-400 mt-4">Catatan: Force Complete akan menandai konten non-quiz sebagai selesai, membuat attempt lulus untuk quiz (jika belum), dan menyelesaikan essay dengan skor 0/feedback default sesuai mode. Opsi generate sertifikat hanya berlaku jika peserta memenuhi syarat.</p>
</div>
@endsection

