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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Activity Logs - System Activity History
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Header with Export -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Activity History</h3>
                            <p class="text-gray-600 text-sm mt-1">Track all system activities including file management, attendance, courses, and more</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="exportLogs()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export CSV
                            </button>
                            <button onclick="showClearModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Clear Old Logs
                            </button>
                        </div>
                    </div>

                    <!-- Category Pills -->
                    <div class="mb-4">
                        <div class="flex flex-wrap gap-2">
                            <?php $currentCat = request('category'); ?>
                            <a href="<?php echo e(route('activity-logs.index', array_merge(request()->except('page','category'), []))); ?>"
                               class="px-3 py-1 rounded-full text-sm <?php echo e(!$currentCat ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">All</a>
                            <?php $__currentLoopData = ($categories ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('activity-logs.index', array_merge(request()->except('page'), ['category' => $key]))); ?>"
                                   class="px-3 py-1 rounded-full text-sm <?php echo e($currentCat === $key ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>"><?php echo e($label); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="<?php echo e(route('activity-logs.index')); ?>" class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                    placeholder="Search description, file name, or participant..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <!-- Action Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                                <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">All Actions</option>
                                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($action); ?>" <?php echo e(request('action') == $action ? 'selected' : ''); ?>>
                                            <?php echo e(ucfirst($action)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- User Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">All Users</option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                            <?php echo e($user->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">All Status</option>
                                    <option value="success" <?php echo e(request('status') == 'success' ? 'selected' : ''); ?>>Success</option>
                                    <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex items-end gap-2 lg:col-span-2">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition flex-1">
                                    Apply Filters
                                </button>
                                <a href="<?php echo e(route('activity-logs.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition flex-1 text-center">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Total Logs</div>
                            <div class="text-3xl font-bold mt-1"><?php echo e($logs->total()); ?></div>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Success</div>
                            <div class="text-3xl font-bold mt-1"><?php echo e($logs->where('status', 'success')->count()); ?></div>
                        </div>
                        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Failed</div>
                            <div class="text-3xl font-bold mt-1"><?php echo e($logs->where('status', 'failed')->count()); ?></div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">This Page</div>
                            <div class="text-3xl font-bold mt-1"><?php echo e($logs->count()); ?></div>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <?php if($logs->count() > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div><?php echo e($log->created_at->format('M d, Y')); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo e($log->created_at->format('H:i:s')); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo e($log->user ? $log->user->name : 'System'); ?>

                                                </div>
                                                <?php if($log->user): ?>
                                                    <div class="text-xs text-gray-500"><?php echo e($log->user->email); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    <?php if($log->action == 'upload'): ?> bg-blue-100 text-blue-800
                                                    <?php elseif($log->action == 'delete'): ?> bg-red-100 text-red-800
                                                    <?php elseif($log->action == 'copy_link'): ?> bg-purple-100 text-purple-800
                                                    <?php elseif(str_contains($log->action, 'attendance')): ?> bg-green-100 text-green-800
                                                    <?php elseif(str_contains($log->action, 'course')): ?> bg-indigo-100 text-indigo-800
                                                    <?php elseif(str_contains($log->action, 'content')): ?> bg-yellow-100 text-yellow-800
                                                    <?php else: ?> bg-gray-100 text-gray-800
                                                    <?php endif; ?>">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $log->action))); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if($log->file_name): ?>
                                                    <div class="text-sm text-gray-900"><?php echo e($log->file_name); ?></div>
                                                    <?php if($log->file_size): ?>
                                                        <div class="text-xs text-gray-500"><?php echo e($log->formatted_file_size); ?></div>
                                                    <?php endif; ?>
                                                <?php elseif(isset($log->metadata['content_title'])): ?>
                                                    <div class="text-sm text-gray-900"><?php echo e($log->metadata['content_title']); ?></div>
                                                    <?php if(isset($log->metadata['participant_name'])): ?>
                                                        <div class="text-xs text-gray-500"><?php echo e($log->metadata['participant_name']); ?></div>
                                                    <?php endif; ?>
                                                <?php elseif(isset($log->metadata['course_title'])): ?>
                                                    <div class="text-sm text-gray-900"><?php echo e($log->metadata['course_title']); ?></div>
                                                <?php else: ?>
                                                    <div class="text-sm text-gray-500">-</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 max-w-xs truncate" title="<?php echo e($log->description); ?>">
                                                    <?php echo e($log->description ?? '-'); ?>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo e($log->ip_address ?? '-'); ?>

                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    <?php if($log->status == 'success'): ?> bg-green-100 text-green-800
                                                    <?php else: ?> bg-red-100 text-red-800
                                                    <?php endif; ?>">
                                                    <?php echo e(ucfirst($log->status)); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <button onclick="showLogDetails(<?php echo e($log->id); ?>)"
                                                    class="text-red-600 hover:text-red-900 font-medium">
                                                    Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <?php echo e($logs->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No activity logs</h3>
                            <p class="mt-1 text-sm text-gray-500">No logs found with the current filters.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Activity Log Details</h3>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="detailsContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Clear Logs Modal -->
    <div id="clearModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Clear Old Logs</h3>
                <button onclick="closeClearModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="clearForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delete logs older than:</label>
                    <select id="clearDays" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="7">7 days</option>
                        <option value="30">30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days</option>
                        <option value="180">180 days</option>
                        <option value="365">1 year</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="clearOldLogs()" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        Clear Logs
                    </button>
                    <button type="button" onclick="closeClearModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show log details
        function showLogDetails(id) {
            fetch(`/activity-logs/${id}`)
                .then(response => response.json())
                .then(data => {
                    const meta = data.metadata || {};
                    let content = `
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700">User</label>
                                <p class="mt-1 text-sm text-gray-900">${data.user ? data.user.name : 'System'}</p>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Action</label>
                                <p class="mt-1 text-sm text-gray-900">${data.action}</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900">${data.description || '-'}</p>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700">IP Address</label>
                                <p class="mt-1 text-sm text-gray-900">${data.ip_address || 'N/A'}</p>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1 text-sm text-gray-900">${data.status}</p>
                            </div>
                            ${data.error_message ? `
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Error Message</label>
                                    <p class="mt-1 text-sm text-red-600">${data.error_message}</p>
                                </div>
                            ` : ''}
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">User Agent</label>
                                <p class="mt-1 text-sm text-gray-900 break-all">${data.user_agent || 'N/A'}</p>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Created At</label>
                                <p class="mt-1 text-sm text-gray-900">${new Date(data.created_at).toLocaleString()}</p>
                            </div>
                        </div>
                    `;

                    // Render before/after diffs from Eloquent listeners
                    const before = meta.before || {};
                    const after = meta.after || {};
                    const changedFields = meta.changed_fields || [];
                    if (changedFields.length) {
                        content += `
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-red-50 p-3 rounded">
                                    <div class="font-semibold mb-2">Before</div>
                                    ${changedFields.map(k => `<div class=\"text-sm\"><span class=\"text-gray-500\">${k}:</span> ${escapeHtml(before[k])}</div>`).join('')}
                                </div>
                                <div class="bg-green-50 p-3 rounded">
                                    <div class="font-semibold mb-2">After</div>
                                    ${changedFields.map(k => `<div class=\"text-sm\"><span class=\"text-gray-500\">${k}:</span> ${escapeHtml(after[k])}</div>`).join('')}
                                </div>
                            </div>
                        `;
                    }

                    // Render model diffs from http_* logs
                    const models = Array.isArray(meta.models) ? meta.models : [];
                    if (models.length) {
                        content += `<div class=\"mt-6\"><div class=\"text-sm font-semibold mb-2\">Models</div>`;
                        models.forEach(m => {
                            const fields = Array.isArray(m.changed_fields) ? m.changed_fields : [];
                            content += `
                                <div class=\"mb-3 border rounded\">
                                    <div class=\"px-3 py-2 bg-gray-50 border-b text-sm\">
                                        <span class=\"font-semibold\">${m.model}</span> #${m.id} <span class=\"ml-2 text-xs px-2 py-0.5 rounded bg-gray-100\">${m.state}</span>
                                    </div>
                                    <div class=\"grid grid-cols-1 md:grid-cols-2 gap-3 p-3\">
                                        <div class=\"bg-red-50 p-2 rounded\"><div class=\"font-semibold mb-1\">Before</div>${fields.map(k => `<div class=\"text-xs\"><span class=\"text-gray-500\">${k}:</span> ${escapeHtml((m.before||{})[k])}</div>`).join('')}</div>
                                        <div class=\"bg-green-50 p-2 rounded\"><div class=\"font-semibold mb-1\">After</div>${fields.map(k => `<div class=\"text-xs\"><span class=\"text-gray-500\">${k}:</span> ${escapeHtml((m.after||{})[k])}</div>`).join('')}</div>
                                    </div>
                                </div>
                            `;
                        });
                        content += `</div>`;
                    }

                    // Raw metadata viewer
                    if (Object.keys(meta).length) {
                        content += `
                            <div class=\"mt-4\">
                                <div class=\"text-sm font-semibold mb-1\">Raw Metadata</div>
                                <pre class=\"mt-1 text-xs text-gray-900 bg-gray-50 p-2 rounded overflow-x-auto\">${escapeHtml(JSON.stringify(meta, null, 2))}</pre>
                            </div>
                        `;
                    }

                    document.getElementById('detailsContent').innerHTML = content;
                    document.getElementById('detailsModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load log details');
                });
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        // Clear old logs
        function showClearModal() {
            document.getElementById('clearModal').classList.remove('hidden');
        }

        function closeClearModal() {
            document.getElementById('clearModal').classList.add('hidden');
        }

        function clearOldLogs() {
            const days = document.getElementById('clearDays').value;

            if (!confirm(`Are you sure you want to delete all logs older than ${days} days? This action cannot be undone.`)) {
                return;
            }

            fetch('/activity-logs/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ days: days })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeClearModal();
                    location.reload();
                } else {
                    alert('Failed to clear logs');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to clear logs');
            });
        }

        // Export logs
        function exportLogs() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '/activity-logs/export?' + params.toString();
        }

        // Close modals on outside click
        window.onclick = function(event) {
            const detailsModal = document.getElementById('detailsModal');
            const clearModal = document.getElementById('clearModal');
            if (event.target == detailsModal) {
                closeDetailsModal();
            }
            if (event.target == clearModal) {
                closeClearModal();
            }
        }
    </script>
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
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/activity-logs/index.blade.php ENDPATH**/ ?>