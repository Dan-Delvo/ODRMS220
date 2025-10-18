@extends('layout.blankpage')
@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Pending Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Pending Requests</li>
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
        <h5 class="mb-2 mb-md-0">Pending Document Requests</h5>

        {{-- Search/Filter Form --}}
        <form method="GET" action="{{ route('pending.index') }}" id="searchForm">
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
                <a href="{{ route('pending.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
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
                        No pending document requests found matching your search criteria.
                        <a href="{{ route('pending.index') }}" class="btn btn-sm btn-outline-warning ms-2">Clear Search</a>
                    @else
                        No pending document requests found.
                    @endif
                </div>
            @else
                <table class="table table-bordered table-hover align-middle" id="requestsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <a href="{{ route('pending.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                            <td class="text-nowrap">
                                @if(!empty($approvePending))
                                    <button type="button"
                                            class="btn btn-sm btn-danger decline-btn"
                                            data-id="{{ $item->id }}">
                                        Decline
                                    </button>
                                    <form action="{{ route('document-request.complete', $item->id) }}"
                                          method="POST"
                                          class="d-inline accept-form">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success accept-btn">
                                            Accept
                                        </button>
                                    </form>
                                @endif

                                @if(!empty($PermissionEdit))
                                    <a href="{{ route('pending.edit', $item->id) }}"
                                       class="btn btn-sm btn-warning">Edit</a>
                                @endif

                                <button class="btn btn-sm btn-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#receiptModal{{ $item->id }}">
                                    Receipt
                                </button>

                                @if($item->supporting_document)
                                    <button type="button"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#documentModal{{ $item->id }}">
                                        <i class="fas fa-file-alt"></i> View Doc
                                    </button>
                                @endif
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

{{-- Decline Reason Modal --}}
<div class="modal fade" id="reasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1f2937;">
                <h5 class="modal-title" style="color: #1dd3b0;">Decline Reason</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="reasonInput" rows="3"
                          placeholder="Enter reason for declining..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeclineBtn">Confirm Decline</button>
            </div>
        </div>
    </div>
</div>

{{-- All your existing modals (Receipt & Document) go here unchanged --}}
@foreach ($DocRequests as $item)
    {{-- Receipt Modal --}}
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

    {{-- Supporting Document Modal --}}
    @if($item->supporting_document)
    <div class="modal fade" id="documentModal{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header text-white" style="background-color: #1f2937;">
                    <h5 class="modal-title" style="color: #1dd3b0;">
                        <i class="fas fa-file-alt me-2"></i>
                        Supporting Document - Request No. {{ $item->req_no }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-light">
                    @php
                        $fileExtension = strtolower(pathinfo($item->supporting_document, PATHINFO_EXTENSION));
                        $documentPath = $item->supporting_document;
                    @endphp

                    @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ asset($documentPath) }}"
                             alt="Supporting Document"
                             class="img-fluid w-100"
                             style="max-height: 70vh; object-fit: contain;">
                    @elseif($fileExtension === 'pdf')
                        <div class="p-4">
                            <iframe src="{{ asset($documentPath) }}"
                                    width="100%"
                                    height="400px"
                                    style="border: 1px solid #ddd;"></iframe>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <i class="fas fa-file text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">{{ strtoupper($fileExtension) }} Document</h5>
                            <p class="text-muted">{{ basename($item->supporting_document) }}</p>
                            <a href="{{ asset($documentPath) }}" class="btn btn-primary" download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    @endif

                    <div class="p-3 bg-white border-top">
                        <div class="row text-start">
                            <div class="col-md-4">
                                <small class="text-muted">Student:</small><br>
                                <strong>{{ $item->studentInformation->full_name }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Document Type:</small><br>
                                <strong>{{ $item->documents->DocType }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">File Type:</small><br>
                                <strong>{{ strtoupper($fileExtension) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #1f2937;">
                    <a href="{{ asset($documentPath) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
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
    const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
    const reasonInput = document.getElementById('reasonInput');
    const confirmDeclineBtn = document.getElementById('confirmDeclineBtn');

    let pendingDeclineId = null;
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
        window.location.href = '{{ route("pending.index") }}';
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

    // Decline workflow
    document.querySelectorAll('.decline-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            pendingDeclineId = this.dataset.id;
            reasonInput.value = '';
            reasonModal.show();
        });
    });

    confirmDeclineBtn?.addEventListener('click', function() {
        const reason = reasonInput.value.trim();

        if (!reason) {
            Swal.fire({
                icon: 'warning',
                title: 'Please enter a reason',
                confirmButtonColor: '#1dd3b0'
            });
            return;
        }

        reasonModal.hide();

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            html: `You are about to decline with reason:<br><strong>${reason}</strong>`,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, decline it'
        }).then((result) => {
            if (result.isConfirmed) {
                submitDeclineForm(pendingDeclineId, reason);
            }
        });
    });

    function submitDeclineForm(id, reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('pending') }}/${id}`;

        form.innerHTML = `
            @csrf
            @method('DELETE')
            <input type="hidden" name="remarks" value="${reason}">
        `;

        Swal.fire({
            title: 'Declining...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        document.body.appendChild(form);
        form.submit();
    }

    // Accept form handling
    document.querySelectorAll('.accept-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('.accept-btn');

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
            document.querySelectorAll('.accept-btn').forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = 'Accept';
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

/* Prevent table data from wrapping & allow full-width expansion */
#tableContainer {
    overflow-x: auto;
}

#requestsTable {
    width: max-content; /* table expands to fit content */
    min-width: 100%;
    table-layout: auto; /* columns adjust naturally */
}

#requestsTable th,
#requestsTable td {
    white-space: nowrap; /* keep data on one line */
    padding: 0.5rem 1rem; /* more space inside cells */
}

/* Make remarks column wider and allow wrapping only there */
#requestsTable th:nth-child(7),
#requestsTable td:nth-child(7) {
    white-space: normal; /* allow wrapping in Remarks only */
    min-width: 100px;
}

</style>

@endsection
