@php
    $unreadCount = $announcements->filter(function($announcement) {
        return !($announcement->is_read_by_user ?? true);
    })->count();
@endphp

<!-- Notification Component -->
<div class="relative notification-container">
    <!-- Notification Bell Icon -->
    <button
        type="button"
        class="notification-bell relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        onclick="toggleNotifications()"
        id="notificationButton"
        aria-label="Notifications"
        title="Klik untuk melihat pengumuman"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5V9a5 5 0 00-10 0v8l-5-5h5m0 0V9a5 5 0 0110 0v8.293l5 4.707"></path>
        </svg>

        <!-- Notification Badge -->
        @if($unreadCount > 0)
        <span
            class="notification-badge absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full animate-pulse"
            id="notificationBadge"
        >
            <span id="notificationCount">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        </span>

        <!-- Pulse Animation for New Notifications -->
        <span class="notification-pulse absolute top-0 right-0 block w-3 h-3 bg-red-400 rounded-full animate-ping" id="notificationPulse"></span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div
        class="notification-dropdown absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 ease-in-out"
        id="notificationDropdown"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5V9a5 5 0 00-10 0v8l-5-5h5m0 0V9a5 5 0 0110 0v8.293l5 4.707"></path>
                    </svg>
                    Pengumuman & Notifikasi
                </h3>
                @if($unreadCount > 0)
                <button
                    onclick="markAllAsRead()"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors focus:outline-none"
                >
                    Tandai Semua Dibaca
                </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="notification-list max-h-80 overflow-y-auto" id="notificationList">
            @forelse($announcements->take(5) as $announcement)
            <div class="notification-item p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors {{ !($announcement->is_read_by_user ?? true) ? 'unread' : '' }}"
                 onclick="markAsRead({{ $announcement->id }}, '{{ route('admin.announcements.show', $announcement) }}')"
                 data-announcement-id="{{ $announcement->id }}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-{{ $announcement->level_color }}-100 text-{{ $announcement->level_color }}-800">
                            @if($announcement->level === 'info')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @elseif($announcement->level === 'success')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @elseif($announcement->level === 'warning')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @endif
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $announcement->title }}</p>
                            @if(!($announcement->is_read_by_user ?? true))
                            <div class="w-2 h-2 bg-blue-600 rounded-full ml-2 flex-shrink-0"></div>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($announcement->content, 80) }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-gray-400">{{ $announcement->created_at->diffForHumans() }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $announcement->level_color }}-100 text-{{ $announcement->level_color }}-800">
                                {{ ucfirst($announcement->level) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5V9a5 5 0 00-10 0v8l-5-5h5m0 0V9a5 5 0 0110 0v8.293l5 4.707"></path>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada pengumuman</p>
                <p class="text-xs text-gray-500">Semua pengumuman akan muncul di sini</p>
            </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($announcements->count() > 0)
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors focus:outline-none">
                Lihat Semua Pengumuman →
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Toast Container for Notifications -->
<div id="notificationToasts" class="fixed top-4 right-4 z-50 space-y-2"></div>

@push('styles')
<style>
/* Notification Styles */
.notification-bell {
    position: relative;
    overflow: visible;
}

.notification-bell:hover {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(59, 130, 246, 0.1));
}

.notification-badge {
    animation: bounce 2s infinite;
    z-index: 10;
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
    position: relative;
}

.notification-item:hover {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(59, 130, 246, 0.05));
    transform: translateX(2px);
}

.notification-item.unread {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(59, 130, 246, 0.05));
    border-left: 3px solid #6366f1;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #6366f1, #3b82f6);
    animation: pulse 2s infinite;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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

/* Toast notifications */
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

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Notification System JavaScript
let notificationDropdownVisible = false;
let unreadCount = {{ $unreadCount }};

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
        closeNotifications();
    }
}

// Close notifications
function closeNotifications() {
    const dropdown = document.getElementById('notificationDropdown');

    dropdown.classList.remove('show');
    setTimeout(() => {
        dropdown.style.display = 'none';
    }, 200);
    notificationDropdownVisible = false;
}

// Mark notification as read and navigate
function markAsRead(announcementId, url) {
    const notificationItem = document.querySelector(`[data-announcement-id="${announcementId}"]`);

    if (notificationItem && notificationItem.classList.contains('unread')) {
        // Mark as read visually
        notificationItem.classList.remove('unread');

        // Update counter
        unreadCount = Math.max(0, unreadCount - 1);
        updateBadge();

        // Send AJAX request to mark as read
        fetch(`/notifications/mark-as-read/${announcementId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).catch(error => {
            console.log('Could not mark as read:', error);
        });
    }

    // Close dropdown and navigate
    closeNotifications();

    // Navigate to announcement
    if (url) {
        setTimeout(() => {
            window.location.href = url;
        }, 100);
    }
}

// Mark all notifications as read
function markAllAsRead() {
    const unreadItems = document.querySelectorAll('.notification-item.unread');

    unreadItems.forEach(item => {
        item.classList.remove('unread');
    });

    unreadCount = 0;
    updateBadge();

    // Send AJAX request
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Semua pengumuman telah ditandai sebagai dibaca', 'success');
        }
    }).catch(error => {
        console.log('Could not mark all as read:', error);
    });
}

// Update notification badge
function updateBadge() {
    const badge = document.getElementById('notificationBadge');
    const count = document.getElementById('notificationCount');
    const pulse = document.getElementById('notificationPulse');

    if (unreadCount > 0) {
        if (badge) {
            badge.style.display = 'inline-flex';
            count.textContent = unreadCount > 99 ? '99+' : unreadCount;
        }
        if (pulse) pulse.style.display = 'block';
    } else {
        if (badge) badge.style.display = 'none';
        if (pulse) pulse.style.display = 'none';
    }
}

// Show toast notification
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notificationToasts');
    if (!container) return;

    const toast = document.createElement('div');

    const typeStyles = {
        info: 'bg-blue-600 text-white',
        success: 'bg-green-600 text-white',
        warning: 'bg-yellow-600 text-white',
        error: 'bg-red-600 text-white'
    };

    const icons = {
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z',
        error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
    };

    toast.className = `notification-toast max-w-sm w-full ${typeStyles[type]} shadow-lg rounded-lg pointer-events-auto overflow-hidden transform transition-all duration-300`;
    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icons[type]}"></path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="inline-flex text-white focus:outline-none hover:opacity-75" onclick="this.closest('.notification-toast').remove()">
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
    if (!container?.contains(event.target) && notificationDropdownVisible) {
        closeNotifications();
    }
});

// Handle escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && notificationDropdownVisible) {
        closeNotifications();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBadge();
});

// Refresh notifications periodically (every 5 minutes)
setInterval(() => {
    if (!notificationDropdownVisible) {
        fetch('/notifications/api/for-user')
        .then(response => response.json())
        .then(data => {
            if (data.unread_count !== unreadCount) {
                const oldCount = unreadCount;
                unreadCount = data.unread_count;
                updateBadge();

                if (data.unread_count > oldCount) {
                    showToast('Anda memiliki pengumuman baru', 'info', 3000);
                }
            }
        })
        .catch(error => {
            console.log('Could not refresh notifications:', error);
        });
    }
}, 300000); // 5 minutes
</script>
@endpush
