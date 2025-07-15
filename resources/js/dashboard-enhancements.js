// Dashboard Enhancement JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard features
    initializeCounters();
    initializeCharts();
    initializeRealTimeUpdates();
    initializeTooltips();
    initializeProgressBars();

    // Tab functionality with smooth transitions
    setupTabSystem();

    // Auto-refresh data every 5 minutes
    setInterval(refreshDashboardData, 300000);
});

// Animated counter for statistics
function initializeCounters() {
    const counters = document.querySelectorAll('.stat-number');

    const animateCounter = (counter) => {
        const target = parseInt(counter.textContent.replace(/,/g, ''));
        const duration = 1500;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString();
        }, 16);
    };

    // Use Intersection Observer to trigger animation when visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    });

    counters.forEach(counter => observer.observe(counter));
}

// Simple chart implementation using Chart.js (if available)
function initializeCharts() {
    // Check if Chart.js is available
    if (typeof Chart === 'undefined') {
        console.log('Chart.js not loaded, creating simple progress charts');
        createSimpleCharts();
        return;
    }

    // Create enrollment trend chart
    const enrollmentCtx = document.getElementById('enrollmentChart');
    if (enrollmentCtx) {
        new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Enrollments',
                    data: [12, 19, 15, 25, 22, 30],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
}

// Create simple progress charts without Chart.js
function createSimpleCharts() {
    const chartContainers = document.querySelectorAll('.simple-chart');

    chartContainers.forEach(container => {
        const percentage = container.dataset.percentage || 0;
        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar mt-2';

        const progressFill = document.createElement('div');
        progressFill.className = 'progress-bar-fill';
        progressFill.style.width = '0%';

        progressBar.appendChild(progressFill);
        container.appendChild(progressBar);

        // Animate progress bar
        setTimeout(() => {
            progressFill.style.width = percentage + '%';
        }, 500);
    });
}

// Real-time updates simulation
function initializeRealTimeUpdates() {
    // Add live indicators to show system is active
    const statusIndicators = document.querySelectorAll('.status-indicator');
    statusIndicators.forEach(indicator => {
        indicator.classList.add('online');
    });

    // Simulate real-time notifications
    showWelcomeNotification();
}

// Show welcome notification
function showWelcomeNotification() {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Dashboard loaded successfully
        </div>
    `;

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Enhanced tab system
function setupTabSystem() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'active');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.style.opacity = '0';
            });

            // Add active class to clicked button
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600', 'active');

            // Show corresponding content with fade effect
            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.classList.remove('hidden');
                setTimeout(() => {
                    targetContent.style.opacity = '1';
                }, 50);
            }
        });
    });

    // Initialize first tab
    if (tabButtons.length > 0) {
        tabButtons[0].click();
    }
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-gray-800 text-white text-xs rounded py-1 px-2 z-50 -top-8 left-1/2 transform -translate-x-1/2';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.id = 'tooltip-' + Math.random().toString(36).substr(2, 9);

            this.style.position = 'relative';
            this.appendChild(tooltip);
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('[id^="tooltip-"]');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

// Initialize progress bars
function initializeProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const fill = entry.target.querySelector('.progress-bar-fill');
                if (fill) {
                    const percentage = fill.dataset.percentage || 0;
                    setTimeout(() => {
                        fill.style.width = percentage + '%';
                    }, 200);
                }
                observer.unobserve(entry.target);
            }
        });
    });

    progressBars.forEach(bar => observer.observe(bar));
}

// Refresh dashboard data
async function refreshDashboardData() {
    try {
        // This would typically make an AJAX call to get updated stats
        console.log('Refreshing dashboard data...');

        // For demonstration, we'll just update the timestamp
        const timestampElements = document.querySelectorAll('.last-updated');
        timestampElements.forEach(element => {
            element.textContent = 'Last updated: ' + new Date().toLocaleTimeString();
        });

        // Add a subtle indicator that data was refreshed
        const refreshIndicator = document.createElement('div');
        refreshIndicator.className = 'fixed bottom-4 right-4 bg-blue-500 text-white px-3 py-1 rounded text-xs opacity-0 transition-opacity duration-300';
        refreshIndicator.textContent = 'Data refreshed';

        document.body.appendChild(refreshIndicator);

        setTimeout(() => {
            refreshIndicator.style.opacity = '1';
        }, 100);

        setTimeout(() => {
            refreshIndicator.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(refreshIndicator);
            }, 300);
        }, 2000);

    } catch (error) {
        console.error('Error refreshing dashboard data:', error);
    }
}

// Utility functions
function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search functionality for recent activities
function setupSearchFilter() {
    const searchInput = document.getElementById('activity-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.toLowerCase();
            const activityItems = document.querySelectorAll('.activity-item');

            activityItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }, 300));
    }
}

// Export functions for external use
window.DashboardUtils = {
    refreshData: refreshDashboardData,
    formatNumber: formatNumber,
    setupSearch: setupSearchFilter
};

// Handle page visibility change to pause/resume updates
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Dashboard hidden, pausing updates');
    } else {
        console.log('Dashboard visible, resuming updates');
        refreshDashboardData();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Alt + R to refresh data
    if (e.altKey && e.key === 'r') {
        e.preventDefault();
        refreshDashboardData();
    }

    // Alt + 1-3 to switch tabs
    if (e.altKey && ['1', '2', '3'].includes(e.key)) {
        e.preventDefault();
        const tabIndex = parseInt(e.key) - 1;
        const tabButtons = document.querySelectorAll('.tab-button');
        if (tabButtons[tabIndex]) {
            tabButtons[tabIndex].click();
        }
    }
});
