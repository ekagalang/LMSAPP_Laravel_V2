<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Peran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <!-- Role Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Peran')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        <!-- Permissions -->
                        <div class="mt-4">
                            <x-input-label for="permissions" :value="__('Hak Akses (Permissions)')" />

                            @php
                                $categories = [
                                    'Users & Roles' => ['users','roles'],
                                    'Courses' => ['course','courses'],
                                    'Classes / Periods' => ['class','classes','period'],
                                    'Lessons' => ['lesson','lessons'],
                                    'Contents' => ['content','contents','zoom'],
                                    'Attendance' => ['attendance'],
                                    'Quizzes & Essays' => ['quiz','quizzes','essay'],
                                    'Discussions' => ['discussion','discussions'],
                                    'Certificates' => ['certificate ' , 'certificates '],
                                    'Certificate Templates' => ['certificate template','certificate templates'],
                                    'Announcements' => ['announcement','announcements'],
                                    'Reports / Analytics' => ['report','reports','analytics','progress'],
                                    'Activity Logs' => ['activity log','activity logs'],
                                    'File Control' => ['file','files','upload'],
                                ];
                                $permsByGroup = [];
                                foreach ($permissions as $permission) {
                                    $name = $permission->name;
                                    $group = 'Lainnya';
                                    foreach ($categories as $label => $keywords) {
                                        foreach ($keywords as $kw) {
                                            if (\Illuminate\Support\Str::contains($name, $kw)) { $group = $label; break 2; }
                                        }
                                    }
                                    $permsByGroup[$group][] = $permission;
                                }
                                ksort($permsByGroup);
                            @endphp

                            <div class="mt-3">
                                <input type="text" id="permissionSearch" placeholder="Cari permission..." class="w-full md:w-1/2 border-gray-300 rounded-md" />
                            </div>

                            <div class="mt-3 space-y-6" id="permissionGroups">
                                @foreach ($permsByGroup as $group => $perms)
                                    <div class="border rounded-lg">
                                        <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b">
                                            <h3 class="text-sm font-semibold text-gray-700">{{ $group }}</h3>
                                            <div class="text-xs text-gray-600">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="group-toggle rounded border-gray-300 text-indigo-600" data-group="group_{{ \Illuminate\Support\Str::slug($group,'_') }}">
                                                    <span class="ml-2">Pilih Semua</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 p-4" id="group_{{ \Illuminate\Support\Str::slug($group,'_') }}">
                                            @foreach ($perms as $permission)
                                                <div class="flex items-center permission-item" data-name="{{ strtolower($permission->name) }}">
                                                    <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->name }}"
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-600">{{ $permission->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Peran') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle select all per group
            document.querySelectorAll('.group-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const groupId = this.getAttribute('data-group');
                    const container = document.getElementById(groupId);
                    if (!container) return;
                    container.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.checked = this.checked; });
                });
            });

            // Permission search filter
            const search = document.getElementById('permissionSearch');
            if (search) {
                search.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('.permission-item').forEach(item => {
                        const name = item.getAttribute('data-name');
                        item.style.display = name.includes(q) ? '' : 'none';
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
