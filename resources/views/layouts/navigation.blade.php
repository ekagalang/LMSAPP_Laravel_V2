<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- ✅ Menggunakan izin yang ada di file Anda: 'view courses' --}}
                    @can('view courses')
                        <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                            {{ __('Kelola Kursus') }}
                        </x-nav-link>
                    @endcan

                    {{-- ✅ Menggunakan izin yang ada di file Anda: 'view progress reports' --}}
                    @can('view progress reports')
                         <x-nav-link :href="route('eo.courses.index')" :active="request()->routeIs('eo.courses.index')">
                            {{ __('Pemantauan Kursus') }}
                        </x-nav-link>
                    @endcan
                    
                    {{-- Menu khusus Super Admin --}}
                    @role('super-admin')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Manajemen Pengguna') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                            {{ __('Manajemen Peran') }}
                        </x-nav-link>
                    @endrole
                </div>
            </div>

            </div>
    </div>
</nav>