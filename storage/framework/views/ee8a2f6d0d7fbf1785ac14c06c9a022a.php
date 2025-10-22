
<div class="mt-8 pt-8 border-t" x-data="{ newTopic: false, activeReply: null }">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-2xl font-bold text-gray-800">Forum Diskusi</h3>
        <button @click="newTopic = !newTopic" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Mulai Topik Baru</button>
    </div>

    <div x-show="newTopic" x-collapse class="mb-6">
        <form action="<?php echo e(route('discussions.store', $content)); ?>" method="POST" class="bg-gray-50 p-4 rounded-lg">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Topik</label>
                <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'title','name' => 'title','class' => 'w-full mt-1','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'title','name' => 'title','class' => 'w-full mt-1','required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="body" class="block text-sm font-medium text-gray-700">Isi Pertanyaan</label>
                <textarea name="body" rows="4" class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
            </div>
            <div class="text-right">
                <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['@click' => 'newTopic = false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'newTopic = false']); ?>Batal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['type' => 'submit','class' => 'ml-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'ml-2']); ?>Kirim <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        <?php $__empty_1 = true; $__currentLoopData = $content->discussions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discussion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center font-bold text-gray-600">
                            <?php echo e(strtoupper(substr($discussion->user->name, 0, 1))); ?>

                        </div>
                    </div>
                    <div class="flex-grow">
                        <p class="font-bold text-gray-900"><?php echo e($discussion->title); ?></p>
                        <p class="text-sm text-gray-600">
                            Oleh <span class="font-semibold"><?php echo e($discussion->user->name); ?></span> - <span class="text-gray-400"><?php echo e($discussion->created_at->diffForHumans()); ?></span>
                        </p>
                    </div>
                    <button 
                        @click="activeReply === <?php echo e($discussion->id); ?> ? activeReply = null : activeReply = <?php echo e($discussion->id); ?>" 
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow hover:bg-indigo-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        Balas (<?php echo e($discussion->replies->count()); ?>)
                    </button>
                </div>

                <div class="mt-4 pl-14 prose prose-sm max-w-none text-gray-700">
                    <p><?php echo e($discussion->body); ?></p>
                </div>

                <div x-show="activeReply === <?php echo e($discussion->id); ?>" x-collapse class="mt-4 pl-14">
                    <div class="space-y-4">
                        <?php $__currentLoopData = $discussion->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500 text-sm">
                                         <?php echo e(strtoupper(substr($reply->user->name, 0, 1))); ?>

                                    </div>
                                </div>
                                <div class="flex-grow bg-gray-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold text-gray-800"><?php echo e($reply->user->name); ?></span> - <span class="text-gray-400"><?php echo e($reply->created_at->diffForHumans()); ?></span>
                                    </p>
                                    <p class="mt-1 text-sm text-gray-700"><?php echo e($reply->body); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <form action="<?php echo e(route('discussions.replies.store', $discussion)); ?>" method="POST" class="mt-4">
                        <?php echo csrf_field(); ?>
                        <textarea name="body" rows="2" class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis balasan..."></textarea>
                        <div class="text-right mt-2">
                            <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['type' => 'submit','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'text-xs']); ?>Kirim Balasan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-6">
                <p class="text-gray-500">Belum ada diskusi untuk materi ini. Jadilah yang pertama!</p>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/contents/partials/discussion-section.blade.php ENDPATH**/ ?>