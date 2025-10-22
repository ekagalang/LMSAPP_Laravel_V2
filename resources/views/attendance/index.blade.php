<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance - {{ $content->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Actions -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $content->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $course->title }}</p>
                        @if($content->min_attendance_minutes)
                            <span class="text-xs text-blue-600 font-medium">Min Duration: {{ $content->min_attendance_minutes }} min</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button onclick="showBulkMarkModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Bulk Mark
                        </button>
                        <a href="{{ route('attendance.export', $content->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Export CSV
                        </a>
                        <a href="{{ route('courses.show', $course->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                <div class="bg-blue-500 rounded-lg p-3 text-white">
                    <div class="text-xs opacity-90">Total</div>
                    <div class="text-2xl font-bold">{{ $participants->total() }}</div>
                </div>
                <div class="bg-green-500 rounded-lg p-3 text-white">
                    <div class="text-xs opacity-90">Present</div>
                    <div class="text-2xl font-bold">{{ $stats['present'] }}</div>
                </div>
                <div class="bg-red-500 rounded-lg p-3 text-white">
                    <div class="text-xs opacity-90">Absent</div>
                    <div class="text-2xl font-bold">{{ $stats['absent'] }}</div>
                </div>
                <div class="bg-yellow-500 rounded-lg p-3 text-white">
                    <div class="text-xs opacity-90">Late</div>
                    <div class="text-2xl font-bold">{{ $stats['late'] }}</div>
                </div>
                <div class="bg-purple-500 rounded-lg p-3 text-white">
                    <div class="text-xs opacity-90">Excused</div>
                    <div class="text-2xl font-bold">{{ $stats['excused'] }}</div>
                </div>
            </div>

            <!-- Search Form & Selection Info -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <form method="GET" action="{{ route('attendance.index', $content->id) }}" class="flex gap-2 mb-3">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search by name or email..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
                        Search
                    </button>
                    @if($search)
                        <a href="{{ route('attendance.index', $content->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                            Clear
                        </a>
                    @endif
                </form>

                <div class="flex justify-between items-center">
                    <div>
                        @if($search)
                            <p class="text-sm text-gray-600">
                                Showing results for: <strong>"{{ $search }}"</strong> ({{ $participants->total() }} found)
                            </p>
                        @endif
                    </div>
                    <div id="selectionInfo" class="hidden">
                        <span class="text-sm font-medium text-blue-600">
                            <span id="selectionCount">0</span> participants selected across all pages
                        </span>
                        <button onclick="clearSelection()" class="ml-2 text-xs text-red-600 hover:text-red-800 underline">
                            Clear Selection
                        </button>
                    </div>
                </div>
            </div>

            <!-- Participants Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded" onclick="toggleSelectAll(this)">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($participants as $participant)
                                @php
                                    $attendance = $participant->attendances->first();
                                    $statusColors = [
                                        'present' => 'bg-green-100 text-green-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'excused' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $status = $attendance ? $attendance->status : 'absent';
                                    $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" class="participant-checkbox rounded" value="{{ $participant->id }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $participant->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $participant->email }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $attendance ? $attendance->duration_minutes . ' min' : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button onclick="showMarkModal({{ $participant->id }}, '{{ $participant->name }}', {{ $attendance ? $attendance->id : 'null' }}, '{{ $status }}', {{ $attendance ? $attendance->duration_minutes : 0 }})"
                                                class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            {{ $attendance ? 'Edit' : 'Mark' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        @if($search)
                                            No participants found matching "{{ $search }}"
                                        @else
                                            No participants enrolled in this course.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($participants->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $participants->links() }}
                    </div>
                @endif
            </div>

            <!-- Showing X of Y -->
            <div class="mt-3 text-sm text-gray-600 text-center">
                Showing {{ $participants->firstItem() ?? 0 }} to {{ $participants->lastItem() ?? 0 }} of {{ $participants->total() }} participants
            </div>

        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div id="markModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Mark Attendance</h3>
                <button onclick="closeMarkModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="markForm">
                <input type="hidden" id="userId" name="user_id">
                <input type="hidden" id="attendanceId" name="attendance_id">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Participant</label>
                    <p id="participantName" class="text-sm text-gray-900 font-medium"></p>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration_minutes" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="0">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        Save
                    </button>
                    <button type="button" onclick="closeMarkModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Mark Modal -->
    <div id="bulkMarkModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Bulk Mark Attendance</h3>
                <button onclick="closeBulkMarkModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="bulkMarkForm">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selected Participants</label>
                    <p id="selectedCount" class="text-sm text-gray-900 font-medium">0 selected</p>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="bulkStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" id="bulkDuration" name="duration_minutes" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="0">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        Mark All
                    </button>
                    <button type="button" onclick="closeBulkMarkModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const contentId = {{ $content->id }};

        // Store selected participant IDs across pages
        let selectedParticipants = JSON.parse(localStorage.getItem('attendance_selected_' + contentId) || '[]');

        // Initialize checkboxes based on stored selection
        document.addEventListener('DOMContentLoaded', function() {
            updateCheckboxStates();
            updateSelectAllState();
            updateSelectionInfo();
        });

        // Update checkbox states from stored selection
        function updateCheckboxStates() {
            document.querySelectorAll('.participant-checkbox').forEach(cb => {
                if (selectedParticipants.includes(parseInt(cb.value))) {
                    cb.checked = true;
                }

                // Add event listener to each checkbox
                cb.addEventListener('change', function() {
                    const participantId = parseInt(this.value);
                    if (this.checked) {
                        // Add to selected
                        if (!selectedParticipants.includes(participantId)) {
                            selectedParticipants.push(participantId);
                        }
                    } else {
                        // Remove from selected
                        selectedParticipants = selectedParticipants.filter(id => id !== participantId);
                    }
                    saveSelection();
                    updateSelectAllState();
                });
            });
        }

        // Save selection to localStorage
        function saveSelection() {
            localStorage.setItem('attendance_selected_' + contentId, JSON.stringify(selectedParticipants));
            updateSelectionInfo();
        }

        // Update selection info display
        function updateSelectionInfo() {
            const selectionInfo = document.getElementById('selectionInfo');
            const selectionCount = document.getElementById('selectionCount');

            if (selectedParticipants.length > 0) {
                selectionInfo.classList.remove('hidden');
                selectionCount.textContent = selectedParticipants.length;
            } else {
                selectionInfo.classList.add('hidden');
            }
        }

        // Clear all selections
        function clearSelection() {
            if (!confirm('Clear all selected participants across all pages?')) {
                return;
            }
            selectedParticipants = [];
            localStorage.removeItem('attendance_selected_' + contentId);
            document.querySelectorAll('.participant-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            document.getElementById('selectAll').indeterminate = false;
            updateSelectionInfo();
        }

        // Update "select all" checkbox state
        function updateSelectAllState() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.participant-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

            if (checkboxes.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === checkboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Toggle select all (for current page)
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.participant-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
                const participantId = parseInt(cb.value);

                if (checkbox.checked) {
                    if (!selectedParticipants.includes(participantId)) {
                        selectedParticipants.push(participantId);
                    }
                } else {
                    selectedParticipants = selectedParticipants.filter(id => id !== participantId);
                }
            });
            saveSelection();
        }

        // Show mark modal
        function showMarkModal(userId, name, attendanceId, status, duration) {
            document.getElementById('userId').value = userId;
            document.getElementById('attendanceId').value = attendanceId || '';
            document.getElementById('participantName').textContent = name;
            document.getElementById('status').value = status;
            document.getElementById('duration').value = duration;
            document.getElementById('markModal').classList.remove('hidden');
        }

        function closeMarkModal() {
            document.getElementById('markModal').classList.add('hidden');
        }

        // Show bulk mark modal
        function showBulkMarkModal() {
            if (selectedParticipants.length === 0) {
                alert('Please select at least one participant');
                return;
            }
            document.getElementById('selectedCount').textContent = `${selectedParticipants.length} selected (across all pages)`;
            document.getElementById('bulkMarkModal').classList.remove('hidden');
        }

        function closeBulkMarkModal() {
            document.getElementById('bulkMarkModal').classList.add('hidden');
        }

        // Submit mark form
        document.getElementById('markForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const userId = formData.get('user_id');
            const attendanceId = formData.get('attendance_id');

            const data = {
                user_id: userId,
                status: formData.get('status'),
                duration_minutes: formData.get('duration_minutes') || 0,
                _token: '{{ csrf_token() }}'
            };

            try {
                let response;
                if (attendanceId) {
                    // Update existing
                    response = await fetch(`/attendance/record/${attendanceId}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                } else {
                    // Create new
                    response = await fetch(`/attendance/content/${contentId}/mark`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                }

                const result = await response.json();
                if (result.success) {
                    closeMarkModal();
                    location.reload();
                } else {
                    alert(result.message || 'Failed to mark attendance');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to mark attendance');
            }
        });

        // Submit bulk mark form
        document.getElementById('bulkMarkForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (selectedParticipants.length === 0) {
                alert('No participants selected');
                return;
            }

            const formData = new FormData(this);
            const data = {
                user_ids: selectedParticipants, // Use stored selected IDs
                status: formData.get('status'),
                duration_minutes: formData.get('duration_minutes') || 0,
                _token: '{{ csrf_token() }}'
            };

            try {
                const response = await fetch(`/attendance/content/${contentId}/bulk-mark`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    // Clear selection after successful bulk mark
                    selectedParticipants = [];
                    localStorage.removeItem('attendance_selected_' + contentId);
                    closeBulkMarkModal();
                    location.reload();
                } else {
                    alert(result.message || 'Failed to bulk mark attendance');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to bulk mark attendance');
            }
        });

        // Close modals on outside click
        window.onclick = function(event) {
            const markModal = document.getElementById('markModal');
            const bulkModal = document.getElementById('bulkMarkModal');
            if (event.target == markModal) closeMarkModal();
            if (event.target == bulkModal) closeBulkMarkModal();
        }
    </script>
</x-app-layout>
