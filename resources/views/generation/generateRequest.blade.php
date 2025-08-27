@extends('layout.blankpage')

@section ('content')

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">All Requests</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">All Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Requests Total: {{ $totalCount }}</span></h1>
    </div>
</div>

<!-- Date Range Filters and Status Filter for Reports -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color: #1f2937;">
                <h5 class="mb-0">Generate Reports</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('generateReports') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label text-dark">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label text-dark">End Date:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label text-dark">Status Filter:</label>
                        <select id="status_filter" name="status_filter" class="form-select">
                            <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="Pending" {{ request('status_filter') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Processing" {{ request('status_filter') == 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="For Release" {{ request('status_filter') == 'For Release' ? 'selected' : '' }}>For Release</option>
                            <option value="Claimed" {{ request('status_filter') == 'Claimed' ? 'selected' : '' }}>Claimed</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" name="action" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" name="action" value="excel" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Alerts -->
<div class="row mb-4">
    <div class="col-md-12">
        @if(session('Status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('Status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('Danger'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('Danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>
</div>

<!-- Status Filter for Table Display -->
<div class="row mb-3">
    <div class="col-md-12">
        <form action="{{ route('generateReports.display') }}" method="GET" class="d-flex align-items-center" id="statusFilterForm">
            <label for="table_status_filter" class="me-2 text-dark">Filter Table by Status:</label>
            <select id="table_status_filter" name="status" class="form-select me-3" style="width: auto;" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                <option value="For Release" {{ request('status') == 'For Release' ? 'selected' : '' }}>For Release</option>
                <option value="Claimed" {{ request('status') == 'Claimed' ? 'selected' : '' }}>Claimed</option>
            </select>
            <!-- Keep the button but make it optional/hidden -->
            <button type="submit" class="btn text-white d-none" style="background-color: #1dd3b0;">Filter</button>
        </form>
    </div>
</div>

<!-- Requests Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">

                <h4 class="mb-0">
                    Requests
                    @if(request('status') && request('status') != 'all')
                    <span class="badge bg-light text-dark ms-2">{{ request('status') }}</span>
                    @endif

                </h4>

                <div class="text-end">
                    <small>Showing {{ $DocRequests->count() }} of {{ $totalCount }} total requests</small>
                </div>

                <div class="dropdown ms-3 text-start">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Sort by Request Number">
                        <i class="fas fa-sort me-1"></i>Sort
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item sort-option" href="#" data-sort="default">Default Order</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item sort-option" href="#" data-sort="req-asc"><i class="fas fa-sort-numeric-down me-2"></i>Req No. (A-Z)</a></li>
                        <li><a class="dropdown-item sort-option" href="#" data-sort="req-desc"><i class="fas fa-sort-numeric-up me-2"></i>Req No. (Z-A)</a></li>
                    </ul>
                </div>
            </div>



            <div class="card-body">
                @if($DocRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
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
                            <tr class="table-row"
                                data-req-no="{{ strtolower($item->req_no) }}"
                                data-student="{{ strtolower($item->studentInformation->full_name ?? '') }}"
                                data-document="{{ strtolower($item->documents->DocType ?? '') }}"
                                data-school="{{ strtolower($item->request_schl_entity ?? '') }}"
                                data-via="{{ strtolower($item->request_mode ?? '') }}"
                                data-release-mode="{{ strtolower($item->release_mode ?? '') }}"
                                data-remarks="{{ strtolower($item->remarks ?? '') }}"
                                data-status="{{ strtolower($item->status ?? '') }}"
                                data-request-date="{{ $item->request_date ?? '' }}"
                                data-approve-date="{{ $item->approve_date ?? '' }}"
                                data-release-date="{{ $item->forRelease_date ?? '' }}"
                                data-claimed-date="{{ $item->claimed_date ?? '' }}">
                                <td>{{ $item->req_no ?? 'N/A' }}</td>
                                <td>{{ $item->studentInformation->full_name ?? 'N/A' }}</td>
                                <td>{{ $item->documents->DocType ?? 'N/A' }}</td>
                                <td>{{ $item->request_schl_entity ?? 'N/A' }}</td>
                                <td>{{ $item->request_mode ?? 'N/A' }}</td>
                                <td>{{ $item->release_mode ?? 'N/A' }}</td>
                                <td>{{ $item->remarks ?? 'N/A' }}</td>
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
                <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                    {{ $DocRequests->appends(request()->query())->links() }}
                    <small class="text-muted">
                        Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
                    </small>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No requests found</h5>
                    <p class="text-muted">
                        @if(request('status') && request('status') != 'all')
                        No requests with status "{{ request('status') }}" found.
                        @else
                        No requests available at the moment.
                        @endif
                    </p>
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
    });

    document.addEventListener('DOMContentLoaded', function() {
    // Existing code for alerts and date filters...

    // Sorting functionality for Req #
    const sortOptions = document.querySelectorAll('.sort-option');
    const tableBody = document.getElementById('tableBody');
    const originalRows = Array.from(tableBody.querySelectorAll('.table-row')); // Preserve initial order

    sortOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const sortType = this.getAttribute('data-sort');

                let rows = Array.from(tableBody.querySelectorAll('.table-row'));

                if (sortType === 'req-asc') {
                    rows.sort((a, b) => {
                        const reqA = a.getAttribute('data-req-no');
                        const reqB = b.getAttribute('data-req-no');
                        return reqA.localeCompare(reqB, undefined, { numeric: true });
                    });
                } else if (sortType === 'req-desc') {
                    rows.sort((a, b) => {
                        const reqA = a.getAttribute('data-req-no');
                        const reqB = b.getAttribute('data-req-no');
                        return reqB.localeCompare(reqA, undefined, { numeric: true });
                    });
                } else if (sortType === 'default') {
                    rows = [...originalRows];
                }

                // Update table with sorted rows
                tableBody.innerHTML = '';
                rows.forEach(row => tableBody.appendChild(row));
            });
        });
    });

</script>
@endpush
