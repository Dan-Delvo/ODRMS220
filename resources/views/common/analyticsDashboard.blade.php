@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4"><span class="badge text-bg-dark">Analytics Dashboard</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Analytics</li>
        </ol>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Monthly Document Requests -->
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-2 mb-md-0">Monthly Document Requests</h5>
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-outline-light btn-sm">Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body bg-light">
                <canvas id="monthlyRequestsChart" style="max-height: 300px;"></canvas>
                <p class="mt-3 text-center mb-0">
                    <strong>Total Requests:</strong> {{ $totalRequestsInInterval }}<br>
                    <small>
                        From <strong>{{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }}</strong>
                        to <strong>{{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</strong>
                    </small>
                </p>
            </div>
        </div>
    </div>
</div>



<div class="row justify-content-center g-3">
    <!-- Request Distribution by Document Type -->
    <div class="col-12 col-md-6 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-lg w-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Request Distribution by Document Type</h5>
            </div>
            <div class="card-body bg-light">
                <canvas id="docTypeChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Request Mode (Walk-in vs Online) -->
    <div class="col-12 col-md-6 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-lg w-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Request Mode (Walk-in vs Online)</h5>
            </div>
            <div class="card-body bg-light">
                <canvas id="modeChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Request Distribution by Grade Level -->
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Request Distribution by Grade Level</h5>
            </div>
            <div class="card-body bg-light">
                <canvas id="gradeLevelChart" style="max-height: 300px;"></canvas>
                <p class="mt-3 text-center mb-0">
                    <strong>Total Requests (All Grade Levels):</strong> <span id="gradeLevelTotal"></span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Monthly Revenue -->
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Monthly Revenue</h5>
            </div>
            <div class="card-body bg-light">
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Number of Unclaimed Documents -->
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Unclaimed Documents</h5>
            </div>
            <div class="card-body bg-light">
                <canvas id="unclaimedChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const monthlyRequests = @json($monthlyRequestsData);
    const docTypeData = @json($docTypeData);
    const modeData = @json($modeData);
    const revenueData = @json($revenueData);
    const unclaimedData = @json($unclaimedData);
    const gradeLevelData = @json($gradeLevelData);

    const allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    function mapDataToAllMonths(data) {
        return allMonths.map(month => data[month] ?? 0);
    }

    // Monthly Document Requests Chart
    const monthlyValues = mapDataToAllMonths(monthlyRequests);
    new Chart(document.getElementById('monthlyRequestsChart'), {
        type: 'bar',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'Requests Per Month',
                data: monthlyValues,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Requests Per Month'
                }
            }
        }
    });

    // Document Type Chart (Doughnut)
    const docTypeLabels = Object.keys(docTypeData);
    const docTypeValues = Object.values(docTypeData);
    const docTypeColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
    new Chart(document.getElementById('docTypeChart'), {
        type: 'doughnut',
        data: {
            labels: docTypeLabels,
            datasets: [{
                label: 'Document Types',
                data: docTypeValues,
                backgroundColor: docTypeColors.slice(0, docTypeLabels.length),
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Document Type Distribution'
                }
            }
        }
    });

    // Request Mode Chart (Pie)
    const modeLabels = Object.keys(modeData);
    const modeValues = Object.values(modeData);
    const modeColors = ['#36A2EB', '#FF6384'];
    new Chart(document.getElementById('modeChart'), {
        type: 'pie',
        data: {
            labels: modeLabels,
            datasets: [{
                label: 'Request Mode',
                data: modeValues,
                backgroundColor: modeColors.slice(0, modeLabels.length),
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Request Mode'
                }
            }
        }
    });

    // Grade Level Chart (Bar)
    const gradeLevelLabels = Object.keys(gradeLevelData).map(level => `Grade Level ${level}`);
    const gradeLevelValues = Object.values(gradeLevelData);
    const gradeLevelTotal = gradeLevelValues.reduce((sum, value) => sum + value, 0);
    const gradeLevelColors = ['#8E44AD', '#3498DB', '#1ABC9C', '#F39C12', '#E74C3C', '#2ECC71', '#9B59B6', '#34495E', '#E67E22', '#95A5A6', '#F1C40F', '#E91E63'];
    
    // Display total in the HTML
    document.getElementById('gradeLevelTotal').textContent = gradeLevelTotal;
    
    new Chart(document.getElementById('gradeLevelChart'), {
        type: 'bar',
        data: {
            labels: gradeLevelLabels,
            datasets: [{
                label: 'Total Requests',
                data: gradeLevelValues,
                backgroundColor: gradeLevelColors.slice(0, gradeLevelLabels.length),
                borderColor: gradeLevelColors.slice(0, gradeLevelLabels.length),
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Requests'
                    },
                    ticks: {
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Grade Level'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Document Requests by Grade Level'
                }
            }
        }
    });

    // Monthly Revenue Chart (Line)
    const revenueLabels = allMonths;
    const revenueValues = mapDataToAllMonths(revenueData);
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue (₱)',
                data: revenueValues,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: 'Monthly Revenue'
                }
            }
        }
    });

    // Unclaimed Documents Per Month (Bar Chart)
    const unclaimedValues = mapDataToAllMonths(unclaimedData);

    new Chart(document.getElementById('unclaimedChart'), {
        type: 'bar',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'Unclaimed Documents',
                data: unclaimedValues,
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 50 // jumps 0, 50, 100, 150...
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Unclaimed Documents Per Month'
                }
            }
        }
    });
</script>

@endsection