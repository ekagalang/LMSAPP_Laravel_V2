<x-app-layout>
    @push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .chart-container-lg {
            position: relative;
            height: 400px;
            width: 100%;
        }
    </style>
    @endpush

    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 -mx-4 -my-2 px-4 py-8 sm:px-6 lg:px-8 rounded-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-white text-3xl font-bold leading-tight">
                        {{ __('Analytics Sertifikat') }}
                    </h2>
                    <p class="text-blue-100 mt-2">
                        {{ __('Dashboard analytics untuk pembuatan dan penggunaan sertifikat') }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('certificate-management.index') }}"
                       class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Main Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📜</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-blue-100 text-sm font-medium">Total Sertifikat</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['total_certificates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🎓</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-green-100 text-sm font-medium">Kursus dengan Sertifikat</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['total_courses_with_certificates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📋</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-purple-100 text-sm font-medium">Total Template</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['total_templates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-yellow-100 text-sm font-medium">Bulan Ini</div>
                            <div class="text-white text-2xl font-bold">{{ number_format($analytics['certificates_this_month']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Period Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-emerald-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Hari Ini</div>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($analytics['certificates_today']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📈</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Minggu Ini</div>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($analytics['certificates_this_week']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📊</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Bulan Ini</div>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($analytics['certificates_this_month']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Statistics Chart -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistik Bulanan</h3>
                    <p class="text-sm text-gray-500 mt-1">Jumlah sertifikat yang dibuat per bulan (12 bulan terakhir)</p>
                </div>
                <div class="p-6">
                    @if($monthlyStats->count() > 0)
                        <div class="chart-container-lg">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">📊</div>
                            <p>Belum ada data statistik bulanan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Kursus Terpopuler</h3>
                    <p class="text-sm text-gray-500 mt-1">10 kursus dengan sertifikat terbanyak</p>
                </div>
                <div class="p-6">
                    @if($courseStats->count() > 0)
                        <div class="chart-container-lg">
                            <canvas id="courseChart"></canvas>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">🎓</div>
                            <p>Belum ada data kursus</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Template Usage Statistics -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Penggunaan Template</h3>
                <p class="text-sm text-gray-500 mt-1">Statistik penggunaan template sertifikat</p>
            </div>
            <div class="p-6">
                @if($templateStats->count() > 0)
                    <div class="chart-container-lg">
                        <canvas id="templateChart"></canvas>
                    </div>
                @else
                    <div class="text-center text-gray-500 py-12">
                        <div class="text-6xl mb-4">📋</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada template yang digunakan</h3>
                        <p class="text-sm text-gray-500">Template akan muncul di sini setelah digunakan untuk membuat sertifikat</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Market & Demographic Insights -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Demographics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Demografi Peserta</h3>
                    <p class="text-sm text-gray-500 mt-1">Berdasarkan data form sertifikat</p>
                </div>
                <div class="p-6 space-y-8">
                    <!-- Gender -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-900">Jenis Kelamin</h4>
                            <span class="text-xs text-gray-500">Total: {{ number_format($analytics['total_certificates']) }}</span>
                        </div>
                        @if(!empty($genderStats) && count($genderStats) > 0)
                            <div class="chart-container">
                                <canvas id="genderChart"></canvas>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm text-center py-8">Belum ada data gender</div>
                        @endif
                    </div>

                    <!-- Age Groups -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-900">Kelompok Usia</h4>
                        </div>
                        @php $ageTotal = array_sum($ageGroups); @endphp
                        @if($ageTotal > 0)
                            <div class="chart-container">
                                <canvas id="ageChart"></canvas>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm text-center py-8">Belum ada data usia</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Market Insights -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Market & Pemasaran</h3>
                    <p class="text-sm text-gray-500 mt-1">Institusi, profesi, dan domain email teratas</p>
                </div>
                <div class="p-6 grid grid-cols-1 gap-8">
                    <!-- Occupations -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Profesi Teratas</h4>
                        @if(count($topOccupations) > 0)
                            <div class="chart-container">
                                <canvas id="occupationChart"></canvas>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm text-center py-8">Belum ada data profesi</div>
                        @endif
                    </div>

                    <!-- Institutions -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Institusi Teratas</h4>
                        @if(count($topInstitutions) > 0)
                            <div class="chart-container">
                                <canvas id="institutionChart"></canvas>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm text-center py-8">Belum ada data institusi</div>
                        @endif
                    </div>

                    <!-- Email Domains -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Domain Email Teratas</h4>
                        @if(count($topEmailDomains) > 0)
                            <div class="chart-container">
                                <canvas id="emailChart"></canvas>
                            </div>
                        @else
                            <div class="text-gray-500 text-sm text-center py-8">Belum ada data email</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Chart.js Global Config
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6B7280';

    // Color Palettes
    const colorPalette = {
        blue: ['#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE', '#DBEAFE'],
        purple: ['#8B5CF6', '#A78BFA', '#C4B5FD', '#DDD6FE', '#EDE9FE'],
        green: ['#10B981', '#34D399', '#6EE7B7', '#A7F3D0', '#D1FAE5'],
        gradient: ['#3B82F6', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#06B6D4', '#6366F1', '#8B5CF6'],
    };

    // Monthly Statistics Chart
    @if($monthlyStats->count() > 0)
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyStats as $stat)
                        '{{ \Carbon\Carbon::createFromDate($stat->year, $stat->month, 1)->format('M Y') }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Sertifikat Dibuat',
                    data: [
                        @foreach($monthlyStats as $stat)
                            {{ $stat->count }},
                        @endforeach
                    ],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif

    // Course Statistics Chart
    @if($courseStats->count() > 0)
    const courseCtx = document.getElementById('courseChart');
    if (courseCtx) {
        new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($courseStats as $course)
                        '{{ Str::limit($course->title, 30) }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Jumlah Sertifikat',
                    data: [
                        @foreach($courseStats as $course)
                            {{ $course->certificates_count }},
                        @endforeach
                    ],
                    backgroundColor: colorPalette.gradient,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif

    // Template Usage Chart
    @if($templateStats->count() > 0)
    const templateCtx = document.getElementById('templateChart');
    if (templateCtx) {
        new Chart(templateCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($templateStats as $template)
                        '{{ $template->name }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($templateStats as $template)
                            {{ $template->certificates_count }},
                        @endforeach
                    ],
                    backgroundColor: colorPalette.gradient,
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: { size: 12 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Gender Distribution Chart
    @if(!empty($genderStats) && count($genderStats) > 0)
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($genderStats as $gender => $count)
                        '{{ ucfirst($gender) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($genderStats as $gender => $count)
                            {{ $count }},
                        @endforeach
                    ],
                    backgroundColor: ['#3B82F6', '#EC4899', '#8B5CF6'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Age Distribution Chart
    @php $ageTotal = array_sum($ageGroups); @endphp
    @if($ageTotal > 0)
    const ageCtx = document.getElementById('ageChart');
    if (ageCtx) {
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($ageGroups as $label => $count)
                        '{{ $label }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: [
                        @foreach($ageGroups as $label => $count)
                            {{ $count }},
                        @endforeach
                    ],
                    backgroundColor: '#10B981',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif

    // Occupation Chart
    @if(count($topOccupations) > 0)
    const occupationCtx = document.getElementById('occupationChart');
    if (occupationCtx) {
        new Chart(occupationCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($topOccupations as $row)
                        '{{ Str::limit($row->occupation, 25) }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        @foreach($topOccupations as $row)
                            {{ $row->total }},
                        @endforeach
                    ],
                    backgroundColor: '#8B5CF6',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif

    // Institution Chart
    @if(count($topInstitutions) > 0)
    const institutionCtx = document.getElementById('institutionChart');
    if (institutionCtx) {
        new Chart(institutionCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($topInstitutions as $row)
                        '{{ Str::limit($row->institution_name, 25) }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        @foreach($topInstitutions as $row)
                            {{ $row->total }},
                        @endforeach
                    ],
                    backgroundColor: '#F59E0B',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif

    // Email Domains Chart
    @if(count($topEmailDomains) > 0)
    const emailCtx = document.getElementById('emailChart');
    if (emailCtx) {
        new Chart(emailCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($topEmailDomains as $row)
                        '{{ $row['domain'] }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($topEmailDomains as $row)
                            {{ $row['total'] }},
                        @endforeach
                    ],
                    backgroundColor: colorPalette.gradient,
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: { size: 11 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush
</x-app-layout>
