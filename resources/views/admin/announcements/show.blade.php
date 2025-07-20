{{-- resources/views/admin/announcements/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pengumuman') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.announcements.edit', $announcement) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    Edit Pengumuman
                </a>
                <a href="{{ route('admin.announcements.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Announcement Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-{{ $announcement->level_color }}-500">
                <div class="p-8">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-4">
                                <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-{{ $announcement->level_color }}-100 text-{{ $announcement->level_color }}-800">
                                    @if($announcement->level === 'info')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @elseif($announcement->level === 'success')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @elseif($announcement->level === 'warning')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @endif
                                </span>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $announcement->title }}</h1>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $announcement->level_color }}-100 text-{{ $announcement->level_color }}-800">
                                            {{ ucfirst($announcement->level) }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $announcement->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="prose prose-lg max-w-none mb-8">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>

                    <!-- Meta Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Informasi Pengumuman</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm text-gray-500">Dibuat oleh</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $announcement->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Target Audiens</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $announcement->formatted_target_roles }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Dibuat pada</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $announcement->created_at->format('d M Y H:i') }}</dd>
                                </div>
                                @if($announcement->published_at)
                                <div>
                                    <dt class="text-sm text-gray-500">Dipublikasikan pada</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $announcement->published_at->format('d M Y H:i') }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Status & Waktu</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm text-gray-500">Status</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        @if($announcement->is_published)
                                            <span class="text-green-600">✓ Dipublikasikan</span>
                                        @elseif($announcement->published_at && $announcement->published_at->isFuture())
                                            <span class="text-yellow-600">⏱ Terjadwal</span>
                                        @else
                                            <span class="text-gray-600">📝 Draft</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($announcement->expires_at)
                                <div>
                                    <dt class="text-sm text-gray-500">Kadaluarsa pada</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ $announcement->expires_at->format('d M Y H:i') }}
                                        @if($announcement->is_expired)
                                            <span class="text-red-600 ml-2">(Kadaluarsa)</span>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm text-gray-500">Terakhir diperbarui</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $announcement->updated_at->format('d M Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Statistics -->
                    @if(isset($announcement->reads) && $announcement->reads->isNotEmpty())
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Statistik Pembacaan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $announcement->reads->count() }}</div>
                                <div class="text-sm text-blue-600">Total Pembaca</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $announcement->reads->where('read_at', '>=', now()->subDay())->count() }}</div>
                                <div class="text-sm text-green-600">Dibaca Hari Ini</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $announcement->reads->where('read_at', '>=', now()->subWeek())->count() }}</div>
                                <div class="text-sm text-purple-600">Minggu Ini</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="border-t pt-6 mt-6">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.announcements.toggle-status', $announcement) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 {{ $announcement->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition">
                                        @if($announcement->is_active)
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            </svg>
                                            Nonaktifkan
                                        @else
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Aktifkan
                                        @endif
                                    </button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
