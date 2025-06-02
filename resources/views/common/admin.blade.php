@extends('layout.blankpage')

@section('content')

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3) !important;
    }
</style>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Dashboard</span></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active text-dark"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    </ol>

    <!-- Dashboard Summary (Total Requests) -->
    <div class="row mb-4">
        <div class="col-12 text-center text-dark">
            <h3><span id="current-time" style="font-size: 1.5rem; font-weight: bold; color: black;"></span></h3>
        </div>
    </div>

    <!-- Dashboard Cards (Pending, Ongoing, Completed Requests) -->
    <div class="row">
        <!-- Pending Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card text-white shadow" style="background-color: #1f2937; border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clock fa-3x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-weight: bold;">Pending Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalPending }}</h2>
                        <p class="mb-0 text-light small">Manage pending requests from users.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="#">View Details</a>
                    <div class="small" style="color: #1dd3b0;"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Ongoing Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card text-white shadow" style="background-color: #1f2937; border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-spinner fa-3x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-weight: bold;">Ongoing Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalOngoing }}</h2>
                        <p class="mb-0 text-light small">Requests that are being processed currently.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="#">View Details</a>
                    <div class="small" style="color: #1dd3b0;"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Completed Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card text-white shadow" style="background-color: #1f2937; border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-weight: bold;">Completed Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalCompleted }}</h2>
                        <p class="mb-0 text-light small">Requests that have been fully completed.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="#">View Details</a>
                    <div class="small" style="color: #1dd3b0;"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Quick Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            <button class="btn btn-lg w-100 text-light" style="background-color: #1dd3b0;" onclick="window.location.href='{{ route('walkin.form') }}'">
                <i class="fas fa-plus-circle me-2"></i> Create New Request
            </button>
        </div>
        <div class="col-md-6">
            <button class="btn  btn-lg w-100" style="background-color: #1f2937; border-color: #1f2937; color: white;" onclick="window.location.href='{{ route('generate') }}'">
                <i class="fas fa-chart-line me-2"></i> Generate Reports
            </button>
        </div>
    </div>

        <!-- Analytic Reports -->
    <!-- <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow" style="border-radius: 16px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Request Status Overview (Bar Chart)</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusBarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow" style="border-radius: 16px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Request Status Distribution (Pie Chart)</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusPieChart"></canvas>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Add Tooltips for Buttons -->
    <script>
        // Function to update the current time on the dashboard
        function updateTime() {
            let currentTime = new Date().toLocaleString();
            document.getElementById('current-time').textContent = "Current Time: " + currentTime;
        }

        // Update the time every second
        setInterval(updateTime, 1000);
        updateTime(); // Initial call to display time immediately

        // const statusLabels = ['Pending', 'Ongoing', 'Completed'];
        // const statusData = [
        //     {{ $totalPending }},
        //     {{ $totalOngoing }},
        //     {{ $totalCompleted }}
        // ];

        // const chartColors = {
        //     pending: '#ffc107',     // Bootstrap text-warning (Yellow)
        //     ongoing: '#cbd5e1',     // Light Grey
        //     completed: '#22c55e'    // Green (custom)
        // };

        // // Bar Chart
        // new Chart(document.getElementById('statusBarChart'), {
        //     type: 'bar',
        //     data: {
        //         labels: statusLabels,
        //         datasets: [{
        //             label: 'Requests',
        //             data: statusData,
        //             backgroundColor: [
        //                 chartColors.pending,
        //                 chartColors.ongoing,
        //                 chartColors.completed
        //             ],
        //             borderColor: '#1f2937',
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         plugins: {
        //             legend: { display: false },
        //         },
        //         scales: {
        //             y: {
        //                 beginAtZero: true,
        //                 ticks: {
        //                     color: '#cbd5e1', // light grey text
        //                     precision: 0
        //                 },
        //                 grid: {
        //                     color: 'rgba(255, 255, 255, 0.05)' // subtle grid lines
        //                 }
        //             },
        //             x: {
        //                 ticks: { color: '#cbd5e1' },
        //                 grid: {
        //                     color: 'rgba(255, 255, 255, 0.05)'
        //                 }
        //             }
        //         }
        //     }
        // });

        // // Pie Chart
        // new Chart(document.getElementById('statusPieChart'), {
        //     type: 'pie',
        //     data: {
        //         labels: statusLabels,
        //         datasets: [{
        //             label: 'Requests',
        //             data: statusData,
        //             backgroundColor: [
        //                 chartColors.pending,
        //                 chartColors.ongoing,
        //                 chartColors.completed
        //             ],
        //             borderColor: '#1f2937',
        //             borderWidth: 2
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         plugins: {
        //             legend: {
        //                 labels: {
        //                     color: '#cbd5e1' // legend text
        //                 }
        //             }
        //         }
        //     }
        // });
    </script>

</div>
@endsection
