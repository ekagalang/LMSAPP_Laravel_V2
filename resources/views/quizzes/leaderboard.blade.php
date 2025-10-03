@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        {{-- Header Section --}}
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🏆 Leaderboard</h1>
                    <h2 class="text-xl opacity-90">{{ $quiz->title }}</h2>
                    <p class="text-sm opacity-75 mt-2">{{ $quiz->description }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-6 py-3">
                        <p class="text-sm opacity-75">Total Partisipan</p>
                        <p class="text-3xl font-bold">{{ $leaderboard->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($leaderboard->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data</h3>
                <p class="text-gray-500">Belum ada yang menyelesaikan quiz ini.</p>
            </div>
        @else
            {{-- Top 3 Podium --}}
            @if($leaderboard->count() >= 3)
                <div class="grid grid-cols-3 gap-4 mb-8">
                    {{-- 2nd Place --}}
                    @if(isset($leaderboard[1]))
                        <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg p-6 text-center transform translate-y-8">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center mb-3 shadow-lg">
                                <span class="text-3xl font-bold text-white">2</span>
                            </div>
                            <div class="w-16 h-16 mx-auto mb-3 bg-gray-300 rounded-full flex items-center justify-center text-2xl font-bold text-gray-700">
                                {{ strtoupper(substr($leaderboard[1]['user']->name, 0, 1)) }}
                            </div>
                            <h3 class="font-bold text-lg text-gray-800 truncate">{{ $leaderboard[1]['user']->name }}</h3>
                            <p class="text-2xl font-bold text-gray-700 mt-2">{{ $leaderboard[1]['percentage'] }}%</p>
                            <p class="text-sm text-gray-600">{{ $leaderboard[1]['score'] }}/{{ $leaderboard[1]['total_marks'] }}</p>
                        </div>
                    @endif

                    {{-- 1st Place --}}
                    @if(isset($leaderboard[0]))
                        <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-lg p-6 text-center shadow-xl">
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center mb-3 shadow-lg animate-pulse">
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                            <div class="w-20 h-20 mx-auto mb-3 bg-yellow-400 rounded-full flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                                {{ strtoupper(substr($leaderboard[0]['user']->name, 0, 1)) }}
                            </div>
                            <h3 class="font-bold text-xl text-gray-800 truncate">{{ $leaderboard[0]['user']->name }}</h3>
                            <p class="text-3xl font-bold text-yellow-700 mt-2">{{ $leaderboard[0]['percentage'] }}%</p>
                            <p class="text-sm text-gray-700">{{ $leaderboard[0]['score'] }}/{{ $leaderboard[0]['total_marks'] }}</p>
                            @if($leaderboard[0]['duration'])
                                <p class="text-xs text-gray-600 mt-1">⏱️ {{ $leaderboard[0]['duration'] }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- 3rd Place --}}
                    @if(isset($leaderboard[2]))
                        <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg p-6 text-center transform translate-y-12">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-orange-300 to-orange-400 rounded-full flex items-center justify-center mb-3 shadow-lg">
                                <span class="text-3xl font-bold text-white">3</span>
                            </div>
                            <div class="w-16 h-16 mx-auto mb-3 bg-orange-300 rounded-full flex items-center justify-center text-2xl font-bold text-orange-700">
                                {{ strtoupper(substr($leaderboard[2]['user']->name, 0, 1)) }}
                            </div>
                            <h3 class="font-bold text-lg text-gray-800 truncate">{{ $leaderboard[2]['user']->name }}</h3>
                            <p class="text-2xl font-bold text-orange-700 mt-2">{{ $leaderboard[2]['percentage'] }}%</p>
                            <p class="text-sm text-gray-600">{{ $leaderboard[2]['score'] }}/{{ $leaderboard[2]['total_marks'] }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Full Leaderboard Table --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Semua Peringkat</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Peringkat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Skor</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Persentase</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Durasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($leaderboard as $entry)
                                <tr class="hover:bg-gray-50 transition-colors {{ $entry['rank'] <= 3 ? 'bg-yellow-50/30' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($entry['rank'] == 1)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 text-white font-bold shadow-md">
                                                    {{ $entry['rank'] }}
                                                </span>
                                            @elseif($entry['rank'] == 2)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 text-white font-bold shadow-md">
                                                    {{ $entry['rank'] }}
                                                </span>
                                            @elseif($entry['rank'] == 3)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-orange-300 to-orange-400 text-white font-bold shadow-md">
                                                    {{ $entry['rank'] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold">
                                                    {{ $entry['rank'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold mr-3">
                                                {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $entry['user']->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $entry['user']->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-lg font-semibold text-gray-900">{{ $entry['score'] }}</span>
                                        <span class="text-sm text-gray-500">/{{ $entry['total_marks'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center">
                                            <div class="w-24">
                                                <div class="flex items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="h-2 rounded-full {{ $entry['percentage'] >= 80 ? 'bg-green-500' : ($entry['percentage'] >= 60 ? 'bg-blue-500' : 'bg-yellow-500') }}"
                                                             style="width: {{ $entry['percentage'] }}%"></div>
                                                    </div>
                                                    <span class="text-sm font-semibold {{ $entry['percentage'] >= 80 ? 'text-green-600' : ($entry['percentage'] >= 60 ? 'text-blue-600' : 'text-yellow-600') }}">
                                                        {{ $entry['percentage'] }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($entry['passed'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Lulus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                ✗ Tidak Lulus
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">
                                        {{ $entry['completed_at']->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">
                                        @if($entry['duration'])
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $entry['duration'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Back Button --}}
        <div class="mt-8 text-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
