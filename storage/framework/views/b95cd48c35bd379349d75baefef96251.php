<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo e(config('app.name', 'LMS')); ?> - BASS Training</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap" rel="stylesheet" />
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="antialiased font-sans">
        <div class="bg-gray-50 text-black/50">
            <div class="relative min-h-screen flex flex-col items-center justify-center">

                <main class="w-full max-w-7xl mx-auto p-6 lg:p-8">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
                        
                        <div class="lg:w-1/2 text-center lg:text-left">
                            <div class="flex justify-center lg:justify-start">
                                <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['class' => 'h-20 w-auto text-bass-red']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-20 w-auto text-bass-red']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                            </div>
                            
                            <h1 class="mt-8 text-4xl md:text-5xl font-bold text-gray-800 leading-tight">
                                Raih Sertifikasi, <br class="hidden lg:block"/>
                                <span class="relative">
                                    <span class="relative z-10 text-bass-red">Tingkatkan</span>
                                    <span class="absolute bottom-1.5 left-0 w-full h-3 bg-bass-gold/70 -z-0"></span>
                                </span>
                                Kompetensi Anda.
                            </h1>
                            
                            <p class="mt-6 text-lg text-gray-600 max-w-xl mx-auto lg:mx-0">
                                Lembaga training dan sertifikasi resmi untuk mencetak tenaga kerja profesional dan berdaya saing tinggi sesuai kebutuhan industri.
                            </p>

                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                                <a href="<?php echo e(route('register')); ?>" class="w-full sm:w-auto rounded-lg px-8 py-3 text-lg font-semibold text-white bg-bass-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bass-red transition-transform hover:scale-105">
                                    Daftar Sekarang
                                </a>
                                <a href="<?php echo e(route('login')); ?>" class="w-full sm:w-auto rounded-lg px-8 py-3 text-lg font-semibold text-bass-red bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bass-red transition-transform hover:scale-105">
                                    Masuk
                                </a>
                            </div>
                        </div>

                        <div class="lg:w-1/2">
                            <svg class="w-full h-auto" viewBox="0 0 552 383" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="25" y="25" width="502" height="333" rx="20" fill="#FFFFFF" stroke="#E5E7EB" stroke-width="2"/>
                                <path d="M25 65H527" stroke="#E5E7EB" stroke-width="2"/>
                                <circle cx="45" cy="45" r="7" fill="#FEE2E2"/>
                                <circle cx="67" cy="45" r="7" fill="#FEF3C7"/>
                                <circle cx="89" cy="45" r="7" fill="#D1FAE5"/>
                                <rect x="65" y="110" width="220" height="25" rx="5" fill="#F3F4F6"/>
                                <rect x="65" y="155" width="150" height="12" rx="5" fill="#E5E7EB"/>
                                <rect x="65" y="180" width="180" height="12" rx="5" fill="#E5E7EB"/>
                                <rect x="65" y="220" width="90" height="30" rx="15" fill="#DA1E1E"/>
                                <path d="M333.689 313.111C333.689 313.111 405.952 291.603 421.284 225.833C436.616 160.063 381.189 101 381.189 101L405.353 91L491 216.5L405.353 323L333.689 313.111Z" fill="#FECACA"/>
                                <path d="M381.189 101C381.189 101 445.616 150.063 460.948 215.833C476.28 281.603 403.017 313.111 403.017 313.111" stroke="#B91C1C" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="10 10"/>
                                <path d="M405.353 323L491 216.5L405.353 91L381.189 101L333.689 313.111L405.353 323Z" stroke="#991B1B" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </main>

            </div>
        </div>
    </body>
</html><?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/welcome.blade.php ENDPATH**/ ?>