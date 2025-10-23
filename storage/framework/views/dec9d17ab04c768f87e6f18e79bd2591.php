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
                <?php echo e(__('Dashboard')); ?>

            </h2>
            <div class="text-sm text-gray-500">
                <?php echo e(now()->format('l, d F Y')); ?>

            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <?php if(isset($announcements) && $announcements->isNotEmpty()): ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengumuman Terbaru</h3>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-4 rounded-lg border bg-gray-50">
                                    <h4 class="font-bold"><?php echo e($announcement->title); ?></h4>
                                    <p class="text-sm mt-1"><?php echo e($announcement->content); ?></p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Diposting oleh <?php echo e($announcement->user->name); ?> pada <?php echo e($announcement->created_at->format('d M Y')); ?>

                                    </p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Kursus Diikuti</p>
                    <p class="text-2xl font-bold"><?php echo e($stats['courses']['total'] ?? 0); ?></p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Progress Rata-rata</p>
                    <p class="text-2xl font-bold"><?php echo e($stats['courses']['overall_progress'] ?? 0); ?>%</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Diskusi</p>
                    <p class="text-2xl font-bold"><?php echo e($stats['discussions']['total'] ?? 0); ?></p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Kuis Selesai</p>
                    <p class="text-2xl font-bold"><?php echo e($stats['quizzes']['completed'] ?? 0); ?></p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Akses Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view courses')): ?>
                            <a href="<?php echo e(route('courses.index')); ?>" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Kursus</div>
                                <div class="text-sm text-gray-500">Lihat daftar kursus</div>
                            </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view instructor analytics')): ?>
                            <a href="<?php echo e(route('instructor-analytics.index')); ?>" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Analitik Instruktur</div>
                                <div class="text-sm text-gray-500">Lihat kinerja instruktur</div>
                            </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view certificate management')): ?>
                            <a href="<?php echo e(route('certificate-management.analytics')); ?>" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Sertifikat</div>
                                <div class="text-sm text-gray-500">Analitik & manajemen</div>
                            </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage users')): ?>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="block border rounded-lg p-4 hover:bg-gray-50">
                                <div class="font-semibold">Pengguna</div>
                                <div class="text-sm text-gray-500">Kelola pengguna & peran</div>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('announcements.index')); ?>" class="block border rounded-lg p-4 hover:bg-gray-50">
                            <div class="font-semibold">Pengumuman</div>
                            <div class="text-sm text-gray-500">Lihat semua pengumuman</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/dashboard/generic.blade.php ENDPATH**/ ?>