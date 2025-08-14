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
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    
                    <!-- Title -->
                    <h5 class="mb-0">Monthly Document Requests</h5>

                    <!-- Filters + Toggle -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- Date Filter -->
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm" style="width: 140px;">
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm" style="width: 140px;">
                            <button type="submit" class="btn btn-outline-light btn-sm">Filter</button>
                        </form>

                        <!-- Yearly Toggle -->
                        <div class="d-flex align-items-center">
                            <span class="me-2 small text-white">Monthly</span>
                            <label class="switch mb-0">
                                <input type="checkbox" id="toggleYearly">
                                <span class="slider round"></span>
                            </label>
                            <span class="ms-2 small text-white">Yearly</span>
                        </div>
                    </div>
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


<!-- Other charts remain unchanged -->
<div class="row justify-content-center g-3 mb-4">
    <div class="col-12 col-md-6 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-lg w-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Request Distribution by Document Type</h5>
            </div>
            <div class="card-body bg-light"><canvas id="docTypeChart" style="max-height: 300px;"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-md-6 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-lg w-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Request Mode (Walk-in vs Online)</h5>
            </div>
            <div class="card-body bg-light"><canvas id="modeChart" style="max-height: 300px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Request Distribution by Grade Level</h5>
            </div>
            <div class="card-body bg-light">
                <canvas id="gradeLevelChart" style="max-height: 300px;"></canvas>
                <p class="mt-3 text-center mb-0"><strong>Total Requests (All Grade Levels):</strong> <span id="gradeLevelTotal"></span></p>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Monthly Revenue</h5>
            </div>
            <div class="card-body bg-light"><canvas id="revenueChart" style="max-height: 300px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Unclaimed Documents</h5>
            </div>
            <div class="card-body bg-light"><canvas id="unclaimedChart" style="max-height: 300px;"></canvas></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyRequests = @json($monthlyRequestsData);
    const yearlyRequests = @json($yearlyRequestsData);
    const docTypeData = @json($docTypeData);
    const modeData = @json($modeData);
    const revenueData = @json($revenueData);
    const unclaimedData = @json($unclaimedData);
    const gradeLevelData = @json($gradeLevelData);

    const allMonths = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    function mapDataToAllMonths(data) {
        return allMonths.map(month => data[month] ?? 0);
    }

    // Monthly & Yearly toggle chart
    const monthlyValues = mapDataToAllMonths(monthlyRequests);
    const yearlyLabels = Object.keys(yearlyRequests);
    const yearlyValues = Object.values(yearlyRequests);

    const ctx = document.getElementById('monthlyRequestsChart').getContext('2d');
    const monthlyRequestsChart = new Chart(ctx, {
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
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Requests Per Month'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => Number.isInteger(value) ? value : null
                    }
                }
            }
        }
    });

    document.getElementById('toggleYearly').addEventListener('change', function() {
        if (this.checked) {
            monthlyRequestsChart.data.labels = yearlyLabels;
            monthlyRequestsChart.data.datasets[0].data = yearlyValues;
            monthlyRequestsChart.data.datasets[0].label = 'Requests Per Year';
            monthlyRequestsChart.options.plugins.title.text = 'Requests Per Year';
        } else {
            monthlyRequestsChart.data.labels = allMonths;
            monthlyRequestsChart.data.datasets[0].data = monthlyValues;
            monthlyRequestsChart.data.datasets[0].label = 'Requests Per Month';
            monthlyRequestsChart.options.plugins.title.text = 'Requests Per Month';
        }
        monthlyRequestsChart.update();
    });

    // Document Type Chart
    new Chart(document.getElementById('docTypeChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(docTypeData),
            datasets: [{
                label: 'Document Types',
                data: Object.values(docTypeData),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Request Mode Chart
    new Chart(document.getElementById('modeChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(modeData),
            datasets: [{
                label: 'Request Mode',
                data: Object.values(modeData),
                backgroundColor: ['#36A2EB', '#FF6384']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Grade Level Chart
    const gradeLevelLabels = Object.keys(gradeLevelData).map(level => `Grade Level ${level}`);
    const gradeLevelValues = Object.values(gradeLevelData);
    const gradeLevelTotal = gradeLevelValues.reduce((sum, v) => sum + v, 0);
    document.getElementById('gradeLevelTotal').textContent = gradeLevelTotal;

    new Chart(document.getElementById('gradeLevelChart'), {
        type: 'bar',
        data: {
            labels: gradeLevelLabels,
            datasets: [{
                label: 'Total Requests',
                data: gradeLevelValues,
                backgroundColor: '#1dd3b0'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'Revenue (₱)',
                data: mapDataToAllMonths(revenueData),
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Unclaimed Documents Chart
    new Chart(document.getElementById('unclaimedChart'), {
        type: 'bar',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'Unclaimed Documents',
                data: mapDataToAllMonths(unclaimedData),
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection