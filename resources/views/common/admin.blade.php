@extends('layout.blankpage')

@section('content')

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3) !important;
    }
    
    /* Date input styling */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
    
    input[type="date"]:focus {
        border-color: #1dd3b0 !important;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25) !important;
        outline: none;
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: #fff;
    }
</style>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Dashboard</span></h1>
    
    <!-- Date Range Filter -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="card shadow" style="background-color: #1f2937; border-radius: 12px;">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('dashboard') }}" id="dateFilterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-3 col-md-6">
                                <label for="start_date" class="form-label text-white mb-2" style="font-size: 0.9rem; font-weight: 500;">
                                    <i class="fas fa-calendar-alt me-2" style="color: #1dd3b0;"></i>Start Date
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ $startDate }}"
                                       max="{{ date('Y-m-d') }}"
                                       style="border: 1px solid #374151; background-color: #111827; color: #f3f4f6; padding: 0.5rem 0.75rem;">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label for="end_date" class="form-label text-white mb-2" style="font-size: 0.9rem; font-weight: 500;">
                                    <i class="fas fa-calendar-alt me-2" style="color: #1dd3b0;"></i>End Date
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ $endDate }}"
                                       max="{{ date('Y-m-d') }}"
                                       style="border: 1px solid #374151; background-color: #111827; color: #f3f4f6; padding: 0.5rem 0.75rem;">
                            </div>
                            <div class="col-lg-3 col-md-6 d-flex gap-2">
                                <button type="submit" class="btn text-white flex-grow-1" style="background-color: #1dd3b0; padding: 0.5rem 1rem; font-weight: 500;">
                                    <i class="fas fa-filter me-2"></i>Apply Filter
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-light w-100" style="padding: 0.5rem 1rem; font-weight: 500;">
                                    <i class="fas fa-redo me-2"></i>Clear Filter
                                </a>
                            </div>
                        </div>
                        @if($startDate && $endDate)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0" style="background-color: rgba(29, 211, 176, 0.1); border: 1px solid #1dd3b0; border-radius: 8px;">
                                    <i class="fas fa-info-circle me-2" style="color: #1dd3b0;"></i>
                                    <span style="color: #f3f4f6;">
                                        <strong>Filtered View:</strong> Showing requests from 
                                        <strong>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</strong> to 
                                        <strong>{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

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
        
        // Date range validation
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDateInput = document.getElementById('end_date');
            
            if (startDate) {
                endDateInput.min = startDate;
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = startDate;
                }
            }
        });
        
        document.getElementById('end_date').addEventListener('change', function() {
            const endDate = this.value;
            const startDateInput = document.getElementById('start_date');
            
            if (endDate) {
                startDateInput.max = endDate;
                if (startDateInput.value && startDateInput.value > endDate) {
                    startDateInput.value = endDate;
                }
            }
        });

    </script>

</div>
@endsection
