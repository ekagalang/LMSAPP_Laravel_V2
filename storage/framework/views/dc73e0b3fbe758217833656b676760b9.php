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
                <?php echo e(__('Dashboard Peserta')); ?>

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
            <div class="bg-gradient-to-r from-green-600 to-teal-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Selamat datang, <?php echo e(auth()->user()->name); ?>! 🎓</h3>
                            <p class="text-green-100">Lanjutkan perjalanan pembelajaran Anda dan raih tujuan yang telah ditetapkan.</p>
                        </div>
                        <?php if($stats['courses']['overall_progress'] > 0): ?>
                        <div class="hidden md:block">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2">
                                    <span class="text-2xl font-bold"><?php echo e($stats['courses']['overall_progress']); ?>%</span>
                                </div>
                                <p class="text-xs text-green-100">Progress Total</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if(session('certificate_created')): ?>
        <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl shadow-lg overflow-hidden" 
            x-data="{ show: true }" 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95">
            
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-green-900">
                                    🎉 Sertifikat Berhasil Dibuat!
                                </h3>
                                <p class="text-green-800 mt-1">
                                    <?php echo e(session('success')); ?>

                                </p>
                                <p class="text-sm text-green-600 mt-2">
                                    Kursus: <span class="font-semibold"><?php echo e(session('course_title')); ?></span>
                                </p>
                            </div>
                            
                            <button @click="show = false" class="flex-shrink-0 ml-4 text-green-500 hover:text-green-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="<?php echo e(route('certificates.index')); ?>" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                Lihat Semua Sertifikat
                            </a>
                            
                            <?php if(session('certificate_id')): ?>
                                <a href="<?php echo e(route('certificates.download', session('certificate_id'))); ?>" 
                                class="inline-flex items-center px-4 py-2 bg-white border border-green-300 hover:bg-green-50 text-green-700 text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-8 5h16l-5-6H9l-5 6z"></path>
                                    </svg>
                                    Download Sertifikat
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="h-2 bg-gradient-to-r from-green-400 to-emerald-500"></div>
        </div>
    <?php endif; ?>

    
    <?php if(session('success') && !session('certificate_created')): ?>
        <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm" role="alert">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl shadow-sm" role="alert">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('info')): ?>
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl shadow-sm" role="alert">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800"><?php echo e(session('info')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="mb-6 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl shadow-sm" role="alert">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800"><?php echo e(session('warning')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
            <!-- Announcement Section -->
            <?php if($announcements && $announcements->count() > 0): ?>
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <!-- Enrolled Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kursus Diikuti</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e(number_format($stats['courses']['total'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex text-xs text-gray-600">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    <?php echo e($stats['courses']['completed']); ?> Selesai
                                </span>
                                <span class="flex items-center ml-3">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                                    <?php echo e($stats['courses']['in_progress']); ?> Berlangsung
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overall Progress -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Progress Keseluruhan</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e($stats['courses']['overall_progress']); ?>%</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: <?php echo e($stats['courses']['overall_progress']); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Completed -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Konten Selesai</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number"><?php echo e(number_format($stats['content']['completed_contents'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                dari <?php echo e($stats['content']['total_contents']); ?> total konten
                            </div>
                        </div>
                    </div>
                </div>

                <a href="https://www.canva.com/design/DAG5HIUQIaU/VAbTGdpZDQvnnIVmqBGgjA/edit?utm_content=DAG5HIUQIaU&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton" target="_blank" class="block">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 dashboard-card hover-lift cursor-pointer">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <!-- ICON USER GUIDE (Book) -->
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-lg font-semibold text-gray-900">User Guide</p>
                                        <p class="text-sm text-gray-600">Panduan penggunaan aplikasi</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <!-- ICON ARROW -->
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Course Progress & Next to Study -->
                <div class="lg:col-span-2">
                    <!-- My Courses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Kursus Saya</h3>
                        </div>
                        <div class="p-6">
                            <?php $__empty_1 = true; $__currentLoopData = $stats['courses']['progress']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="mb-6 last:mb-0 p-4 bg-gray-50 rounded-lg hover-lift">
                                <div class="flex items-start space-x-4">
                                    <?php if($course['thumbnail']): ?>
                                    <img src="<?php echo e(asset('storage/' . $course['thumbnail'])); ?>" alt="<?php echo e($course['title']); ?>" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                    <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="text-lg font-medium text-gray-900 truncate"><?php echo e($course['title']); ?></h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 flex-shrink-0
                                                <?php echo e($course['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($course['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                                                <?php echo e($course['status'] === 'completed' ? 'Selesai' :
                                                   ($course['status'] === 'in_progress' ? 'Berlangsung' : 'Belum Dimulai')); ?>

                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo e(Str::limit($course['description'], 120)); ?></p>
                                        <div class="flex items-center text-xs text-gray-500 mb-3">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Instruktur: <?php echo e($course['instructors']->pluck('name')->join(', ')); ?>

                                        </div>
                                        <div class="mb-3">
                                            <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                                <span>Progress: <?php echo e($course['progress']); ?>%</span>
                                                <span><?php echo e($course['completed_lessons']); ?>/<?php echo e($course['total_lessons']); ?> pelajaran • <?php echo e($course['completed_contents']); ?>/<?php echo e($course['total_contents']); ?> konten</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-green-500 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: <?php echo e($course['progress']); ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center mt-4">
                                            <div class="flex items-center gap-4">
                                                <a href="<?php echo e(route('courses.show', $course['id'])); ?>" 
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-lg transition-colors duration-200">
                                                    <?php echo e($course['status'] === 'not_started' ? 'Mulai Belajar' : 'Lanjutkan Belajar'); ?>

                                                </a>
                                                <a href="<?php echo e(route('courses.my-scores', $course['id'])); ?>" 
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition-colors duration-200">
                                                    Nilai & Hasil
                                                </a>
                                            </div>
                                            
                                            <?php
                                                // Improved logic dengan error handling
                                                $courseModel = \App\Models\Course::find($course['id']);
                                                $showCertificateButton = false;
                                                $certificateButtonType = null;
                                                $certificate = null;
                                                
                                                if ($courseModel && $courseModel->certificate_template_id) {
                                                    try {
                                                        $isEligible = Auth::user()->isEligibleForCertificate($courseModel);
                                                        $hasCertificate = Auth::user()->hasCertificateForCourse($courseModel);
                                                        
                                                        if ($isEligible) {
                                                            $showCertificateButton = true;
                                                            if ($hasCertificate) {
                                                                $certificateButtonType = 'download';
                                                                $certificate = Auth::user()->getCertificateForCourse($courseModel);
                                                            } else {
                                                                $certificateButtonType = 'generate';
                                                            }
                                                        } elseif ($course['progress'] >= 100) {
                                                            $showCertificateButton = true;
                                                            $certificateButtonType = 'waiting';
                                                        }
                                                    } catch (\Exception $e) {
                                                        // Fallback jika ada error
                                                        $showCertificateButton = false;
                                                    }
                                                }
                                            ?>

                                            
                                            <?php if($showCertificateButton): ?>
                                                <?php if($certificateButtonType === 'download'): ?>
                                                    
                                                    <div class="flex gap-1">
                                                        <a href="<?php echo e(route('certificates.download', $certificate)); ?>" 
                                                        class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-xs font-semibold transition-colors duration-200"
                                                        title="Download certificate">
                                                            📥 Unduh
                                                        </a>
                                                        <a href="<?php echo e(route('certificates.verify', $certificate->certificate_code)); ?>" target="_blank"
                                                        class="px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs font-semibold transition-colors duration-200"
                                                        title="View certificate">
                                                            👁️ Lihat
                                                        </a>
                                                    </div>
                                                <?php elseif($certificateButtonType === 'generate'): ?>
                                                    
                                                    <a href="<?php echo e(route('my-certificates.generate', $courseModel)); ?>"
                                                    class="px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-xs font-semibold transition-colors duration-200"
                                                    title="Generate your certificate">
                                                        🎓 Cetak Sertifikat
                                                    </a>
                                                <?php elseif($certificateButtonType === 'waiting'): ?>
                                                    
                                                    <span class="px-3 py-1.5 bg-gray-400 text-white rounded-md text-xs font-semibold cursor-not-allowed" 
                                                        title="Menunggu penilaian dari instruktur">
                                                        ⏳ Menunggu Penilaian
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Kursus</h3>
                                <p class="text-gray-600 mb-4">Anda belum terdaftar di kursus manapun.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php $__empty_1 = true; $__currentLoopData = $stats['recent_activities']['completions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $completion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($completion->content_title); ?></p>
                                        <p class="text-xs text-gray-600"><?php echo e($completion->lesson_title); ?> • <?php echo e($completion->course_title); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($completion->created_at)->diffForHumans()); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Belum ada aktivitas minggu ini</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <?php echo $__env->make('dashboard.partials.my-certificates', ['completedCertificates' => $completedCertificates], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Learning Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistik Belajar</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pelajaran Selesai</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($stats['content']['completed_lessons']); ?>/<?php echo e($stats['content']['total_lessons']); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Konten Selesai</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($stats['content']['completed_contents']); ?>/<?php echo e($stats['content']['total_contents']); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Kuis Selesai</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($stats['quizzes']['completed']); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Esai Terkirim</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($stats['essays']['submissions']); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diskusi Dimulai</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($stats['discussions']['started']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Chat Room</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <a href="<?php echo e(route('chat.index')); ?>" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Chat Private
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Token Enrollment Section -->
                    <div class="mb-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-5 py-3">
                                <div class="flex items-center">
                                    <h3 class="text-lg font-medium text-gray-900">Gabung Kelas</h3>
                                </div>
                            </div>

                            <div class="px-4 pb-3">
                                <!-- Unified Token Form -->
                                <form action="<?php echo e(route('enroll')); ?>" method="POST" class="w-full">
                                    <?php echo csrf_field(); ?>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        <svg class="w-4 h-4 inline mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                            </path>
                                        </svg>
                                        Token Pendaftaran
                                    </label>

                                    <div class="flex w-full overflow-hidden rounded-xl border border-gray-300 focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500">
                                        <input
                                            type="text"
                                            name="token"
                                            placeholder="Masukkan token kelas"
                                            maxlength="20"
                                            required
                                            class="flex-1 h-11 px-3 placeholder-gray-400 focus:outline-none" />
                                        <button type="submit"
                                            class="h-11 px-4 bg-green-600 hover:bg-green-700 text-white font-medium transition-colors duration-200 flex items-center justify-center">
                                            <span class="sr-only">Gabung</span>
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M13.172 12l-4.95-4.95 1.414-1.414L16 12l-6.364 6.364-1.414-1.414z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <?php if($errors->has('token')): ?>
                                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                            <p class="text-sm text-red-600"><?php echo e($errors->first('token')); ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <p class="mt-2 text-xs text-gray-500">
                                        Masukkan token yang diberikan admin untuk memasuki kelas
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Next to Study -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Selanjutnya Dipelajari</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <?php $__empty_1 = true; $__currentLoopData = $stats['recent_activities']['next_contents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="p-3 bg-blue-50 rounded-lg hover-lift">
                                    <h4 class="text-sm font-medium text-blue-900"><?php echo e($content->title); ?></h4>
                                    <p class="text-xs text-blue-600"><?php echo e($content->lesson->title); ?> • <?php echo e($content->lesson->course->title); ?></p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo e(ucfirst($content->type)); ?>

                                        </span>
                                        <a href="<?php echo e(route('courses.show', $content->lesson->course->id)); ?>" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Mulai →
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm">Semua konten telah selesai!</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <?php if($stats['courses']['total'] > 0): ?>
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium bg-gradient-to-r from-green-50 to-teal-50 rounded-lg border border-green-200">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-green-800 font-medium">Progress Keseluruhan</p>
                                    <p class="text-xs text-green-600"><?php echo e($stats['courses']['overall_progress']); ?>% dari semua kursus</p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($stats['essays']['submissions'] > $stats['essays']['graded']): ?>
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                                <svg class="w-5 h-5 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-orange-800 font-medium">Esai Menunggu Penilaian</p>
                                    <p class="text-xs text-orange-600"><?php echo e($stats['essays']['submissions'] - $stats['essays']['graded']); ?> esai belum dinilai</p>
                                </div>
                            </div>
                            <?php endif; ?>
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

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Notification Styles */
        .notification-bell {
            position: relative;
            overflow: visible;
        }

        .notification-bell:hover {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(20, 184, 166, 0.1));
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

        .notification-item {
            transition: all 0.2s ease-in-out;
        }

        .notification-item:hover {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.05), rgba(20, 184, 166, 0.05));
            transform: translateX(2px);
        }

        .notification-item.unread {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.05), rgba(20, 184, 166, 0.05));
            border-left: 3px solid #22c55e;
        }

        .notification-toast {
            animation: slideInRight 0.3s ease-out;
        }

        .notification-toast.removing {
            animation: slideOutRight 0.3s ease-in;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
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

        /* Custom scrollbar for notification list */
        .notification-list::-webkit-scrollbar {
            width: 4px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .notification-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Smooth animations for progress bars */
        .transition-all {
            transition: all 0.5s ease-in-out;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Notification System JavaScript
        let notificationDropdownVisible = false;
        let notifications = [];
        let unreadCount = 2; // Initialize with current count

        // Toggle notification dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const button = document.getElementById('notificationButton');

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

        // Mark notification as read
        function markAsRead(notificationId) {
            // Find and update notification items
            const notificationItems = document.querySelectorAll('.notification-item.unread');
            notificationItems.forEach(item => {
                item.classList.remove('unread');
            });

            unreadCount = Math.max(0, unreadCount - 1);
            updateBadge();
        }

        // Mark all notifications as read
        function markAllAsRead() {
            const notificationItems = document.querySelectorAll('.notification-item.unread');
            notificationItems.forEach(item => {
                item.classList.remove('unread');
            });

            unreadCount = 0;
            updateBadge();

            // Send to server
            // fetch('/notifications/mark-all-read', { method: 'POST' });
        }

        // Update notification badge
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

        // Show toast notification
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

            // Auto remove after duration
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
                showToast('🎓 Dashboard peserta berhasil dimuat! Progress terbaru telah disinkronkan.', 'success');
            }, 1000);

            // Animate progress bars on load
            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach((bar, index) => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 300 + (index * 100));
            });

            // Add smooth scroll to course cards
            const courseCards = document.querySelectorAll('.hover-lift');
            courseCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') return;
                    this.style.opacity = '0.7';
                    setTimeout(() => {
                        this.style.opacity = '1';
                    }, 200);
                });
            });

            // Add click handlers to notification items
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach((item, index) => {
                item.addEventListener('click', function() {
                    if (this.classList.contains('unread')) {
                        markAsRead(index);
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
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/dashboard/participant.blade.php ENDPATH**/ ?>