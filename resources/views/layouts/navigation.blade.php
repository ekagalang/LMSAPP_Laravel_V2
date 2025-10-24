<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-black-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <!-- Dashboard Link dengan styling custom -->
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out
                              {{ request()->routeIs('dashboard') 
                                  ? 'border-red-900 text-red-900' 
                                  : 'border-transparent text-black hover:text-red-900 hover:border-red-300' }}">
                        {{ __('Dashboard') }}
                    </a>

                    @can('view courses')
                        <a href="{{ route('courses.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out
                                  {{ request()->routeIs('courses.*') 
                                      ? 'border-red-900 text-red-900' 
                                      : 'border-transparent text-black hover:text-red-900 hover:border-red-300' }}">
                            {{ __('Kelola Kursus') }}
                        </a>
                    @endcan

                    @can('view progress reports')
                        <a href="{{ route('eo.courses.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out
                                  {{ request()->routeIs('eo.courses.index') 
                                      ? 'border-red-900 text-red-900' 
                                      : 'border-transparent text-black hover:text-red-900 hover:border-red-300' }}">
                            {{ __('Pemantauan Kursus') }}
                        </a>
                    @endcan

                    @can('view progress reports')
                        <a href="{{ route('certificate-management.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out
                                  {{ request()->routeIs('certificate-management.*') 
                                      ? 'border-red-900 text-red-900' 
                                      : 'border-transparent text-black hover:text-red-900 hover:border-red-300' }}">
                            {{ __('Manajemen Sertifikat') }}
                        </a>
                    @endcan

                    @can('view progress reports')
                        <a href="{{ route('instructor-analytics.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out
                                  {{ request()->routeIs('instructor-analytics.*') 
                                      ? 'border-red-900 text-red-900' 
                                      : 'border-transparent text-black hover:text-red-900 hover:border-red-300' }}">
                            {{ __('Analytics Instruktur') }}
                        </a>
                    @endcan
                    
                    <!-- Menu Admin berbasis izin -->
                    @canany(['manage users','manage roles','view certificate templates','view activity logs','view certificate analytics','view certificate management'])
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-base font-semibold rounded-md text-black bg-white hover:text-red-900 focus:outline-none transition ease-in-out duration-150">
                                        <div>Admin Menu</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="py-1">
                                        <a href="{{ route('admin.users.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Manajemen Pengguna') }}
                                        </a>
                                        <a href="{{ route('admin.tools.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Tools') }}
                                        </a>
                                        <a href="{{ route('admin.participants.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Data Peserta') }}
                                        </a>
                                        <a href="{{ route('admin.roles.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Manajemen Peran') }}
                                        </a>
                                        <a href="{{ route('admin.announcements.index') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Manajemen Pengumuman') }}
                                        </a>
                                        <a href="{{ route('admin.certificates.index') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Certificate Template') }}
                                        </a>
                                        <a href="{{ route('admin.auto-grade.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Auto Complete Grading') }}
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('file-control.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('File Control') }}
                                        </a>
                                        <a href="{{ route('activity-logs.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                            {{ __('Activity Logs') }}
                                        </a>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endcanany
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-base font-medium rounded-md text-black bg-white hover:text-red-900 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                {{ __('Profile') }}
                            </a>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-900 transition duration-150 ease-in-out">
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-red-900 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-red-900 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="block pl-3 pr-4 py-2 border-l-4 text-base font-semibold transition duration-150 ease-in-out
                      {{ request()->routeIs('dashboard') 
                          ? 'border-red-900 text-red-900 bg-red-50' 
                          : 'border-transparent text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300' }}">
                {{ __('Dashboard') }}
            </a>
            
            @can('view courses')
                <a href="{{ route('courses.index') }}" 
                   class="block pl-3 pr-4 py-2 border-l-4 text-base font-semibold transition duration-150 ease-in-out
                          {{ request()->routeIs('courses.*') 
                              ? 'border-red-900 text-red-900 bg-red-50' 
                              : 'border-transparent text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300' }}">
                    {{ __('Kelola Kursus') }}
                </a>
            @endcan

            @can('view progress reports')
                <a href="{{ route('eo.courses.index') }}" 
                   class="block pl-3 pr-4 py-2 border-l-4 text-base font-semibold transition duration-150 ease-in-out
                          {{ request()->routeIs('eo.courses.index') 
                              ? 'border-red-900 text-red-900 bg-red-50' 
                              : 'border-transparent text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300' }}">
                    {{ __('Pemantauan Kursus') }}
                </a>
            @endcan

            @can('view progress reports')
                <a href="{{ route('certificate-management.index') }}" 
                   class="block pl-3 pr-4 py-2 border-l-4 text-base font-semibold transition duration-150 ease-in-out
                          {{ request()->routeIs('certificate-management.*') 
                              ? 'border-red-900 text-red-900 bg-red-50' 
                              : 'border-transparent text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300' }}">
                    {{ __('Manajemen Sertifikat') }}
                </a>
            @endcan

            @can('view progress reports')
                <a href="{{ route('instructor-analytics.index') }}" 
                   class="block pl-3 pr-4 py-2 border-l-4 text-base font-semibold transition duration-150 ease-in-out
                          {{ request()->routeIs('instructor-analytics.*') 
                              ? 'border-red-900 text-red-900 bg-red-50' 
                              : 'border-transparent text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300' }}">
                    {{ __('Analytics Instruktur') }}
                </a>
            @endcan

            @canany(['manage users','manage roles','view certificate templates','view activity logs','view certificate analytics','view certificate management'])
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-semibold text-base text-gray-800">Admin Menu</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('admin.users.index') }}"
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                            {{ __('Manajemen Pengguna') }}
                        </a>
                        <a href="{{ route('admin.participants.index') }}"
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                            {{ __('Data Peserta') }}
                        </a>
                        <a href="{{ route('admin.roles.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                            {{ __('Manajemen Peran') }}
                        </a>
                        <a href="{{ route('admin.announcements.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                            {{ __('Manajemen Pengumuman') }}
                        </a>
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                            {{ __('Certificate Template') }}
                        </a>
                        <a href="{{ route('admin.auto-grade.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                            {{ __('Auto Complete Grading') }}
                        </a>
                    </div>
                </div>
            @endcanany
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-semibold text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" 
                   class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-black hover:text-red-900 hover:bg-red-50 hover:border-red-300 transition duration-150 ease-in-out">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
