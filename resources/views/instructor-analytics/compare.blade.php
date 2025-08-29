<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 -mx-4 -my-2 px-4 py-8 sm:px-6 lg:px-8 rounded-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <nav class="flex text-sm text-orange-100 mb-2" aria-label="Breadcrumb">
                        <a href="{{ route('instructor-analytics.index') }}" class="hover:text-white">Analytics Instruktur</a>
                        <span class="mx-2">›</span>
                        <span class="text-white">Bandingkan Instruktur</span>
                    </nav>
                    <h2 class="text-white text-3xl font-bold leading-tight">
                        Perbandingan Aktivitas Instruktur
                    </h2>
                    <p class="text-orange-100 mt-2">
                        {{ __('Bandingkan performa dan aktivitas beberapa instruktur') }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('instructor-analytics.index') }}" 
                       class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Selection Form -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Instruktur untuk Dibandingkan</h3>
                <form method="GET" action="{{ route('instructor-analytics.compare') }}" class="space-y-4">
                    
                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                    
                    <!-- Instructor Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Instruktur (maksimal 5)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($allInstructors as $instructor)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="instructor_{{ $instructor->id }}" 
                                           name="instructors[]" 
                                           value="{{ $instructor->id }}"
                                           {{ in_array($instructor->id, $instructorIds ?? []) ? 'checked' : '' }}
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <label for="instructor_{{ $instructor->id }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $instructor->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <button type="reset" 
                                class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Reset
                        </button>
                        <button type="submit" 
                                class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Bandingkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($compareData) && count($compareData) > 0)
            <!-- Comparison Results -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Hasil Perbandingan ({{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }})
                    </h3>
                </div>
                
                <!-- Comparison Chart -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <!-- Discussion Comparison -->
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-4">Aktivitas Diskusi</h4>
                            <div class="space-y-3">
                                @php
                                    $maxDiscussions = collect($compareData)->max('discussions');
                                @endphp
                                @foreach($compareData as $data)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-700">{{ Str::limit($data['instructor']->name, 20) }}</span>
                                            <span class="font-medium">{{ $data['discussions'] }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-purple-600 h-2 rounded-full" 
                                                 style="width: {{ $maxDiscussions > 0 ? ($data['discussions'] / $maxDiscussions) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Grading Comparison -->
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-4">Penilaian Essay</h4>
                            <div class="space-y-3">
                                @php
                                    $maxGrading = collect($compareData)->max('grading');
                                @endphp
                                @foreach($compareData as $data)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-700">{{ Str::limit($data['instructor']->name, 20) }}</span>
                                            <span class="font-medium">{{ $data['grading'] }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" 
                                                 style="width: {{ $maxGrading > 0 ? ($data['grading'] / $maxGrading) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Total Activity Comparison -->
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-4">Total Aktivitas</h4>
                            <div class="space-y-3">
                                @php
                                    $maxTotal = collect($compareData)->max('total');
                                @endphp
                                @foreach($compareData as $data)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-700">{{ Str::limit($data['instructor']->name, 20) }}</span>
                                            <span class="font-medium">{{ $data['total'] }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ $maxTotal > 0 ? ($data['total'] / $maxTotal) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Comparison Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Perbandingan</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Instruktur
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Kursus
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Diskusi
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Essay Dinilai
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Aktivitas
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rata-rata per Kursus
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ranking
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $sortedData = collect($compareData)->sortByDesc('total')->values();
                            @endphp
                            @foreach($sortedData as $index => $data)
                                <tr class="{{ $index === 0 ? 'bg-yellow-50' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($index === 0)
                                                <div class="flex-shrink-0 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-yellow-900 text-xs font-bold">👑</span>
                                                </div>
                                            @endif
                                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold">
                                                    {{ strtoupper(substr($data['instructor']->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $data['instructor']->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $data['instructor']->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $data['courses_count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $data['discussions'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $data['grading'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $data['total'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $data['courses_count'] > 0 ? number_format($data['total'] / $data['courses_count'], 1) : '0' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($index === 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                #1 🏆
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                #{{ $index + 1 }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Performance Insights -->
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Insights Performa</h3>
                </div>
                <div class="p-6">
                    @php
                        $topPerformer = $sortedData->first();
                        $avgTotal = $sortedData->avg('total');
                        $avgDiscussions = $sortedData->avg('discussions');
                        $avgGrading = $sortedData->avg('grading');
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">🏆</div>
                            <div class="text-sm text-gray-600 mt-1">Top Performer</div>
                            <div class="font-medium text-gray-900">{{ $topPerformer['instructor']->name ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($avgTotal, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Rata-rata Total Aktivitas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($avgDiscussions, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Rata-rata Diskusi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($avgGrading, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Rata-rata Penilaian</div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif(request()->has('instructors'))
            <!-- No Data Message -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <div class="text-6xl mb-4">📊</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data untuk dibandingkan</h3>
                        <p class="text-sm text-gray-500">Pilih minimal 2 instruktur untuk melakukan perbandingan.</p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="instructors[]"]');
    const maxSelections = 5;
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('input[name="instructors[]"]:checked');
            
            if (checkedBoxes.length >= maxSelections) {
                checkboxes.forEach(cb => {
                    if (!cb.checked) {
                        cb.disabled = true;
                    }
                });
            } else {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                });
            }
        });
    });
});
</script>
</x-app-layout>