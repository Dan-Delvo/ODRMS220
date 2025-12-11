@extends('layout.blankpage')

@section('content')

<style>
    .dashboard-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        overflow: hidden;
        position: relative;
    }
    
    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
    }
    
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1dd3b0, #16a085);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .dashboard-card:hover::before {
        opacity: 1;
    }
    
    .dashboard-card .card-body {
        position: relative;
        z-index: 1;
    }
    
    .dashboard-card .icon-wrapper {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .dashboard-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
        background: rgba(29, 211, 176, 0.15);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Date input styling */
    input[type="date"] {
        transition: all 0.3s ease;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    input[type="date"]:hover::-webkit-calendar-picker-indicator {
        transform: scale(1.2);
    }
    
    input[type="date"]:focus {
        border-color: #1dd3b0 !important;
        box-shadow: 0 0 0 0.3rem rgba(29, 211, 176, 0.25) !important;
        outline: none;
        transform: translateY(-2px);
    }
    
    .filter-card {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        border: 1px solid rgba(29, 211, 176, 0.1);
        transition: all 0.3s ease;
    }
    
    .filter-card:hover {
        border-color: rgba(29, 211, 176, 0.3);
        box-shadow: 0 8px 24px rgba(29, 211, 176, 0.1);
    }
    
    .btn-filter {
        background: linear-gradient(135deg, #1dd3b0 0%, #16a085 100%);
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }
    
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(29, 211, 176, 0.4);
    }
    
    .btn-filter:active {
        transform: translateY(0);
    }
    
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: #fff;
        transform: translateY(-2px);
    }
    
    .quick-action-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    
    .quick-action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .quick-action-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
    }
    
    .page-header {
        margin-bottom: 2rem;
        animation: fadeInDown 0.6s ease-out;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dashboard-card {
        animation: fadeInUp 0.6s ease-out backwards;
    }
    
    .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
    .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
    .dashboard-card:nth-child(3) { animation-delay: 0.3s; }
    .dashboard-card:nth-child(4) { animation-delay: 0.4s; }
    .dashboard-card:nth-child(5) { animation-delay: 0.5s; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .time-display {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        padding: 1rem 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(29, 211, 176, 0.1);
        display: inline-block;
    }
    
    .card-footer-link {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .card-footer-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #1dd3b0;
        transition: width 0.3s ease;
    }
    
    .card-footer-link:hover::after {
        width: 100%;
    }
</style>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="mt-4 mb-0">
            <span class="badge" style="background: linear-gradient(135deg, #1dd3b0 0%, #16a085 100%); font-size: 2.5rem; padding: 0.75rem 1.5rem; box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);">
                Dashboard
            </span>
        </h1>
    </div>

    <!-- Dashboard Summary (Current Time) -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 id="current-time" style="font-size: 2rem; font-weight: 600; color: #1f2937; margin: 0;"></h2>
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-lg filter-card" style="border-radius: 16px; transition: none;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-sliders-h me-2" style="color: #1dd3b0; font-size: 1.25rem;"></i>
                        <h5 class="mb-0 text-white" style="font-weight: 600;">Filter by Date Range</h5>
                    </div>
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
                                       style="border: 1px solid #374151; background-color: #0f172a; color: #f3f4f6; padding: 0.65rem 0.85rem; border-radius: 8px;">
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
                                       style="border: 1px solid #374151; background-color: #0f172a; color: #f3f4f6; padding: 0.65rem 0.85rem; border-radius: 8px;">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <button type="submit" class="btn btn-filter text-white w-100" style="padding: 0.65rem 1.5rem; font-weight: 600; border-radius: 8px;">
                                    <i class="fas fa-filter me-2"></i>Apply Filter
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-light w-100" style="padding: 0.65rem 1.5rem; font-weight: 600; border-radius: 8px;">
                                    <i class="fas fa-redo me-2"></i>Clear Filter
                                </a>
                            </div>
                        </div>
                        @if($startDate && $endDate)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert mb-0" style="background: linear-gradient(135deg, rgba(29, 211, 176, 0.15) 0%, rgba(22, 160, 133, 0.15) 100%); border: 2px solid #1dd3b0; border-radius: 12px; padding: 1rem 1.25rem;">
                                    <i class="fas fa-info-circle me-2" style="color: #1dd3b0; font-size: 1.1rem;"></i>
                                    <span style="color: #f3f4f6; font-weight: 500;">
                                        Filtered View: Showing requests from 
                                        <strong style="color: #1dd3b0;">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</strong> to 
                                        <strong style="color: #1dd3b0;">{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</strong>
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
    <!-- Dashboard Cards (Pending, Ongoing, Completed Requests) -->
    <div class="row g-4 mb-5">
        <!-- Pending Requests Card -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card text-white shadow-lg" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-clock fa-2x" style="color: #3b82f6;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white-50" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">PENDING</p>
                            <h2 class="stat-number mb-0">{{ $totalPending }}</h2>
                        </div>
                    </div>
                    <p class="mb-0 mt-3 text-white-50 small">Awaiting review and processing</p>
                </div>
                <div class="card-footer border-0 d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.03); padding: 1rem 1.5rem;">
                    <a class="card-footer-link small text-decoration-none" style="font-weight: 600; color: #1dd3b0;" href="{{ route('pending.index') }}">
                        View Details
                    </a>
                    <i class="fas fa-arrow-right" style="color: #1dd3b0;"></i>
                </div>
            </div>
        </div>

        <!-- Processing Requests Card -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card text-white shadow-lg" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-spinner fa-2x" style="color: #f59e0b;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white-50" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">PROCESSING</p>
                            <h2 class="stat-number mb-0">{{ $totalOngoing }}</h2>
                        </div>
                    </div>
                    <p class="mb-0 mt-3 text-white-50 small">Currently being processed</p>
                </div>
                <div class="card-footer border-0 d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.03); padding: 1rem 1.5rem;">
                    <a class="card-footer-link small text-decoration-none" style="font-weight: 600; color: #1dd3b0;" href="{{ route('ongoing.index') }}">
                        View Details
                    </a>
                    <i class="fas fa-arrow-right" style="color: #1dd3b0;"></i>
                </div>
            </div>
        </div>

        <!-- For Release Requests Card -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card text-white shadow-lg" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-circle-arrow-up fa-2x" style="color: #8b5cf6;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white-50" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">FOR RELEASE</p>
                            <h2 class="stat-number mb-0">{{ $totalRelease }}</h2>
                        </div>
                    </div>
                    <p class="mb-0 mt-3 text-white-50 small">Ready for release</p>
                </div>
                <div class="card-footer border-0 d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.03); padding: 1rem 1.5rem;">
                    <a class="card-footer-link small text-decoration-none" style="font-weight: 600; color: #1dd3b0;" href="{{ route('tables.index') }}">
                        View Details
                    </a>
                    <i class="fas fa-arrow-right" style="color: #1dd3b0;"></i>
                </div>
            </div>
        </div>

        <!-- Claimed Requests Card -->
        <div class="col-xl-6 col-md-6">
            <div class="card dashboard-card text-white shadow-lg" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-check-circle fa-2x" style="color: #10b981;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white-50" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">CLAIMED</p>
                            <h2 class="stat-number mb-0">{{ $totalClaimed }}</h2>
                        </div>
                    </div>
                    <p class="mb-0 mt-3 text-white-50 small">Successfully completed and claimed</p>
                </div>
                <div class="card-footer border-0 d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.03); padding: 1rem 1.5rem;">
                    <a class="card-footer-link small text-decoration-none" style="font-weight: 600; color: #1dd3b0;" href="{{ route('claimed-documents.index') }}">
                        View Details
                    </a>
                    <i class="fas fa-arrow-right" style="color: #1dd3b0;"></i>
                </div>
            </div>
        </div>

        <!-- Declined Requests Card -->
        <div class="col-xl-6 col-md-6">
            <div class="card dashboard-card text-white shadow-lg" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-circle-xmark fa-2x" style="color: #ef4444;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white-50" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">DECLINED</p>
                            <h2 class="stat-number mb-0">{{ $totalDeclined }}</h2>
                        </div>
                    </div>
                    <p class="mb-0 mt-3 text-white-50 small">Requests that were declined</p>
                </div>
                <div class="card-footer border-0 d-flex align-items-center justify-content-between" style="background-color: rgba(255,255,255,0.03); padding: 1rem 1.5rem;">
                    <a class="card-footer-link small text-decoration-none" style="font-weight: 600; color: #1dd3b0;" href="{{ route('declined-documents.index') }}">
                        View Details
                    </a>
                    <i class="fas fa-arrow-right" style="color: #1dd3b0;"></i>
                </div>
            </div>
        </div>
    </div>


    <!-- Additional Quick Action Buttons -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <button class="quick-action-btn btn btn-lg w-100 text-white position-relative" 
                    style="background: linear-gradient(135deg, #1dd3b0 0%, #16a085 100%); padding: 1.25rem; border-radius: 16px; box-shadow: 0 8px 24px rgba(29, 211, 176, 0.3);" 
                    onclick="window.location.href='{{ route('walkin.form') }}'">
                <span class="position-relative" style="z-index: 1;">
                    <i class="fas fa-plus-circle me-2"></i>Create New Request
                </span>
            </button>
        </div>
        <div class="col-md-6">
            <button class="quick-action-btn btn btn-lg w-100 text-white position-relative" 
                    style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); padding: 1.25rem; border: 2px solid rgba(29, 211, 176, 0.3); border-radius: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);" 
                    onclick="window.location.href='{{ route('generate') }}'">
                <span class="position-relative" style="z-index: 1;">
                    <i class="fas fa-chart-line me-2"></i>Generate Reports
                </span>
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
