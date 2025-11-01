@extends('layout.blankpage')
@section('content')
    @include('layout.partials.message')

    {{-- Header Section --}}
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h1 class="mt-4">
                <span class="badge page-title-badge">Processing Requests</span>
            </h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
                <li class="breadcrumb-item active">Processing Requests</li>
            </ol>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <h1 class="mt-md-4">
                <span class="badge count-badge">Total: {{ $totalCount }}</span>
            </h1>
        </div>
    </div>

    <x-tabs page='Processing' />


    {{-- Main Card --}}
        {{-- Main Card --}}
        {{-- Main Card --}}
        {{-- Main Card --}}
    <div class="card shadow-lg border-0 rounded-lg mt-3">
        {{-- Card Header with Search/Filter Controls --}}
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Processing Document Requests</h5>

            {{-- Search/Filter Form --}}
            <form method="GET" action="{{ route('ongoing.index') }}" id="searchForm" class="w-100 w-md-auto">
                <div class="search-controls">
                    {{-- Search Input --}}
                    <div class="input-group search-input-group">
                        <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                            placeholder="Search..." value="{{ request('search') }}">
                        <button class="btn btn-outline-light btn-sm px-2" type="button" id="clearSearch" title="Clear">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Filter Dropdown --}}
                    <select name="filter" id="filterSelect" class="form-select form-select-sm filter-select">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document</option>
                        <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School</option>
                        <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Req No.</option>
                        <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                    </select>

                    {{-- Sort Dropdown --}}
                    <select name="sort" id="sortSelect" class="form-select form-select-sm sort-select">
                        <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Sort</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z-A</option>
                    </select>

                    {{-- Search Button --}}
                    <button type="submit" class="btn btn-light btn-sm search-btn px-2">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Card Body --}}
        <div class="card-body bg-light">
            {{-- Search Info Banner --}}
            @if (request('search'))
                <div class="alert alert-info mb-3 py-2">
                    <small>
                        <i class="fas fa-search me-1"></i>
                        Showing results for: <strong>"{{ request('search') }}"</strong>
                        @if (request('filter') != 'all')
                            in <strong>{{ ucfirst(request('filter')) }}</strong>
                        @endif
                        @if (request('sort') != 'default')
                            - Sorted by <strong>Request No. ({{ request('sort') == 'asc' ? 'A-Z' : 'Z-A' }})</strong>
                        @endif
                        <a href="{{ route('ongoing.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
                    </small>
                </div>
            @endif

            {{-- Loading Spinner --}}
            <div id="loadingSpinner" class="text-center my-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="table-responsive" id="tableContainer">
                @if ($DocRequests->isEmpty())
                    <div class="alert alert-warning text-center my-3">
                        @if (request('search'))
                            No processing document requests found matching your search criteria.
                            <a href="{{ route('ongoing.index') }}" class="btn btn-sm btn-outline-warning ms-2">Clear
                                Search</a>
                        @else
                            No processing document requests found.
                        @endif
                    </div>
                @else
                    <table class="table table-bordered table-hover align-middle" id="requestsTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="sortable-header">
                                    <a href="{{ route('ongoing.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-white text-decoration-none d-flex align-items-center gap-1">
                                        <span>Req #</span>
                                        @if (request('sort') == 'asc')
                                            <i class="fas fa-sort-up"></i>
                                        @elseif(request('sort') == 'desc')
                                            <i class="fas fa-sort-down"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Student</th>
                                <th>Doc</th>
                                <th>School</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Req Date</th>
                                <th>App Date</th>
                                <th class="action-column">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($DocRequests as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->req_no }}</td>
                                    <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                                    <td>{{ $item->documents->DocType }}</td>
                                    <td>{{ strtoupper($item->request_schl_entity) }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td><span class="badge bg-warning text-dark status-badge">{{ $item->status }}</span></td>
                                    <td>{{ $item->request_date }}</td>
                                    <td>{{ $item->approve_date }}</td>
                                    <td class="action-column">
                                        <div class="btn-group-vertical btn-group-sm d-md-inline" role="group">
                                            @if (!empty($approveOngoing))
                                                <form action="{{ route('document-request2.complete', $item->id) }}"
                                                    method="POST" class="d-inline complete-form" data-swal-loading="true"
                                                    data-swal-title="Completing Document Request"
                                                    data-swal-text="This may take a few seconds...">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm complete-btn mb-1">
                                                        <i class="fas fa-check me-1"></i>Complete
                                                    </button>
                                                </form>

                                                @if ($item->documents->DocType == 'Good Moral')
                                                    <form action="{{ route('doc.print', $item->id) }}" method="POST"
                                                        class="d-inline print-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-info btn-sm print-btn mb-1">
                                                            <i class="fas fa-print me-1"></i>Print
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            @if (!empty($PermissionEdit))
                                                <a href="{{ route('ongoing.edit', $item->id) }}"
                                                    class="btn btn-warning btn-sm mb-1">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            @endif

                                            @if (!empty($deleteCompleted))
                                                <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST"
                                                    class="d-inline delete2-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm delete2-btn mb-1">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Pagination --}}
            @if (!$DocRequests->isEmpty())
                <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                    {{ $DocRequests->appends(request()->query())->links() }}
                    <small class="text-muted">
                        Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of
                        {{ $DocRequests->total() }}
                    </small>
                </div>
            @endif
        </div>
    </div>

    {{-- Receipt Modals --}}
    @foreach ($DocRequests as $item)
        @if ($item->receipt)
            <div class="modal fade" id="receiptModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow-sm">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title mx-auto">Receipt #{{ $item->receipt->receipt_no }}</h5>
                            <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3"
                                data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body bg-white text-dark px-4 py-3">
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/UBLOGO.png') }}" alt="UB Logo" class="mb-2"
                                    style="max-height: 80px;">
                                <h5 class="fw-bold mb-1">Upper Bicutan National High School</h5>
                                <div class="text-muted small">Official Receipt</div>
                            </div>
                            <hr>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Document:</strong>
                                <span>{{ $item->documents->DocType }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Amount Paid:</strong>
                                <span>₱{{ number_format($item->receipt->doc_amount, 2) }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Student ID:</strong>
                                <span>{{ $item->receipt->name_request }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Date:</strong>
                                <span>{{ \Carbon\Carbon::parse($item->receipt->time_request)->format('F d, Y - h:i A') }}</span>
                            </div>
                            <hr>
                            <div class="text-center mt-3">
                                <div class="text-muted small">Thank you for your request!</div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close
                                Receipt</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearch');
            const filterSelect = document.getElementById('filterSelect');
            const sortSelect = document.getElementById('sortSelect');

            let searchTimeout = null;

            // Auto-submit form on filter/sort change
            filterSelect?.addEventListener('change', function() {
                searchForm.submit();
            });

            sortSelect?.addEventListener('change', function() {
                searchForm.submit();
            });

            // Clear search button
            clearSearchBtn?.addEventListener('click', function() {
                window.location.href = '{{ route('ongoing.index') }}';
            });

            // Auto-search with debounce (optional - remove if you want manual search only)
            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                        searchForm.submit();
                    }
                }, 500);
            });

            // Show loading spinner on form submit
            searchForm?.addEventListener('submit', function() {
                document.getElementById('loadingSpinner').style.display = 'block';
                document.getElementById('tableContainer').style.opacity = '0.5';
            });

            // Handle Complete button clicks with loading spinner
            document.querySelectorAll('.complete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.complete-btn');

                    btn.disabled = true;
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

                    // Disable other buttons in the same row
                    const row = this.closest('tr');
                    row.querySelectorAll('button, a.btn').forEach(b => {
                        if (b !== btn) {
                            b.disabled = true;
                            b.style.opacity = '0.5';
                        }
                    });

                    setTimeout(() => this.submit(), 100);
                });
            });

            // Handle Delete button clicks with confirmation
            document.querySelectorAll('.delete-form, .delete2-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.delete-btn, .delete2-btn');

                    if (confirm('Are you sure you want to delete this request?')) {
                        btn.disabled = true;
                        btn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

                        // Disable other buttons in the same row
                        const row = this.closest('tr');
                        row.querySelectorAll('button, a.btn').forEach(b => {
                            if (b !== btn) {
                                b.disabled = true;
                                b.style.opacity = '0.5';
                            }
                        });

                        setTimeout(() => this.submit(), 100);
                    }
                });
            });

            // Handle Print button clicks with loading spinner
            document.querySelectorAll('.print-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.print-btn');

                    btn.disabled = true;
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span>Printing...';

                    // Disable other buttons in the same row
                    const row = this.closest('tr');
                    row.querySelectorAll('button, a.btn').forEach(b => {
                        if (b !== btn) {
                            b.disabled = true;
                            b.style.opacity = '0.5';
                        }
                    });

                    setTimeout(() => this.submit(), 100);
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput?.focus();
                }
            });

            // Re-enable buttons on page show (back button)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    document.querySelectorAll('.complete-btn, .delete-btn, .delete2-btn, .print-btn')
                        .forEach(btn => {
                            btn.disabled = false;
                            btn.style.opacity = '1';

                            if (btn.classList.contains('complete-btn')) {
                                btn.innerHTML = 'Complete';
                            } else if (btn.classList.contains('delete-btn') || btn.classList.contains(
                                    'delete2-btn')) {
                                btn.innerHTML = 'Delete';
                            } else if (btn.classList.contains('print-btn')) {
                                btn.innerHTML = 'Print';
                            }
                        });
                }
            });
        });
    </script>

    <style>
        /* ===== CORE VARIABLES ===== */
        :root {
            --primary-color: #1dd3b0;
            --secondary-color: #1f2937;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        /* ===== HEADER BADGES ===== */
        .page-title-badge {
            background-color: var(--primary-color);
            font-size: clamp(1.25rem, 4vw, 2rem);
            padding: 0.5rem 1rem;
        }

        .count-badge {
            background-color: var(--secondary-color);
            font-size: clamp(1rem, 3vw, 2rem);
            padding: 0.5rem 1rem;
        }

        /* ===== CARD HEADER ===== */
        .card-header-custom {
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
        }

        @media (min-width: 768px) {
            .card-header-custom {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        /* ===== SEARCH CONTROLS ===== */
        .search-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            width: 100%;
            justify-content: flex-end;
            margin-left: auto;
        }

        @media (min-width: 768px) {
            .search-controls {
                width: auto;
                flex-wrap: nowrap;
                flex: 0 0 auto;
                justify-content: flex-end;
            }
        }

        .search-input-group {
            flex: 1 1 auto;
            min-width: 150px;
            max-width: 250px;
        }

        @media (min-width: 768px) {
            .search-input-group {
                width: 180px;
                flex: 0 0 180px;
            }
        }

        .filter-select,
        .sort-select {
            flex: 1 1 auto;
            min-width: 80px;
            max-width: 120px;
        }

        @media (min-width: 768px) {
            .filter-select,
            .sort-select {
                width: 100px;
                flex: 0 0 100px;
            }
        }

        .search-btn {
            flex: 0 0 auto;
            min-width: 38px;
        }

        /* ===== FORM CONTROLS ===== */
        #searchInput:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        }

        /* ===== TABLE STYLES ===== */
        #requestsTable {
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        #requestsTable thead th {
            white-space: nowrap;
            vertical-align: middle;
            font-weight: 600;
            padding: 0.3rem 0.3rem;
            font-size: 0.8rem;
            line-height: 1;
        }

        #requestsTable tbody td {
            vertical-align: middle;
            padding: 0.3rem 0.3rem;
            font-size: 0.8rem;
            line-height: 1;
        }

        .sortable-header a {
            transition: opacity 0.2s;
        }

        .sortable-header a:hover {
            opacity: 0.8;
        }

        /* ===== ACTION COLUMN ===== */
        .action-column {
            min-width: 200px !important;
            max-width: 200px !important;
            width: 200px !important;
            white-space: normal !important;
        }

        .btn-group-vertical {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            gap: 0.15rem !important;
            width: 100% !important;
        }

        .action-column .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            width: 95px !important;
            min-width: 95px !important;
            max-width: 95px !important;
            display: inline-block !important;
            text-align: center !important;
            margin-bottom: 0 !important;
        }

        .action-column .btn i {
            font-size: 0.75rem !important;
        }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            white-space: nowrap;
        }

        /* ===== BUTTON STATES ===== */
        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .spinner-border-sm {
            width: 0.875rem;
            height: 0.875rem;
            border-width: 0.125rem;
        }

        /* ===== LOADING STATE ===== */
        #tableContainer {
            transition: opacity 0.3s ease;
            overflow-x: auto;
        }

        /* ===== ALERT STYLES ===== */
        .alert-info {
            background-color: #e3f2fd;
            border-color: #1976d2;
            color: #1565c0;
        }

        /* ===== MODAL STYLES ===== */
        .modal-dialog {
            max-width: 600px;
        }

        .modal-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* ===== RESPONSIVE TABLE ===== */
        .table-responsive {
            border-radius: 0.25rem;
        }

        @media (max-width: 576px) {
            #requestsTable {
                font-size: 0.75rem;
            }

            #requestsTable th,
            #requestsTable td {
                padding: 0.25rem 0.25rem;
            }

            .btn-sm {
                font-size: 0.65rem;
                padding: 0.2rem 0.3rem;
            }
        }

        /* ===== PAGINATION ===== */
        .pagination {
            margin-bottom: 0;
        }

        /* ===== SMOOTH TRANSITIONS ===== */
        .btn,
        .form-control,
        .form-select {
            transition: all 0.2s ease-in-out;
        }

        /* ===== UTILITY CLASSES ===== */
        .fw-semibold {
            font-weight: 600;
        }
    </style>

@endsection
