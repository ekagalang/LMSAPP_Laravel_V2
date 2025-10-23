<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="icon" href="<?php echo e(asset('images/favicon.ico')); ?>" type="image/x-icon">

    <title><?php echo e(config('app.name', 'LMS App')); ?> <?php if (! empty(trim($__env->yieldContent('title')))): ?> - <?php echo $__env->yieldContent('title'); ?> <?php endif; ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <!-- Custom Navbar Styles -->
    <style>
        .nav-link-custom {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            padding-top: 0.25rem;
            border-bottom: 2px solid transparent;
            font-size: 1.125rem !important;
            font-weight: 600 !important;
            line-height: 1.25;
            transition: all 0.15s ease-in-out;
            color: #000000 !important;
            text-decoration: none;
        }

        .nav-link-custom:hover {
            color: #7f1d1d !important;
            border-bottom-color: #fca5a5 !important;
        }

        .nav-link-custom.active {
            color: #7f1d1d !important;
            border-bottom-color: #7f1d1d !important;
        }

        .dropdown-trigger-custom {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border: 1px solid transparent;
            font-size: 1rem !important;
            font-weight: 600 !important;
            border-radius: 0.375rem;
            color: #000000 !important;
            background-color: #ffffff;
            transition: all 0.15s ease-in-out;
        }

        .dropdown-trigger-custom:hover {
            color: #7f1d1d !important;
        }

        .dropdown-item-custom {
            display: block;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: #374151 !important;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .dropdown-item-custom:hover {
            background-color: #fef2f2 !important;
            color: #7f1d1d !important;
        }

        .responsive-nav-link-custom {
            display: block;
            padding-left: 0.75rem;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border-left: 4px solid transparent;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #000000 !important;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .responsive-nav-link-custom:hover {
            color: #7f1d1d !important;
            background-color: #fef2f2 !important;
            border-left-color: #fca5a5 !important;
        }

        .responsive-nav-link-custom.active {
            color: #7f1d1d !important;
            background-color: #fef2f2 !important;
            border-left-color: #7f1d1d !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ open: false }" class="min-h-screen">
        
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex items-center">
                            <a href="<?php echo e(route('dashboard')); ?>">
                                <img src="<?php echo e(asset('images/logo.png')); ?>" 
                                    alt="LMS APP Logo" 
                                    class="h-14 w-auto">
                            </a>
                        </div>

                        
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

                            <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('dashboard')); ?>" 
                               class="nav-link-custom <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                                <?php echo e(__('Dashboard')); ?>

                            </a>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view courses')): ?>
                                <a href="<?php echo e(route('courses.index')); ?>" 
                                   class="nav-link-custom <?php echo e(request()->routeIs('courses.*') ? 'active' : ''); ?>">
                                    <?php echo e(__('Kelola Kursus')); ?>

                                </a>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view progress reports')): ?>
                                <a href="<?php echo e(route('eo.courses.index')); ?>" 
                                class="nav-link-custom <?php echo e(request()->routeIs('eo.*') ? 'active' : ''); ?>">
                                    <?php echo e(__('Pemantauan Kursus')); ?>

                                </a>
                                <a href="<?php echo e(route('instructor-analytics.index')); ?>" 
                                class="nav-link-custom <?php echo e(request()->routeIs('instructor-analytics.*') ? 'active' : ''); ?>">
                                    <?php echo e(__('Analytics Instruktur')); ?>

                                </a>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage users','manage roles','view certificate templates','view activity logs','view certificate analytics','view certificate management'])): ?>
                                <div class="hidden sm:flex sm:items-center" x-data="{ adminOpen: false }">
                                    <div class="relative">
                                        <button @click="adminOpen = ! adminOpen" 
                                                class="dropdown-trigger-custom">
                                            <span>Admin Menu</span>
                                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div x-show="adminOpen" 
                                             @click.away="adminOpen = false"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 z-50 mt-2 w-60 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                                             style="display: none;">
                                            <div class="py-1">
                                                <a href="<?php echo e(route('admin.users.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Manajemen Pengguna')); ?>

                                                </a>
                                                <a href="<?php echo e(route('admin.roles.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Manajemen Peran')); ?>

                                                </a>
                                                <a href="<?php echo e(route('admin.announcements.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Manajemen Pengumuman')); ?>

                                                </a>
                                                <a href="<?php echo e(route('admin.participants.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Analitik Peserta')); ?>

                                                </a>
                                                <div class="border-t my-1"></div>
                                                <a href="<?php echo e(route('admin.certificate-templates.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Certificate Template')); ?>

                                                </a>
                                                <a href="<?php echo e(route('certificate-management.index')); ?>" 
                                                    class="dropdown-item-custom">
                                                    <?php echo e(__('Manajemen Sertifikat')); ?>

                                                </a>
                                                <div class="border-t my-1"></div>
                                                <a href="<?php echo e(route('admin.auto-grade.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Penyelesaian Penilaian Otomatis')); ?>

                                                </a>
                                                <a href="<?php echo e(route('admin.force-complete.index')); ?>" 
                                                   class="dropdown-item-custom">
                                                    <?php echo e(__('Force Complete Konten')); ?>

                                                </a>
                                                <div class="border-t my-1"></div>
                                                <a href="<?php echo e(route('file-control.index')); ?>"
                                                    class="dropdown-item-custom">
                                                    <?php echo e(__('File Manager')); ?>

                                                </a>
                                                <a href="<?php echo e(route('activity-logs.index')); ?>"
                                                    class="dropdown-item-custom">
                                                    <?php echo e(__('Log')); ?>

                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="hidden sm:flex sm:items-center sm:ml-6">

                        <?php if(auth()->guard()->check()): ?>
                            
                            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                                 <?php $__env->slot('trigger', null, []); ?> 
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div><?php echo e(Auth::user()->name); ?></div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                 <?php $__env->endSlot(); ?>

                                 <?php $__env->slot('content', null, []); ?> 
                                    <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                                        <?php echo e(__('Profile')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault();
                                                            this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault();
                                                            this.closest(\'form\').submit();']); ?>
                                            <?php echo e(__('Log Out')); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                    </form>
                                 <?php $__env->endSlot(); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                        <?php else: ?>
                            
                            <div class="space-x-4">
                                <a href="<?php echo e(route('login')); ?>" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Log in</a>
                                <a href="<?php echo e(route('register')); ?>" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Register</a>
                            </div>
                        <?php endif; ?>

                    </div>

                    
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" 
                       class="responsive-nav-link-custom <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                        <?php echo e(__('Dashboard')); ?>

                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view courses')): ?>
                        <a href="<?php echo e(route('courses.index')); ?>" 
                           class="responsive-nav-link-custom <?php echo e(request()->routeIs('courses.*') ? 'active' : ''); ?>">
                            <?php echo e(__('Kelola Kursus')); ?>

                        </a>
                    <?php endif; ?>

                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view progress reports')): ?>
                        <a href="<?php echo e(route('eo.courses.index')); ?>" 
                           class="nav-link-custom <?php echo e(request()->routeIs('eo.*') ? 'active' : ''); ?>">
                            <?php echo e(__('Pemantauan Kursus')); ?>

                        </a>
                        <a href="<?php echo e(route('instructor-analytics.index')); ?>" 
                        class="nav-link-custom <?php echo e(request()->routeIs('instructor-analytics.*') ? 'active' : ''); ?>">
                            <?php echo e(__('Analytics Instruktur')); ?>

                        </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage users','manage roles','view certificate templates','view activity logs','view certificate analytics','view certificate management'])): ?>
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="px-4">
                                <div class="font-semibold text-base text-gray-800">Admin Menu</div>
                            </div>
                            <div class="mt-3 space-y-1">
                                <a href="<?php echo e(route('admin.users.index')); ?>" 
                                   class="responsive-nav-link-custom <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                                    <?php echo e(__('Manajemen Pengguna')); ?>

                                </a>
                                <a href="<?php echo e(route('admin.roles.index')); ?>" 
                                   class="responsive-nav-link-custom <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
                                    <?php echo e(__('Manajemen Peran')); ?>

                                </a>
                                <a href="<?php echo e(route('admin.announcements.index')); ?>" 
                                   class="responsive-nav-link-custom">
                                    <?php echo e(__('Manajemen Pengumuman')); ?>

                                </a>
                                <a href="<?php echo e(route('admin.certificate-templates.index')); ?>" 
                                   class="responsive-nav-link-custom">
                                    <?php echo e(__('Certificate Template')); ?>

                                </a>
                                <a href="<?php echo e(route('certificate-management.index')); ?>" 
                                    class="responsive-nav-link-custom">
                                    <?php echo e(__('Manajemen Sertifikat')); ?>

                                </a>
                                <a href="<?php echo e(route('admin.auto-grade.index')); ?>" 
                                   class="responsive-nav-link-custom">
                                    <?php echo e(__('Penyelesaian Penilaian Otomatis')); ?>

                                </a>
                                <a href="<?php echo e(route('admin.force-complete.index')); ?>" 
                                   class="responsive-nav-link-custom">
                                    <?php echo e(__('Force Complete Konten')); ?>

                                </a>
                                <a href="<?php echo e(route('file-control.index')); ?>"
                                    class="responsive-nav-link-custom">
                                    <?php echo e(__('File Manager')); ?>

                                </a>
                                <a href="<?php echo e(route('activity-logs.index')); ?>"
                                    class="responsive-nav-link-custom">
                                    <?php echo e(__('Log')); ?>

                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                
                <?php if(auth()->guard()->check()): ?>
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800"><?php echo e(Auth::user()->name); ?></div>
                        <div class="font-medium text-sm text-gray-500"><?php echo e(Auth::user()->email); ?></div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a href="<?php echo e(route('profile.edit')); ?>" 
                           class="responsive-nav-link-custom">
                            <?php echo e(__('Profile')); ?>

                        </a>

                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <a href="<?php echo e(route('logout')); ?>"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="responsive-nav-link-custom">
                                <?php echo e(__('Log Out')); ?>

                            </a>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <?php if(isset($header)): ?>
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo e($header); ?>

                </div>
            </header>
        <?php endif; ?>

        <main>
            <?php if (! empty(trim($__env->yieldContent('content')))): ?>
                <?php echo $__env->yieldContent('content'); ?>
            <?php else: ?>
                <?php echo e($slot); ?>

            <?php endif; ?>
        </main>
    </div>

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" xintegrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>

        <!-- Summernote JS -->
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/layouts/app.blade.php ENDPATH**/ ?>