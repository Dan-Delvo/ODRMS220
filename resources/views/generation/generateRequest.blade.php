@extends('layout.blankpage')

@section ('content')

<!-- Add CSS for remarks truncation -->
<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-gen {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header-gen h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-gen .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-gen .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-gen .breadcrumb-item.active {
        color: #d1d5db;
    }

    .total-counter {
        background: rgba(29, 211, 176, 0.15);
        border: 1px solid rgba(29, 211, 176, 0.3);
        border-radius: 12px;
        padding: 0.5rem 1.25rem;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .total-counter span {
        color: #1dd3b0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .gen-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .gen-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .gen-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .gen-card-header .header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .gen-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .gen-card-body {
        padding: 1.5rem;
    }

    .gen-card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        width: 100%;
    }

    /* Form Inputs */
    .gen-card .form-control,
    .gen-card .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .gen-card .form-control:focus,
    .gen-card .form-select:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .gen-card .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.35rem;
    }

    /* Buttons */
    .btn-gen-primary {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-gen-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .btn-report {
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-report:hover {
        transform: translateY(-1px);
    }

    /* Search input group */
    .search-input-group .input-group-text {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: #a0aec0;
    }

    .search-input-group .form-control {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }

    .search-input-group .form-control:focus {
        border-left: none;
    }

    .search-input-group .btn-outline-secondary {
        border-radius: 0 10px 10px 0;
        border-color: #e2e8f0;
    }

    /* Active filter badges */
    .filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.775rem;
        font-weight: 500;
    }

    .filter-badge a {
        font-size: 1rem;
        line-height: 1;
    }

    /* Table enhancements */
    .table-gen {
        font-size: 0.85rem;
        margin-bottom: 0;
    }

    .table-gen thead th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.775rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 0.65rem;
        border: none;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-gen tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-gen tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }

    .table-gen tbody td {
        padding: 0.65rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
        font-size: 0.825rem;
    }

    .table-gen .badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 6px;
    }

    .remarks-cell {
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        transition: all 0.2s;
    }

    .remarks-cell:hover {
        background-color: rgba(29, 211, 176, 0.08);
    }

    .remarks-cell.expanded {
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
    }

    /* Table header bar */
    .table-header-bar {
        background: var(--primary-dark);
        padding: 0.875rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .table-header-bar h4 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-header-bar .meta-info {
        font-size: 0.775rem;
        color: #d1d5db;
    }

    .table-header-bar .btn-outline-light {
        border-radius: 8px;
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
    }

    /* Pagination */
    .pagination-wrap {
        padding: 1rem 0 0.5rem;
    }

    /* Loading */
    .loading-overlay {
        text-align: center;
        padding: 2.5rem 1rem;
    }

    .loading-overlay .spinner-border {
        color: #1dd3b0 !important;
    }

    /* Empty state */
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state i {
        color: #d1d5db;
    }

    .empty-state h5 {
        color: #6b7280;
        font-weight: 600;
    }

    .empty-state p {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Alerts */
    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
    }

    /* ======= MOBILE RESPONSIVE ======= */
    @media (max-width: 991px) {
        .page-header-gen {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.25rem 1.5rem;
        }

        .total-counter {
            align-self: flex-start;
        }

        .table-header-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
            padding: 1rem;
        }

        .table-header-bar .meta-info {
            order: 2;
        }

        .table-header-bar .dropdown {
            order: 3;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 767px) {
        .page-header-gen {
            padding: 1rem 1.25rem;
            border-radius: 12px;
        }

        .page-header-gen h1 {
            font-size: 1.35rem;
        }

        .gen-card {
            border-radius: 12px;
        }

        .gen-card-header {
            padding: 0.875rem 1.25rem;
        }

        .gen-card-body {
            padding: 1rem;
        }

        .btn-report {
            font-size: 0.8rem;
            padding: 0.45rem 0.75rem;
        }

        /* Stack the report buttons */
        .report-btn-group {
            flex-direction: column;
        }

        .report-btn-group .btn {
            border-radius: 10px !important;
        }

        /* Table on mobile: horizontal scroll with hint */
        .table-responsive {
            margin: 0 -1rem;
            padding: 0 1rem;
        }

        .table-gen {
            font-size: 0.775rem;
        }

        .table-gen thead th {
            font-size: 0.7rem;
            padding: 0.6rem 0.5rem;
        }

        .table-gen tbody td {
            padding: 0.5rem;
            font-size: 0.775rem;
        }

        .remarks-cell {
            max-width: 120px;
        }

        .filter-badge {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 575px) {
        .page-header-gen h1 {
            font-size: 1.15rem;
        }

        .gen-card-header h5 {
            font-size: 0.875rem;
        }

        .total-counter {
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
        }

        .total-counter span {
            font-size: 1.1rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-gen">
        <div>
            <h1>📄 All Requests</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">All Requests</li>
            </ol>
        </div>
        <div class="total-counter">
            Total: <span id="totalCount">{{ $totalCount }}</span>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('Status'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('Status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('Danger'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        {{ session('Danger') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Generate Reports Card -->
    <div class="gen-card">
        <div class="gen-card-header">
            <span class="header-icon"><i class="fas fa-file-export"></i></span>
            <h5>Generate Reports</h5>
        </div>
        <div class="gen-card-body">
            <form action="{{ route('generateReports') }}" method="GET" id="reportForm" class="row g-3"
                data-swal-loading="true"
                data-swal-title="Generating Reports"
                data-swal-text="This may take a few seconds...">
                <input type="hidden" name="action" id="reportAction" value="">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="status_filter" class="form-label">Status Filter</label>
                    <select id="status_filter" name="status_filter" class="form-select">
                        <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="Pending" {{ request('status_filter') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ request('status_filter') == 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="For Release" {{ request('status_filter') == 'For Release' ? 'selected' : '' }}>For Release</option>
                        <option value="Claimed" {{ request('status_filter') == 'Claimed' ? 'selected' : '' }}>Claimed</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                    <div class="btn-group report-btn-group w-100" role="group">
                        <button type="button" class="btn btn-danger btn-report report-btn" data-action="pdf">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </button>
                        <button type="button" class="btn btn-success btn-report report-btn" data-action="excel">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search and Status Filter -->
    <div class="gen-card">
        <div class="gen-card-body">
            <form id="searchForm" class="row g-3">
                <!-- Search Input -->
                <div class="col-12 col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="search" name="search" class="form-control"
                               placeholder="Search by Req#, Student, Document, School, Mode, or Remarks..."
                               value="{{ request('search') }}">
                        <button type="button" id="clearSearch" class="btn btn-outline-secondary" title="Clear search" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-12 col-sm-8 col-md-4">
                    <label for="table_status_filter" class="form-label">Filter by Status</label>
                    <select id="table_status_filter" name="status" class="form-select">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="For Release" {{ request('status') == 'For Release' ? 'selected' : '' }}>For Release</option>
                        <option value="Claimed" {{ request('status') == 'Claimed' ? 'selected' : '' }}>Claimed</option>
                        <option value="Declined" {{ request('status') == 'Declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>

                <!-- Sort (hidden input to preserve sort state) -->
                <input type="hidden" id="sortInput" name="sort" value="{{ request('sort', 'default') }}">

                <!-- Search Button -->
                <div class="col-12 col-sm-4 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-gen-primary w-100">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters Display -->
    <div id="activeFilters" class="mb-3" style="display: none;">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted" style="font-size: 0.825rem;">Active Filters:</span>
            <span id="searchBadge" class="filter-badge badge bg-info" style="display: none;">
                Search: "<span id="searchText"></span>"
                <a href="#" id="removeSearch" class="text-white ms-1" style="text-decoration: none;">×</a>
            </span>
            <span id="statusBadge" class="filter-badge badge bg-warning text-dark" style="display: none;">
                Status: <span id="statusText"></span>
                <a href="#" id="removeStatus" class="text-dark ms-1" style="text-decoration: none;">×</a>
            </span>
            <a href="#" id="clearAllFilters" class="btn btn-sm btn-outline-secondary ms-1" style="border-radius: 8px; font-size: 0.775rem;">
                <i class="fas fa-redo me-1"></i> Clear All
            </a>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-overlay" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-2" style="font-size: 0.875rem;">Loading requests...</p>
    </div>

    <!-- Requests Table -->
    <div class="gen-card">
        <div class="table-header-bar">
            <h4>
                <i class="fas fa-list-alt"></i>
                Requests
                <span id="statusHeaderBadge" class="badge bg-light text-dark ms-1" style="display: none; font-size: 0.7rem;"></span>
            </h4>

            <div class="meta-info">
                Showing <strong><span id="showingCount">{{ $DocRequests->count() }}</span></strong> of <strong><span id="totalResults">{{ $DocRequests->total() }}</span></strong>
                <span id="filteredText" style="display: none;">filtered</span>
                requests
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Sort by Request Number">
                    <i class="fas fa-sort me-1"></i>Sort
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li>
                        <a class="dropdown-item sort-option" href="#" data-sort="default">
                            Default Order
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item sort-option" href="#" data-sort="asc">
                            <i class="fas fa-sort-numeric-down me-2"></i>Req No. (Low to High)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item sort-option" href="#" data-sort="desc">
                            <i class="fas fa-sort-numeric-up me-2"></i>Req No. (High to Low)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="gen-card-body" style="padding-top: 0;">
            <div id="tableContent">
                @if($DocRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-gen">
                        <thead>
                            <tr>
                                <th title="Request Number">Req #</th>
                                <th>Student</th>
                                <th>Doc</th>
                                <th title="School/Entity">School</th>
                                <th title="Requested Via">Via</th>
                                <th title="Release Mode">Rel Mode</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th title="Request Date">Req Date</th>
                                <th title="Approved Date">App Date</th>
                                <th title="For Release Date">Rel Date</th>
                                <th title="Claimed Date">Clm Date</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach ($DocRequests as $item)
                            <tr class="table-row">
                                <td><strong>{{ $item->req_no ?? 'N/A' }}</strong></td>
                                <td>{{ Str::upper($item->full_name)  ?? 'N/A' }}</td>
                                <td>{{ $item->DocType ?? 'N/A' }}</td>
                                <td>{{ $item->request_schl_entity ?? 'N/A' }}</td>
                                <td>{{ $item->request_mode ?? 'Bulk Request' }}</td>
                                <td>{{ $item->release_mode ?? 'Walk In' }}</td>
                                <td class="remarks-cell" title="Click to expand">
                                    {{ $item->remarks ?? 'N/A' }}
                                </td>
                                <td>
                                    @switch($item->status)
                                    @case('Pending')
                                    <span class="badge bg-warning text-dark">{{ $item->status }}</span>
                                    @break
                                    @case('Processing')
                                    <span class="badge bg-info">{{ $item->status }}</span>
                                    @break
                                    @case('For Release')
                                    <span class="badge bg-primary">{{ $item->status }}</span>
                                    @break
                                    @case('Claimed')
                                    <span class="badge bg-success">{{ $item->status }}</span>
                                    @break
                                    @default
                                    <span class="badge bg-danger">{{ $item->status ?? 'Unknown' }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $item->request_date ? \Carbon\Carbon::parse($item->request_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $item->approve_date ? \Carbon\Carbon::parse($item->approve_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $item->forRelease_date ? \Carbon\Carbon::parse($item->forRelease_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $item->claimed_date ? \Carbon\Carbon::parse($item->claimed_date)->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrap d-flex flex-column justify-content-center align-items-center">
                    {{ $DocRequests->appends(request()->query())->links() }}
                    <small class="text-muted mt-1">
                        Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
                    </small>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <h5>No requests found</h5>
                    <p>
                        @if(request('search'))
                        No requests matching "{{ request('search') }}" found.
                        @elseif(request('status') && request('status') != 'all')
                        No requests with status "{{ request('status') }}" found.
                        @else
                        No requests available at the moment.
                        @endif
                    </p>
                    @if(request('search') || (request('status') && request('status') != 'all'))
                    <a href="#" id="clearFiltersBtn" class="btn btn-gen-primary btn-sm">
                        <i class="fas fa-redo me-2"></i>Clear Filters
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        let currentSearch = '{{ request('search') }}';
        let currentStatus = '{{ request('status', 'all') }}';
        let currentSort = '{{ request('sort', 'default') }}';

        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Set max date to today for date inputs
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').setAttribute('max', today);
        document.getElementById('end_date').setAttribute('max', today);

        // Validate date range
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').setAttribute('min', this.value);
        });

        document.getElementById('end_date').addEventListener('change', function() {
            document.getElementById('start_date').setAttribute('max', this.value);
        });

        // Handle report generation buttons
        document.querySelectorAll('.report-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const form = document.getElementById('reportForm');

                // Validate form
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Set the action value
                document.getElementById('reportAction').value = action;

                // Submit the form
                form.submit();
            });
        });

        // Initialize active filters display
        updateActiveFilters();

        // AJAX Search Form Submit
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            currentSearch = document.getElementById('search').value;
            currentStatus = document.getElementById('table_status_filter').value;
            currentPage = 1;
            fetchRequests();
        });

        // Real-time search with debounce
        let searchTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentSearch = document.getElementById('search').value;
                currentPage = 1;
                fetchRequests();
            }, 500); // 500ms debounce
        });

        // Status filter change
        document.getElementById('table_status_filter').addEventListener('change', function() {
            currentStatus = this.value;
            currentPage = 1;
            fetchRequests();
        });

        // Sort options
        document.querySelectorAll('.sort-option').forEach(function(option) {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                currentSort = this.getAttribute('data-sort');
                document.getElementById('sortInput').value = currentSort;

                // Update active class
                document.querySelectorAll('.sort-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                currentPage = 1;
                fetchRequests();
            });
        });

        // Clear search button
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('search').value = '';
            currentSearch = '';
            currentPage = 1;
            fetchRequests();
        });

        // Remove search filter
        document.getElementById('removeSearch').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('search').value = '';
            currentSearch = '';
            currentPage = 1;
            fetchRequests();
        });

        // Remove status filter
        document.getElementById('removeStatus').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('table_status_filter').value = 'all';
            currentStatus = 'all';
            currentPage = 1;
            fetchRequests();
        });

        // Clear all filters
        document.getElementById('clearAllFilters').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('search').value = '';
            document.getElementById('table_status_filter').value = 'all';
            currentSearch = '';
            currentStatus = 'all';
            currentSort = 'default';
            document.getElementById('sortInput').value = 'default';
            currentPage = 1;
            fetchRequests();
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
                e.preventDefault();
                const link = e.target.matches('.pagination a') ? e.target : e.target.closest('.pagination a');
                const url = link.getAttribute('href');
                if (url) {
                    const urlParams = new URLSearchParams(url.split('?')[1]);
                    currentPage = urlParams.get('page') || 1;
                    fetchRequests();
                }
            }
        });

        // Clear filters button (in empty state)
        document.addEventListener('click', function(e) {
            if (e.target.matches('#clearFiltersBtn') || e.target.closest('#clearFiltersBtn')) {
                e.preventDefault();
                document.getElementById('search').value = '';
                document.getElementById('table_status_filter').value = 'all';
                currentSearch = '';
                currentStatus = 'all';
                currentSort = 'default';
                document.getElementById('sortInput').value = 'default';
                currentPage = 1;
                fetchRequests();
            }
        });

        // Focus search input on Ctrl+F or Cmd+F
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                document.getElementById('search').focus();
            }
        });

        // Handle remarks cell click to expand/collapse
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remarks-cell')) {
                e.target.classList.toggle('expanded');
            }
        });

        // Fetch requests function
        function fetchRequests() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const tableContent = document.getElementById('tableContent');

            // Show loading spinner
            loadingSpinner.style.display = 'block';
            tableContent.style.opacity = '0.5';

            // Build query parameters
            const params = new URLSearchParams({
                search: currentSearch,
                status: currentStatus,
                sort: currentSort,
                page: currentPage
            });

            // Make AJAX request
            fetch('{{ route('generateReports.display') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update table content
                tableContent.innerHTML = data.html;

                // Update counters
                document.getElementById('showingCount').textContent = data.showing;
                document.getElementById('totalResults').textContent = data.total;
                document.getElementById('totalCount').textContent = data.totalCount;

                // Update active filters
                updateActiveFilters();

                // Hide loading spinner
                loadingSpinner.style.display = 'none';
                tableContent.style.opacity = '1';

                // Update URL without page reload
                const newUrl = '{{ route('generateReports.display') }}?' + params.toString();
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Error fetching requests:', error);
                loadingSpinner.style.display = 'none';
                tableContent.style.opacity = '1';
                alert('An error occurred while fetching requests. Please try again.');
            });
        }

        // Update active filters display
        function updateActiveFilters() {
            const activeFiltersDiv = document.getElementById('activeFilters');
            const searchBadge = document.getElementById('searchBadge');
            const statusBadge = document.getElementById('statusBadge');
            const statusHeaderBadge = document.getElementById('statusHeaderBadge');
            const clearSearchBtn = document.getElementById('clearSearch');
            const filteredText = document.getElementById('filteredText');

            let hasFilters = false;

            // Update search badge
            if (currentSearch) {
                document.getElementById('searchText').textContent = currentSearch;
                searchBadge.style.display = 'inline-block';
                clearSearchBtn.style.display = 'block';
                hasFilters = true;
            } else {
                searchBadge.style.display = 'none';
                clearSearchBtn.style.display = 'none';
            }

            // Update status badge
            if (currentStatus && currentStatus !== 'all') {
                document.getElementById('statusText').textContent = currentStatus;
                statusBadge.style.display = 'inline-block';
                statusHeaderBadge.textContent = currentStatus;
                statusHeaderBadge.style.display = 'inline-block';
                hasFilters = true;
            } else {
                statusBadge.style.display = 'none';
                statusHeaderBadge.style.display = 'none';
            }

            // Show/hide active filters section
            activeFiltersDiv.style.display = hasFilters ? 'block' : 'none';
            filteredText.style.display = hasFilters ? 'inline' : 'none';

            // Update sort dropdown active state
            document.querySelectorAll('.sort-option').forEach(option => {
                if (option.getAttribute('data-sort') === currentSort) {
                    option.classList.add('active');
                } else {
                    option.classList.remove('active');
                }
            });
        }

        // Initialize Bootstrap dropdown
        const sortDropdownEl = document.getElementById('sortDropdown');
        if (sortDropdownEl && typeof bootstrap !== 'undefined') {
            new bootstrap.Dropdown(sortDropdownEl);
        }
    });
</script>
@endpush
