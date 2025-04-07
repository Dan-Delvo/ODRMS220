@extends('layout.blankpage')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="mt-4 text-dark">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active text-dark">Dashboard</li>
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
            <div class="card bg-light text-dark shadow-sm" style="transition: all 0.3s ease-in-out; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clock fa-3x text-primary"></i>
                    </div>
                    <div>
                        <h2>Pending Requests: {{ $totalPending }}</h2>
                        <p class="mb-0">Manage pending requests from users.</p>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #f8f9fa; border-top: 1px solid #6c757d;">
                    <a class="small text-dark stretched-link" href="{{ route('pending.index') }}">View Details</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Ongoing Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-light text-dark shadow-sm" style="transition: all 0.3s ease-in-out; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-spinner fa-3x text-warning"></i>
                    </div>
                    <div>
                        <h2>Ongoing Requests: {{ $totalOngoing }}</h2>
                        <p class="mb-0">Requests that are being processed currently.</p>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #f8f9fa; border-top: 1px solid #6c757d;">
                    <a class="small text-dark stretched-link" href="{{ route('ongoing.index') }}">View Details</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Completed Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-light text-dark shadow-sm" style="transition: all 0.3s ease-in-out; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <div>
                        <h2>Completed Requests: {{ $totalCompleted }}</h2>
                        <p class="mb-0">Requests that have been fully completed.</p>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: #f8f9fa; border-top: 1px solid #6c757d;">
                    <a class="small text-dark stretched-link" href="{{ route('tables.index') }}">View Details</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Quick Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            <button class="btn btn-primary btn-lg w-100" onclick="window.location.href='{{ route('walkin.form') }}'">
                <i class="fas fa-plus-circle me-2"></i> Create New Request
            </button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-info btn-lg w-100" onclick="window.location.href='{{ route('generate') }}'">
                <i class="fas fa-chart-line me-2"></i> Generate Reports
            </button>
        </div>
    </div>

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
    </script>

</div>
@endsection
