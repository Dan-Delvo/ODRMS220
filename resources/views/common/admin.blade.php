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
    <!-- <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active text-dark"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    </ol> -->

    <!-- Dashboard Summary (Total Requests) -->
    <div class="row mb-4">
        <div class="col-12 text-center text-dark">
            <h3><span id="current-time" style="font-size: 1.5rem; font-weight: bold; color: black;"></span></h3>
        </div>
    </div>

    <!-- Dashboard Cards (Pending, Ongoing, Completed Requests) -->
    <div class="row align-middle justify-content-center mb-4">
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
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="{{ route('pending.index') }}">View Details</a>
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
                        <h3 class="mb-1" style="font-weight: bold;">Processing Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalOngoing }}</h2>
                        <p class="mb-0 text-light small">Requests that are being processed currently.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="{{ route('ongoing.index') }}">View Details</a>
                    <div class="small" style="color: #1dd3b0;"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- For Release Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card text-white shadow" style="background-color: #1f2937; border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-circle-arrow-up fa-3x"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-weight: bold;">For Release Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalRelease }}</h2>
                        <p class="mb-0 text-light small">Requests that is For release.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="{{ route('tables.index') }}">View Details</a>
                    <div class="small" style="color: #1dd3b0;"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Claimed Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4" style="justify-content: center;">
            <div class="card text-white shadow" style="background-color: #1f2937; border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-weight: bold;">Claimed Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalClaimed }}</h2>
                        <p class="mb-0 text-light small">Requests that have been fully completed.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="{{ route('claimed-documents.index') }}">View Details</a>
                    <div class="small" style="color: #1dd3b0;"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Declined Requests Card -->
        <div class="col-xl-4 col-md-6 mb-4" style="justify-content: center;">
            <div class="card text-white shadow" style="background-color: #1f2937; border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-circle-xmark fa-3x text-danger"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-weight: bold;">Declined Requests</h3>
                        <h2 class="mb-2" style="font-weight: bold;">{{ $totalDeclined }}</h2>
                        <p class="mb-0 text-light small">Requests that have been Declined.</p>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
                    <a class="small stretched-link" style="font-weight: bold; color: #1dd3b0" href="{{ route('declined-documents.index') }}">View Details</a>
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
