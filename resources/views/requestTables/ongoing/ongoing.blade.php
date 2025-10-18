@extends('layout.blankpage')
@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Processing Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Processing Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
        style="background-color: #1f2937;">
        <h5 class="mb-2 mb-md-0">Processing Document Requests</h5>

        {{-- Search/Filter Form --}}
        <form method="GET" action="{{ route('ongoing.index') }}" id="searchForm">
            <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
                {{-- Search Input --}}
                <div class="input-group" style="width: 300px;">
                    <input type="text"
                        name="search"
                        id="searchInput"
                        class="form-control form-control-sm"
                        placeholder="Search requests..."
                        value="{{ request('search') }}">
                    <button class="btn btn-outline-light btn-sm"
                        type="button"
                        id="clearSearch"
                        title="Clear search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Filter Dropdown --}}
                <select name="filter" id="filterSelect" class="form-select form-select-sm" style="width: auto;">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Fields</option>
                    <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student Name</option>
                    <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document Type</option>
                    <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School/Entity</option>
                    <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Request No.</option>
                    <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                </select>

                {{-- Sort Dropdown --}}
                <select name="sort" id="sortSelect" class="form-select form-select-sm" style="width: auto;">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default Order</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Req No. (A-Z)</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Req No. (Z-A)</option>
                </select>

                {{-- Search Button --}}
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    {{-- Card Body --}}
    <div class="card-body bg-light">
        {{-- Search Info Banner --}}
        @if(request('search'))
        <div class="alert alert-info mb-3 py-2">
            <small>
                <i class="fas fa-search me-1"></i>
                Showing results for: <strong>"{{ request('search') }}"</strong>
                @if(request('filter') != 'all')
                in <strong>{{ ucfirst(request('filter')) }}</strong>
                @endif
                @if(request('sort') != 'default')
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
            @if($DocRequests->isEmpty())
            <div class="alert alert-warning text-center my-3">
                @if(request('search'))
                No processing document requests found matching your search criteria.
                <a href="{{ route('ongoing.index') }}" class="btn btn-sm btn-outline-warning ms-2">Clear Search</a>
                @else
                No processing document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <a href="{{ route('ongoing.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
                                class="text-white text-decoration-none">
                                Req #
                                @if(request('sort') == 'asc')
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($DocRequests as $item)
                    <tr>
                        <td>{{ $item->req_no }}</td>
                        <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ $item->request_schl_entity }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $item->status }}</span></td>
                        <td>{{ $item->request_date }}</td>
                        <td>{{ $item->approve_date }}</td>
                        <td class="text-nowrap">
                            <div class="d-flex flex-wrap flex-md-nowrap gap-2 justify-content-center">
                                @if(!empty($approveOngoing))
                                <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-swal-loading="true" data-swal-delete="true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>
                                </form>

                                <form action="{{ route('document-request2.complete', $item->id) }}" method="POST" class="d-inline complete-form"
                                    data-swal-loading="true"
                                    data-swal-title="Completing Document Request"
                                    data-swal-text="This may take a few seconds...">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm complete-btn">Complete</button>
                                </form>

                                @if($item->documents->DocType == 'Good Moral')
                                <form action="{{ route('doc.print', $item->id) }}" method="POST" class="d-inline print-form">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-sm print-btn">Print</button>
                                </form>
                                @endif
                                @endif

                                @if(!empty($PermissionEdit))
                                <a href="{{ route('ongoing.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endif

                                @if(!empty($deleteCompleted))
                                <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST" class="d-inline delete2-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete2-btn">Delete</button>
                                </form>
                                @endif

                                <button class="btn btn-info btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#receiptModal{{ $item->id }}">
                                    Receipt
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Pagination --}}
        @if(!$DocRequests->isEmpty())
        <div class="d-flex flex-column justify-content-center align-items-center mt-3">
            {{ $DocRequests->appends(request()->query())->links() }}
            <small class="text-muted">
                Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
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
                    <img src="{{ asset('images/UBLOGO.png') }}" alt="UB Logo" class="mb-2" style="max-height: 80px;">
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
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close Receipt</button>
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
            window.location.href = '{{ route("ongoing.index") }}';
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
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

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
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

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
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Printing...';

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
                document.querySelectorAll('.complete-btn, .delete-btn, .delete2-btn, .print-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '1';

                    if (btn.classList.contains('complete-btn')) {
                        btn.innerHTML = 'Complete';
                    } else if (btn.classList.contains('delete-btn') || btn.classList.contains('delete2-btn')) {
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
    /* Core table styles */
    #requestsTable {
        font-size: 0.85rem;
    }

    #requestsTable thead th a {
        transition: opacity 0.2s;
    }

    #requestsTable thead th a:hover {
        opacity: 0.8;
    }

    /* Search controls */
    #tableControls {
        gap: 0.5rem;
    }

    #searchInput:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    .form-select:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* Button states */
    .btn:disabled {
        cursor: not-allowed;
    }

    .spinner-border-sm {
        width: 0.875rem;
        height: 0.875rem;
    }

    /* Loading state */
    #tableContainer {
        transition: opacity 0.3s;
        overflow-x: auto;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #tableControls {
            width: 100%;
        }

        #tableControls .input-group,
        #tableControls select {
            width: 100% !important;
            margin-bottom: 0.5rem;
        }

        .table-responsive {
            font-size: 0.75rem;
        }
    }

    /* Action buttons */
    .table td.text-nowrap .btn-sm {
        margin: 1px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Ensure buttons maintain their size during loading */
    .complete-btn,
    .delete-btn,
    .delete2-btn,
    .print-btn {
        min-width: 70px;
    }

    /* Prevent table data from wrapping & allow full-width expansion */
    #tableContainer {
        overflow-x: auto;
    }

    #requestsTable {
        width: max-content;
        min-width: 100%;
        table-layout: auto;
    }

    #requestsTable th,
    #requestsTable td {
        white-space: nowrap;
        padding: 0.5rem 1rem;
    }

    /* Make remarks column wider and allow wrapping only there */
    #requestsTable th:nth-child(7),
    #requestsTable td:nth-child(7) {
        white-space: normal;
        min-width: 100px;
    }
</style>

@endsection