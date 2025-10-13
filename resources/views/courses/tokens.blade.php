<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('courses.show', $course) }}"
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium mb-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Course
                </a>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Token Enrollment Management
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="text-sm text-gray-500">
                    <svg class="w-5 h-5 inline mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Token Management
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Course Token -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl mb-6" x-data="{
                showCourseToken: false,
                courseTokenValue: '{{ $course->enrollment_token ?? '' }}'
            }">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Token Course
                    </h3>
                    <p class="text-purple-100 text-sm mt-1">Token untuk enrollment langsung ke course (tanpa kelas tertentu)</p>
                </div>

                <div class="p-6">
                    @if($course->enrollment_token)
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-6 border-2 border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Token Aktif</h4>
                                    <p class="text-xs text-gray-500">Peserta bisa join course dengan token ini</p>
                                </div>
                                @if($course->token_enabled)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Disabled
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center space-x-3 mb-4">
                                <code class="flex-1 bg-white px-4 py-3 rounded-lg font-mono text-xl text-purple-700 font-bold border-2 border-purple-300"
                                      x-text="showCourseToken ? courseTokenValue : '••••••••'"></code>
                                <button type="button"
                                        @click="showCourseToken = !showCourseToken"
                                        class="p-3 bg-white border-2 border-gray-300 text-gray-600 hover:text-purple-600 hover:border-purple-300 rounded-lg transition-colors">
                                    <svg x-show="!showCourseToken" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showCourseToken" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                                <button type="button"
                                        @click="navigator.clipboard.writeText(courseTokenValue)"
                                        class="p-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
                                        title="Copy token ke clipboard">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                    </svg>
                                </button>
                            </div>

                            @if($course->token_expires_at)
                                <p class="text-sm text-gray-600 mb-4">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Kadaluarsa: <strong>{{ $course->token_expires_at->format('d M Y H:i') }}</strong>
                                    @if($course->token_expires_at->isPast())
                                        <span class="text-red-600 font-semibold">(Sudah Kadaluarsa)</span>
                                    @endif
                                </p>
                            @endif

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button"
                                        @click="$dispatch('open-modal', 'regenerate-course-token')"
                                        class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Regenerate Token
                                </button>
                                <form action="{{ route('courses.token.toggle', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-3 {{ $course->token_enabled ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-colors shadow-sm">
                                        @if($course->token_enabled)
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                            Nonaktifkan
                                        @else
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Aktifkan
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Token Course</h4>
                            <p class="text-gray-500 mb-6">Generate token untuk self-enrollment course</p>
                            <button type="button"
                                    @click="$dispatch('open-modal', 'generate-course-token')"
                                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-md">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Generate Token Course
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Class Tokens -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <div class="bg-gradient-to-r from-indigo-500 to-blue-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Token Kelas
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">Manage token untuk setiap kelas dalam course ini</p>
                </div>

                <div class="p-6">
                    @if($course->classes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($course->classes as $class)
                                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-5 border-2 border-indigo-200"
                                     x-data="{
                                         showToken: false,
                                         tokenValue: '{{ $class->enrollment_token ?? '' }}'
                                     }">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $class->name }}</h4>
                                            @if($class->start_date && $class->end_date)
                                                <p class="text-xs text-gray-500">
                                                    {{ $class->start_date->format('d M Y') }} - {{ $class->end_date->format('d M Y') }}
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-500 italic">Tanggal belum ditentukan</p>
                                            @endif
                                            @if($class->max_participants)
                                                <p class="text-xs text-gray-500">
                                                    {{ $class->participants->count() }}/{{ $class->max_participants }} peserta
                                                </p>
                                            @endif
                                        </div>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($class->status === 'active') bg-green-100 text-green-800
                                            @elseif($class->status === 'upcoming') bg-blue-100 text-blue-800
                                            @elseif($class->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($class->status) }}
                                        </span>
                                    </div>

                                    @if($class->enrollment_token)
                                        <div class="bg-white rounded-lg p-3 mb-3 border border-indigo-200">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-medium text-gray-600">Token:</span>
                                                @if($class->token_enabled)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        ✓ Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                        ✗ Disabled
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <code class="flex-1 bg-gray-100 px-3 py-2 rounded font-mono text-sm text-indigo-700 font-bold"
                                                      x-text="showToken ? tokenValue : '••••••••'"></code>
                                                <button type="button"
                                                        @click="showToken = !showToken"
                                                        class="p-2 text-gray-500 hover:text-indigo-600 transition-colors">
                                                    <svg x-show="!showToken" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    <svg x-show="showToken" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                        @click="navigator.clipboard.writeText(tokenValue)"
                                                        class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded transition-colors"
                                                        title="Copy token ke clipboard">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button"
                                                    @click="$dispatch('open-modal', 'regenerate-class-token-{{ $class->id }}')"
                                                    class="w-full px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded transition-colors">
                                                🔄 Regenerate
                                            </button>
                                            <form action="{{ route('course-periods.token.toggle', [$course, $class]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full px-3 py-2 {{ $class->token_enabled ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-xs font-medium rounded transition-colors">
                                                    {{ $class->token_enabled ? '🚫 Disable' : '✓ Enable' }}
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <button type="button"
                                                @click="$dispatch('open-modal', 'generate-class-token-{{ $class->id }}')"
                                                class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Generate Token
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Kelas</h4>
                            <p class="text-gray-500 mb-6">Buat kelas terlebih dahulu untuk generate token per kelas</p>
                            <a href="{{ route('course-periods.create', $course) }}"
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Kelas Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Generate Course Token -->
    <x-modal name="generate-course-token" :show="false" maxWidth="2xl">
        <form action="{{ route('courses.token.generate', $course) }}" method="POST" class="p-6" x-data="{ tokenType: 'random' }">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4">Generate Token Course</h2>

            <div class="space-y-4">
                <!-- Token Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Token</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="tokenType === 'random' ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'">
                            <input type="radio" name="token_type" value="random" x-model="tokenType" class="sr-only">
                            <div class="flex-1">
                                <span class="block font-medium text-gray-900">🎲 Random</span>
                                <span class="block text-xs text-gray-500">Generate otomatis</span>
                            </div>
                        </label>
                        <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="tokenType === 'custom' ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'">
                            <input type="radio" name="token_type" value="custom" x-model="tokenType" class="sr-only">
                            <div class="flex-1">
                                <span class="block font-medium text-gray-900">✏️ Custom</span>
                                <span class="block text-xs text-gray-500">Buat sendiri</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Random Token Options -->
                <div x-show="tokenType === 'random'" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Panjang Token</label>
                        <select name="token_length" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="6">6 karakter</option>
                            <option value="8" selected>8 karakter</option>
                            <option value="10">10 karakter</option>
                            <option value="12">12 karakter</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Format Token</label>
                        <select name="token_format" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="alphanumeric">Huruf & Angka</option>
                            <option value="alpha">Huruf Saja</option>
                            <option value="numeric">Angka Saja</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Token Input -->
                <div x-show="tokenType === 'custom'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Token Custom</label>
                    <input type="text" name="custom_token"
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 uppercase"
                           placeholder="Contoh: COURSE2024"
                           maxlength="20"
                           x-bind:required="tokenType === 'custom'">
                    <p class="text-xs text-gray-500 mt-1">4-20 karakter, hanya huruf, angka, dan dash (-)</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Generate Token
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Regenerate Course Token -->
    <x-modal name="regenerate-course-token" :show="false" maxWidth="2xl">
        <form action="{{ route('courses.token.regenerate', $course) }}" method="POST" class="p-6" x-data="{ tokenType: 'random' }">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4">Regenerate Token Course</h2>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                <p class="text-sm text-yellow-700">⚠️ Token lama akan tidak valid setelah regenerate</p>
            </div>

            <div class="space-y-4">
                <!-- Token Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Token</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="tokenType === 'random' ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'">
                            <input type="radio" name="token_type" value="random" x-model="tokenType" class="sr-only">
                            <div class="flex-1">
                                <span class="block font-medium text-gray-900">🎲 Random</span>
                                <span class="block text-xs text-gray-500">Generate otomatis</span>
                            </div>
                        </label>
                        <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="tokenType === 'custom' ? 'border-purple-500 bg-purple-50' : 'border-gray-300 hover:border-gray-400'">
                            <input type="radio" name="token_type" value="custom" x-model="tokenType" class="sr-only">
                            <div class="flex-1">
                                <span class="block font-medium text-gray-900">✏️ Custom</span>
                                <span class="block text-xs text-gray-500">Buat sendiri</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Random Token Options -->
                <div x-show="tokenType === 'random'" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Panjang Token</label>
                        <select name="token_length" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="6">6 karakter</option>
                            <option value="8" selected>8 karakter</option>
                            <option value="10">10 karakter</option>
                            <option value="12">12 karakter</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Format Token</label>
                        <select name="token_format" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="alphanumeric">Huruf & Angka</option>
                            <option value="alpha">Huruf Saja</option>
                            <option value="numeric">Angka Saja</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Token Input -->
                <div x-show="tokenType === 'custom'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Token Custom</label>
                    <input type="text" name="custom_token"
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 uppercase"
                           placeholder="Contoh: COURSE2024"
                           maxlength="20"
                           x-bind:required="tokenType === 'custom'">
                    <p class="text-xs text-gray-500 mt-1">4-20 karakter, hanya huruf, angka, dan dash (-)</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Regenerate Token
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modals for Class Tokens -->
    @foreach($course->classes as $class)
        <!-- Modal Generate Class Token -->
        <x-modal name="generate-class-token-{{ $class->id }}" :show="false" maxWidth="2xl">
            <form action="{{ route('course-periods.token.generate', [$course, $class]) }}" method="POST" class="p-6" x-data="{ tokenType: 'random' }">
                @csrf
                <h2 class="text-lg font-bold text-gray-900 mb-2">Generate Token Kelas</h2>
                <p class="text-sm text-gray-600 mb-4">{{ $class->name }}</p>

                <div class="space-y-4">
                    <!-- Token Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Token</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="tokenType === 'random' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                                <input type="radio" name="token_type" value="random" x-model="tokenType" class="sr-only">
                                <div class="flex-1">
                                    <span class="block font-medium text-gray-900">🎲 Random</span>
                                    <span class="block text-xs text-gray-500">Generate otomatis</span>
                                </div>
                            </label>
                            <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="tokenType === 'custom' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                                <input type="radio" name="token_type" value="custom" x-model="tokenType" class="sr-only">
                                <div class="flex-1">
                                    <span class="block font-medium text-gray-900">✏️ Custom</span>
                                    <span class="block text-xs text-gray-500">Buat sendiri</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Random Token Options -->
                    <div x-show="tokenType === 'random'" class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Panjang Token</label>
                            <select name="token_length" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="6">6 karakter</option>
                                <option value="8" selected>8 karakter</option>
                                <option value="10">10 karakter</option>
                                <option value="12">12 karakter</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Token</label>
                            <select name="token_format" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="alphanumeric">Huruf & Angka</option>
                                <option value="alpha">Huruf Saja</option>
                                <option value="numeric">Angka Saja</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom Token Input -->
                    <div x-show="tokenType === 'custom'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Token Custom</label>
                        <input type="text" name="custom_token"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                               placeholder="Contoh: CLASS-A-2024"
                               maxlength="20"
                               x-bind:required="tokenType === 'custom'">
                        <p class="text-xs text-gray-500 mt-1">4-20 karakter, hanya huruf, angka, dan dash (-)</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Generate Token
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Regenerate Class Token -->
        <x-modal name="regenerate-class-token-{{ $class->id }}" :show="false" maxWidth="2xl">
            <form action="{{ route('course-periods.token.regenerate', [$course, $class]) }}" method="POST" class="p-6" x-data="{ tokenType: 'random' }">
                @csrf
                <h2 class="text-lg font-bold text-gray-900 mb-2">Regenerate Token Kelas</h2>
                <p class="text-sm text-gray-600 mb-2">{{ $class->name }}</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-700">⚠️ Token lama akan tidak valid setelah regenerate</p>
                </div>

                <div class="space-y-4">
                    <!-- Token Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Token</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="tokenType === 'random' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                                <input type="radio" name="token_type" value="random" x-model="tokenType" class="sr-only">
                                <div class="flex-1">
                                    <span class="block font-medium text-gray-900">🎲 Random</span>
                                    <span class="block text-xs text-gray-500">Generate otomatis</span>
                                </div>
                            </label>
                            <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="tokenType === 'custom' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                                <input type="radio" name="token_type" value="custom" x-model="tokenType" class="sr-only">
                                <div class="flex-1">
                                    <span class="block font-medium text-gray-900">✏️ Custom</span>
                                    <span class="block text-xs text-gray-500">Buat sendiri</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Random Token Options -->
                    <div x-show="tokenType === 'random'" class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Panjang Token</label>
                            <select name="token_length" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="6">6 karakter</option>
                                <option value="8" selected>8 karakter</option>
                                <option value="10">10 karakter</option>
                                <option value="12">12 karakter</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Token</label>
                            <select name="token_format" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="alphanumeric">Huruf & Angka</option>
                                <option value="alpha">Huruf Saja</option>
                                <option value="numeric">Angka Saja</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom Token Input -->
                    <div x-show="tokenType === 'custom'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Token Custom</label>
                        <input type="text" name="custom_token"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                               placeholder="Contoh: CLASS-A-2024"
                               maxlength="20"
                               x-bind:required="tokenType === 'custom'">
                        <p class="text-xs text-gray-500 mt-1">4-20 karakter, hanya huruf, angka, dan dash (-)</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Regenerate Token
                    </button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>
