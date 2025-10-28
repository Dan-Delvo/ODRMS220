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

<ul class="nav nav-tabs" data-bs-theme="dark">
  <li class="nav-item">
    <a class="nav-link text-dark" href="{{ route('pending.index') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="{{ route('ongoing.index') }}">Processing</a>
  </li>
  <li class="nav-item">
    <a class="nav-link  text-dark" href="{{ route('tables.index') }}">For Release</a>
  </li>
  <li class="nav-item">
    <a class="nav-link  text-dark" href="{{ route('claimed-documents.index') }}">Claimed</a>
  </li>
  <li class="nav-item">
    <a class="nav-link  text-dark" href="{{ route('declined-documents.index') }}">Declined</a>
  </li>
</ul>

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
                        <td>{{ strtoupper($item->request_schl_entity) }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $item->status }}</span></td>
                        <td>{{ $item->request_date }}</td>
                        <td>{{ $item->approve_date }}</td>
                        <td class="text-nowrap">
                            <div class="d-flex flex-wrap flex-md-nowrap gap-2 justify-content-center">
                                @if(!empty($approveOngoing))
                                <!-- <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-swal-loading="true" data-swal-delete="true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>
                                </form> -->

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

                                <!-- @if($item->receipt)
                                <button class="btn btn-info btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#receiptModal{{ $item->id }}">
                                    Receipt
                                </button>
                                @endif -->
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
    /* ============= HEADER & BADGES ============= */
    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* ============= CARD HEADER & CONTROLS ============= */
    .card-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
        border-bottom: 3px solid #1dd3b0;
    }

    .card-header h5 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* ============= TABLE CONTROLS ============= */
    #tableControls {
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    #tableControls .input-group {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 0.375rem;
        overflow: hidden;
    }

    #searchInput,
    #searchInput:focus {
        border: 1px solid #e5e7eb;
        padding: 0.6rem 0.875rem;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    #searchInput:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15), inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    #searchInput::placeholder {
        color: #9ca3af;
    }

    #tableControls .btn-outline-light {
        border-width: 1.5px;
        border-color: rgba(255, 255, 255, 0.6);
        color: rgba(255, 255, 255, 0.9);
        padding: 0.6rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.9375rem;
    }

    #tableControls .btn-outline-light:hover {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: #1f2937;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    #tableControls .btn-light {
        background-color: #f3f4f6;
        border-color: #e5e7eb;
        color: #1f2937;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9375rem;
    }

    #tableControls .btn-light:hover {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    .form-select {
        padding: 0.6rem 0.875rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-select:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .form-select:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    /* ============= TABLE STYLES ============= */
    .table {
        margin-bottom: 0;
        font-size: 0.9375rem;
    }

    #requestsTable thead th {
        background-color: #1f2937;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.8125rem;
        padding: 0.875rem 1rem;
        border-color: #111827;
        vertical-align: middle;
    }

    #requestsTable thead th a {
        color: #1dd3b0;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    #requestsTable thead th a:hover {
        color: white;
        text-shadow: 0 0 8px rgba(29, 211, 176, 0.5);
    }

    #requestsTable tbody tr {
        transition: all 0.2s ease;
        border-color: #e5e7eb;
    }

    #requestsTable tbody tr:hover {
        background-color: #f9fafb;
        box-shadow: inset 0 1px 3px rgba(29, 211, 176, 0.1);
    }

    #requestsTable tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        color: #374151;
    }

    #requestsTable th:nth-child(7),
    #requestsTable td:nth-child(7) {
        white-space: normal;
        word-break: break-word;
        min-width: 120px;
    }

    /* ============= ACTION BUTTONS ============= */
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border-radius: 0.25rem;
    }

    .table .btn-sm {
        margin: 2px;
        white-space: nowrap;
    }

    .btn-primary {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #14a896;
        border-color: #14a896;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    .btn-danger {
        background-color: #dc2626;
        border-color: #dc2626;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
        border-color: #b91c1c;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: white;
        font-weight: 600;
    }

    .btn-warning:hover {
        background-color: #d97706;
        border-color: #d97706;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* ============= LOADING STATE ============= */
    #loadingSpinner {
        padding: 2rem;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        color: #1dd3b0;
    }

    /* ============= TABLE CONTAINER ============= */
    #tableContainer {
        transition: opacity 0.3s ease;
        overflow-x: auto;
        border-radius: 0.375rem;
        background-color: white;
    }

    #tableContainer::-webkit-scrollbar {
        height: 8px;
    }

    #tableContainer::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    #tableContainer::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    #tableContainer::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ============= PAGINATION ============= */
    .pagination {
        margin-top: 1.5rem;
        gap: 0.25rem;
    }

    .pagination .page-link {
        color: #1dd3b0;
        border-color: #e5e7eb;
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
    }

    .pagination .page-item.active .page-link {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
    }

    /* ============= ALERTS ============= */
    .alert-info {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
    }

    .alert-warning {
        background-color: #fffbeb;
        border: 1px solid #fef08a;
        color: #92400e;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
    }

    /* ============= MODAL STYLES ============= */
    .modal-content {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        border-bottom: 2px solid #1dd3b0;
        padding: 1.25rem;
    }

    .modal-title {
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 1.125rem;
    }

    .modal-body {
        padding: 1.5rem;
        background-color: #fafafa;
    }

    .modal-footer {
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    /* ============= FORM CONTROLS IN MODALS ============= */
    .modal-body .form-control,
    .modal-body .form-select,
    .modal-body textarea {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus,
    .modal-body textarea:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    /* ============= RESPONSIVE DESIGN ============= */
    @media (max-width: 1024px) {
        #requestsTable {
            font-size: 0.875rem;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.75rem 0.875rem;
        }

        #tableControls {
            gap: 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .card-header h5 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        #tableControls {
            width: 100%;
            flex-direction: column;
            gap: 0.75rem;
        }

        #tableControls .input-group,
        #tableControls .form-select,
        #tableControls button {
            width: 100%;
        }

        .btn-sm {
            display: inline-block;
            padding: 0.5rem 0.625rem;
            font-size: 0.75rem;
            margin: 2px 1px;
        }

        .table {
            font-size: 0.8125rem;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.625rem 0.5rem;
            font-size: 0.75rem;
        }

        #requestsTable th:nth-child(7),
        #requestsTable td:nth-child(7) {
            min-width: 80px;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        #tableContainer {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .modal-body {
            padding: 1rem;
        }

        .modal-header {
            padding: 1rem;
        }

        .modal-title {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .badge {
            font-size: 1.25rem;
        }

        .card-header h5 {
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        #tableControls {
            padding: 0;
        }

        #tableControls .input-group input,
        #tableControls .form-select,
        #tableControls button {
            font-size: 1rem;
            padding: 0.75rem;
        }

        .btn-sm {
            display: block;
            width: calc(50% - 3px);
            margin: 2px 1px;
            padding: 0.5rem 0.5rem;
        }

        .table {
            font-size: 0.75rem;
        }

        #requestsTable {
            width: max-content;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.5rem 0.375rem;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        #requestsTable th:nth-child(7),
        #requestsTable td:nth-child(7) {
            white-space: normal;
            min-width: 60px;
        }

        .text-muted {
            font-size: 0.75rem;
        }

        .breadcrumb {
            font-size: 0.8rem;
        }

        .alert {
            padding: 0.75rem;
            font-size: 0.8rem;
        }
    }

    /* ============= UTILITY CLASSES ============= */
    .transition-all {
        transition: all 0.3s ease;
    }

    .shadow-lg {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }
</style>

@endsection
