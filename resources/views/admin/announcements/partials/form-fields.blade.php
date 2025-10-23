@php
    // Setel nilai default jika variabel $announcement tidak ada (untuk halaman create)
    $announcement = $announcement ?? new \App\Models\Announcement();
@endphp

<div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8">
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                Detail Pengumuman
            </h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Lengkapi informasi pengumuman yang akan dipublikasikan
            </p>
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Judul Pengumuman -->
                <div class="group">
                    <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Judul Pengumuman
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title', $announcement->title) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500" 
                               placeholder="Masukkan judul pengumuman..."
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('title') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Level Pengumuman -->
                <div class="group">
                    <label for="level" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Level Prioritas
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <select name="level" 
                            id="level" 
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500">
                        <option value="info" {{ old('level', $announcement->level) == 'info' ? 'selected' : '' }}>
                            🔵 Info - Informasi Umum
                        </option>
                        <option value="success" {{ old('level', $announcement->level) == 'success' ? 'selected' : '' }}>
                            🟢 Success - Berita Positif
                        </option>
                        <option value="warning" {{ old('level', $announcement->level) == 'warning' ? 'selected' : '' }}>
                            🟡 Warning - Perhatian Khusus
                        </option>
                        <option value="danger" {{ old('level', $announcement->level) == 'danger' ? 'selected' : '' }}>
                            🔴 Danger - Urgent/Penting
                        </option>
                    </select>
                    @error('level') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Tanggal Publikasi -->
                <div class="group">
                    <label for="published_at" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Tanggal Publikasi
                            <span class="text-gray-500 text-xs ml-2">(Opsional)</span>
                        </span>
                    </label>
                    <input type="datetime-local" 
                           name="published_at" 
                           id="published_at" 
                           value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500">
                    <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-xs text-blue-700 dark:text-blue-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <strong>Tips:</strong> Kosongkan jika ingin menyimpan sebagai draft untuk dipublikasikan nanti.
                        </p>
                    </div>
                    @error('published_at') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Isi Konten -->
                <div class="group">
                    <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Isi Pengumuman
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="6" 
                              class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500 resize-none" 
                              placeholder="Tulis detail pengumuman di sini. Jelaskan informasi yang ingin disampaikan dengan jelas dan lengkap..."
                              required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Target Pengumuman -->
                <div class="group">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Target Penerima
                        </span>
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border-2 border-gray-200 dark:border-gray-600">
                        @php
                            // Dynamic roles to support custom roles
                            $roles = \Spatie\Permission\Models\Role::pluck('name')->toArray();
                            $selectedRoles = old('target_roles', $announcement->target_roles ?? []);
                            $defaultRoleLabels = [
                                'participant' => ['label' => 'Peserta', 'icon' => '👥', 'desc' => 'Semua peserta event'],
                                'instructor' => ['label' => 'Instruktur', 'icon' => '🎓', 'desc' => 'Para pengajar dan mentor'],
                                'event-organizer' => ['label' => 'Event Organizer', 'icon' => '📋', 'desc' => 'Penyelenggara acara'],
                                'super-admin' => ['label' => 'Super Admin', 'icon' => '🛡️', 'desc' => 'Administrator sistem']
                            ];
                            $roleLabels = [];
                            foreach ($roles as $r) {
                                $pretty = ucfirst(str_replace(['-', '_'], ' ', $r));
                                $roleLabels[$r] = $defaultRoleLabels[$r] ?? [
                                    'label' => $pretty,
                                    'icon' => '🏷️',
                                    'desc' => 'Peran kustom'
                                ];
                            }
                        @endphp
                        
                        <!-- Semua Pengguna Option -->
                        <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg border-2 border-transparent hover:border-indigo-200 dark:hover:border-indigo-700 transition-all duration-200">
                            <label for="role_all" class="flex items-center cursor-pointer group">
                                <input type="checkbox" 
                                       id="role_all" 
                                       name="target_roles[]" 
                                       value="all" 
                                       class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2 transition-all duration-200"
                                       @if(empty($selectedRoles) || in_array('all', $selectedRoles)) checked @endif>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <span class="text-lg mr-2">🌐</span>
                                        <span class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            Semua Pengguna
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Kirim ke seluruh pengguna platform
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Individual Role Options -->
                        <div class="space-y-3">
                            @foreach($roles as $role)
                                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border-2 border-transparent hover:border-indigo-200 dark:hover:border-indigo-700 transition-all duration-200">
                                    <label for="role_{{ $role }}" class="flex items-center cursor-pointer group">
                                        <input type="checkbox" 
                                               id="role_{{ $role }}" 
                                               name="target_roles[]" 
                                               value="{{ $role }}" 
                                               class="w-5 h-5 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2 transition-all duration-200"
                                               @if(in_array($role, $selectedRoles)) checked @endif>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <span class="text-lg mr-2">{{ $roleLabels[$role]['icon'] ?? '🏷️' }}</span>
                                                <span class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                    {{ $roleLabels[$role]['label'] ?? ucfirst($role) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $roleLabels[$role]['desc'] ?? 'Peran kustom' }}</p>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk interaktivitas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const textarea = document.getElementById('content');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Checkbox interaction logic
    const allCheckbox = document.getElementById('role_all');
    const roleCheckboxes = document.querySelectorAll('input[name="target_roles[]"]:not(#role_all)');
    
    allCheckbox.addEventListener('change', function() {
        if (this.checked) {
            roleCheckboxes.forEach(cb => cb.checked = false);
        }
    });
    
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                allCheckbox.checked = false;
            }
        });
    });
});
</script>
