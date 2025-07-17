@php
    // Setel nilai default jika variabel $announcement tidak ada (untuk halaman create)
    $announcement = $announcement ?? new \App\Models\Announcement();
@endphp

<!-- Judul Pengumuman -->
<div class="mb-4">
    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
    <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600" required>
    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<!-- Isi Konten -->
<div class="mb-4">
    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pengumuman</label>
    <textarea name="content" id="content" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600" required>{{ old('content', $announcement->content) }}</textarea>
    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<!-- Level Pengumuman -->
<div class="mb-4">
    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Level</label>
    <select name="level" id="level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
        <option value="info" {{ old('level', $announcement->level) == 'info' ? 'selected' : '' }}>Info (Biru)</option>
        <option value="success" {{ old('level', $announcement->level) == 'success' ? 'selected' : '' }}>Success (Hijau)</option>
        <option value="warning" {{ old('level', $announcement->level) == 'warning' ? 'selected' : '' }}>Warning (Kuning)</option>
        <option value="danger" {{ old('level', $announcement->level) == 'danger' ? 'selected' : '' }}>Danger (Merah)</option>
    </select>
    @error('level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<!-- Tanggal Publikasi -->
<div class="mb-4">
    <label for="published_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Publikasi (Opsional)</label>
    <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
    <p class="text-xs text-gray-500 mt-1">Kosongkan jika ingin menyimpan sebagai draft.</p>
    @error('published_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>