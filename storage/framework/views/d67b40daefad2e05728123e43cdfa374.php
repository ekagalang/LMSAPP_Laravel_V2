<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Dashboard Instruktur')); ?>

            </h2>
            <div class="flex items-center space-x-4">
                <!-- ✅ PERBAIKAN: Komponen Notifikasi Fungsional -->
                <a href="<?php echo e(route('announcements.index')); ?>" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.5-1.5A2 2 0 0118 14v-3a6 6 0 10-12 0v3a2 2 0 01-.5 1.5L4 17h5m6 0v1a3 3 0 11-6 0v-1" />
                    </svg>
                    
                    <?php if(Auth::user()->unread_announcements_count > 0): ?>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                            <?php echo e(Auth::user()->unread_announcements_count); ?>

                        </span>
                    <?php endif; ?>
                </a>
                <div class="text-sm text-gray-500">
                    <?php echo e(now()->format('l, d F Y')); ?>

                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Selamat datang, <?php echo e(auth()->user()->name); ?>! 👨‍🏫</h3>
                            <p class="text-indigo-100">Kelola pembelajaran Anda dan pantau perkembangan peserta dengan mudah.</p>
                        </div>
                        <?php if($stats['essays']['pending'] > 0): ?>
                        <div class="hidden md:block">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2">
                                    <span class="text-lg font-bold"><?php echo e($stats['essays']['pending']); ?></span>
                                </div>
                                <p class="text-xs text-indigo-100">Esai Pending</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Announcement Section -->
            <?php if($announcements && $announcements->count() > 0): ?>
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">Pengumuman Terbaru</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php $__currentLoopData = $announcements->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-4 rounded-lg border border-<?php echo e($announcement->level_color); ?>-200 bg-<?php echo e($announcement->level_color); ?>-50">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-<?php echo e($announcement->level_color); ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?php if($announcement->level === 'info'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            <?php elseif($announcement->level === 'success'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            <?php elseif($announcement->level === 'warning'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            <?php else: ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            <?php endif; ?>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-<?php echo e($announcement->level_color); ?>-800"><?php echo e($announcement->title); ?></h4>
                                        <p class="text-sm text-<?php echo e($announcement->level_color); ?>-700 mt-1"><?php echo e(Str::limit($announcement->content, 120)); ?></p>
                                        <p class="text-xs text-<?php echo e($announcement->level_color); ?>-600 mt-2"><?php echo e($announcement->created_at->diffForHumans()); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- My Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kelas Saya</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e(number_format($stats['courses']['total'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex text-xs text-gray-600">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    <?php echo e($stats['courses']['published']); ?> Published
                                </span>
                                <span class="flex items-center ml-3">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                    <?php echo e($stats['courses']['draft']); ?> Draft
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Peserta</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e(number_format($stats['students']['total'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                <span class="text-green-600 font-medium">+<?php echo e($stats['students']['recent_enrollments']); ?></span>
                                pendaftaran baru (30 hari)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Diskusi</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e(number_format($stats['discussions']['total'] ?? 0)); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                di semua kursus Anda
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Essays -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center relative">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <?php if($stats['essays']['pending'] > 0): ?>
                                    <span class="absolute -top-1 -right-1 block w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Penilaian Esai</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e(number_format($stats['essays']['pending'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                dari <?php echo e($stats['essays']['total']); ?> total submisi
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rest of the dashboard content remains the same -->
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Course Performance & Recent Activities -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Kelas</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php $__empty_1 = true; $__currentLoopData = $stats['courses']['performance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover-lift">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-medium text-gray-900"><?php echo e($course['title']); ?></h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($course['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                                <?php echo e(ucfirst($course['status'])); ?>

                                            </span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 mb-3">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <?php echo e($course['students']); ?> peserta
                                        </div>
                                        
                                        <div class="flex items-center text-xs text-gray-500 mt-2">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Lihat progress detail di halaman kursus</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="<?php echo e(route('courses.show', $course['id'])); ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                            Kelola
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <p class="mb-4">Belum ada kursus yang dibuat</p>
                                    <a href="<?php echo e(route('courses.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                        Buat Kursus Pertama
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Zoom Sessions -->
                    <?php if(isset($stats['upcoming_zoom_sessions']) && $stats['upcoming_zoom_sessions']->count() > 0): ?>
                        <?php if (isset($component)) { $__componentOriginal8e3dfed1a8b541ae9dc531177d832f04 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e3dfed1a8b541ae9dc531177d832f04 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.upcoming-zoom-sessions','data' => ['zoomSessions' => $stats['upcoming_zoom_sessions']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('upcoming-zoom-sessions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['zoomSessions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['upcoming_zoom_sessions'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8e3dfed1a8b541ae9dc531177d832f04)): ?>
<?php $attributes = $__attributesOriginal8e3dfed1a8b541ae9dc531177d832f04; ?>
<?php unset($__attributesOriginal8e3dfed1a8b541ae9dc531177d832f04); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e3dfed1a8b541ae9dc531177d832f04)): ?>
<?php $component = $__componentOriginal8e3dfed1a8b541ae9dc531177d832f04; ?>
<?php unset($__componentOriginal8e3dfed1a8b541ae9dc531177d832f04); ?>
<?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Sidebar dengan notifikasi dan aksi cepat -->
                <div class="space-y-6">
                <!-- Quick Actions dengan notifikasi -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Tombol Aksi Cepat</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <!-- Kelola Diskusi -->
                            <?php if($stats['courses']['total'] > 0): ?>
                            <a href="<?php echo e(route('courses.discussions.index', $stats['courses']['performance'][0]['id'] ?? 1)); ?>" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="text-blue-800">Kelola Diskusi Materi</span>
                            </a>

                            <a href="<?php echo e(route('chat.index')); ?>" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="text-gray-600">Chatting Private</span>
                            </a>

                            <!-- Kelola Penilaian -->
                            <a href="<?php echo e(route('courses.gradebook', $stats['courses']['performance'][0]['id'] ?? 1)); ?>" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <div class="flex-1">
                                    <span class="text-green-800">Kelola Penilaian</span>
                                    <?php if($stats['essays']['pending'] > 0): ?>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <?php echo e($stats['essays']['pending']); ?> pending
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </a>

                            <!-- Lihat Progres -->
                            <a href="<?php echo e(route('eo.courses.index')); ?>" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="text-purple-800">Lihat Progres Peserta</span>
                            </a>
                            <?php endif; ?>

                            <?php if($stats['essays']['pending'] > 0): ?>
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium bg-gradient-to-r from-orange-50 to-red-50 rounded-lg border border-orange-200">
                                <svg class="w-5 h-5 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-orange-800 font-medium"><?php echo e($stats['essays']['pending']); ?> Esai Perlu Dinilai</p>
                                    <p class="text-xs text-orange-600">Klik tombol menu penilaian di atas</p>
                                </div>
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Status Sistem</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">Instruktur Dashboard</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <div class="w-2 h-2 bg-green-600 rounded-full mr-1 animate-pulse"></div>
                                    Aktif
                                </span>
                            </div>
                            <div class="mt-4 text-xs text-gray-500">
                                <p>Terakhir diperbarui: <?php echo e(now()->format('H:i')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast Container -->
    <div id="notificationToasts" class="fixed top-4 right-4 z-50 space-y-2 w-full max-w-sm"></div>

    <?php $__env->startPush('styles'); ?>
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-variant-numeric: tabular-nums;
        }

        /* Notification Styles */
        .notification-bell:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(59, 130, 246, 0.1));
        }

        .notification-badge {
            animation: bounce 2s infinite;
        }

        .notification-dropdown {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
        }

        .notification-dropdown.show {
            display: block !important;
            opacity: 1;
            transform: scale(1);
        }

        .notification-item.unread {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(59, 130, 246, 0.05));
            border-left: 3px solid #6366f1;
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -15px, 0);
            }
            70% {
                transform: translate3d(0, -7px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Notification System JavaScript
        let notificationDropdownVisible = false;
        let unreadCount = <?php echo e($stats['essays']['pending'] + ($stats['discussions']['recent'] > 0 ? 1 : 0) + ($stats['students']['recent_enrollments'] > 0 ? 1 : 0)); ?>;

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');

            if (!notificationDropdownVisible) {
                dropdown.style.display = 'block';
                setTimeout(() => {
                    dropdown.classList.add('show');
                }, 10);
                notificationDropdownVisible = true;
            } else {
                dropdown.classList.remove('show');
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 200);
                notificationDropdownVisible = false;
            }
        }

        function markAllAsRead() {
            const notificationItems = document.querySelectorAll('.notification-item.unread');
            notificationItems.forEach(item => {
                item.classList.remove('unread');
            });

            unreadCount = 0;
            updateBadge();
        }

        function updateBadge() {
            const badge = document.getElementById('notificationBadge');
            const count = document.getElementById('notificationCount');
            const pulse = document.getElementById('notificationPulse');

            if (unreadCount > 0) {
                badge.style.display = 'inline-flex';
                pulse.style.display = 'block';
                count.textContent = unreadCount > 99 ? '99+' : unreadCount;
            } else {
                badge.style.display = 'none';
                pulse.style.display = 'none';
            }
        }

        function showToast(message, type = 'info', duration = 5000) {
            const container = document.getElementById('notificationToasts');
            const toast = document.createElement('div');

            const typeStyles = {
                info: 'bg-blue-600 text-white',
                success: 'bg-green-600 text-white',
                warning: 'bg-yellow-600 text-white',
                error: 'bg-red-600 text-white'
            };

            toast.className = `notification-toast max-w-sm w-full ${typeStyles[type]} shadow-lg rounded-lg pointer-events-auto overflow-hidden`;
            toast.innerHTML = `
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="inline-flex text-white focus:outline-none" onclick="this.closest('.notification-toast').remove()">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('removing');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, duration);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.querySelector('.notification-container');
            if (!container.contains(event.target) && notificationDropdownVisible) {
                toggleNotifications();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Welcome notification
            setTimeout(() => {
                <?php if($stats['essays']['pending'] > 0): ?>
                showToast('👨‍🏫 Anda memiliki <?php echo e($stats['essays']['pending']); ?> esai yang perlu dinilai!', 'warning');
                <?php else: ?>
                showToast('👨‍🏫 Dashboard instruktur berhasil dimuat!', 'success');
                <?php endif; ?>
            }, 1000);

            // Add click handlers to notification items
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach((item, index) => {
                item.addEventListener('click', function() {
                    if (this.classList.contains('unread')) {
                        this.classList.remove('unread');
                        unreadCount = Math.max(0, unreadCount - 1);
                        updateBadge();
                    }
                });
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/dashboard/instructor.blade.php ENDPATH**/ ?>