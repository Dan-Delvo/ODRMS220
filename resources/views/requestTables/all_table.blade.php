@extends('layout.blankpage')
@section('content')
@include('layout.partials.message')

{{-- ===== PAGE HEADER ===== --}}
<div class="page-header-pending">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <div class="d-flex align-items-center gap-3">
                <div class="page-icon-square">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h1 class="page-title mb-0">Pending Requests</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: var(--primary-green);">Dashboard</a></li>
                            <li class="breadcrumb-item active text-muted">Pending Requests</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <span class="total-count-pill">
                <i class="fas fa-database me-1"></i> Total: <span>{{ $totalCount }}</span>
            </span>
        </div>
    </div>
</div>

{{-- ===== MAIN CARD ===== --}}
<div class="card pending-card shadow-sm border-0 mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header pending-card-header">
        <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
            <div class="header-icon-square">
                <i class="fas fa-table"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-white">Pending Document Requests</h5>
        </div>

        {{-- Search/Filter Form --}}
        <form method="GET" action="{{ route('pending.index') }}" id="searchForm">
            <div class="d-flex w-100 gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
                {{-- Search Input --}}
                <div class="d-flex align-items-center" style="min-width:0;">
                    <div class="input-group search-input-group" style="width: 300px;">
                        <input type="text"
                            name="search"
                            id="searchInput"
                            class="form-control form-control-sm"
                            placeholder="Search requests..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-light btn-sm"
                            type="button"
                            id="clearSearch"
                            title="Clear search"
                            style="display: {{ request('search') ? 'inline-block' : 'none' }};">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Filters (right) --}}
                <div class="ms-auto d-flex align-items-center gap-2">
                    {{-- Filter Dropdown --}}
                    <select name="filter" id="filterSelect" class="form-select form-select-sm filter-select">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Fields</option>
                        <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student Name</option>
                        <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document Type</option>
                        <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School/Entity</option>
                        <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Request No.</option>
                        <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                    </select>

                    {{-- Sort Dropdown --}}
                    <select name="sort" id="sortSelect" class="form-select form-select-sm sort-select">
                        <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default Order</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Req No. (A-Z)</option>
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Req No. (Z-A)</option>
                    </select>

                    {{-- Reset Button --}}
                    <a href="{{ route('pending.index') }}" class="btn btn-outline-light btn-sm reset-btn" id="resetBtn" title="Reset all filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Card Body --}}
    <div class="card-body bg-light">
        {{-- Search Info Banner --}}
        @if(request('search') || (request('filter') && request('filter') !== 'all') || (request('sort') && request('sort') !== 'default'))
        <div class="alert table-info-banner mb-3 py-2">
            <small>
                <i class="fas fa-search me-1"></i>
                @if(request('search'))
                Showing results for: <strong>"{{ request('search') }}"</strong>
                @endif
                @if(request('filter') && request('filter') != 'all')
                in <strong>{{ ucfirst(request('filter')) }}</strong>
                @endif
                @if(request('sort') && request('sort') != 'default')
                - Sorted by <strong>Request No. ({{ request('sort') == 'asc' ? 'A-Z' : 'Z-A' }})</strong>
                @endif
                <a href="{{ route('pending.index') }}" class="btn btn-sm btn-clear-all ms-2" id="clearAllBtn">
                    <i class="fas fa-times me-1"></i> Clear All
                </a>
            </small>
        </div>
        @endif

        {{-- Loading Spinner --}}
        <div id="loadingSpinner" class="text-center my-4" style="display: none;">
            <div class="spinner-border" role="status" style="color: var(--primary-green);">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="table-responsive" id="tableContainer">
            @if($DocRequests->isEmpty())
            <div class="alert empty-alert text-center my-3">
                <i class="fas fa-inbox" style="font-size: 2rem; color: var(--primary-green); display: block; margin-bottom: 0.5rem;"></i>
                @if(request('search'))
                No pending document requests found matching your search criteria.
                <br>
                <a href="{{ route('pending.index') }}" class="btn btn-sm btn-clear-all mt-2">
                    <i class="fas fa-times me-1"></i> Clear Search
                </a>
                @else
                No pending document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle table-pending" id="requestsTable">
                <thead>
                    <tr>
                        <th class="sortable-header">
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
                        <td class="fw-semibold" style="color: var(--primary-dark);">{{ $item->req_no }}</td>
                        <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ strtoupper($item->request_schl_entity) }}</td>
                        <td class="remarks-cell">{{ $item->remarks }}</td>
                        <td><span class="badge status-badge bg-warning text-dark">{{ $item->status }}</span></td>
                        <td class="text-nowrap">{{ $item->request_date }}</td>
                        <td class="action-column">
                            <div class="btn-group-actions">
                                @if(!empty($approvePending))
                                <button type="button"
                                    class="btn btn-sm btn-action btn-decline decline-btn"
                                    data-id="{{ $item->id }}">
                                    <i class="fas fa-times-circle me-1"></i> Decline
                                </button>
                                <form action="{{ route('document-request.complete', $item->id) }}"
                                    method="POST"
                                    class="d-inline accept-form"
                                    data-swal-loading="true"
                                    data-swal-title="Accepting Document Request"
                                    data-swal-text="This may take a few seconds...">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-action btn-accept accept-btn">
                                        <i class="fas fa-check-circle me-1"></i> Accept
                                    </button>
                                </form>
                                @endif

                                @if(!empty($PermissionEdit))
                                <a href="{{ route('pending.edit', $item->id) }}"
                                    class="btn btn-sm btn-action btn-edit-action">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                @endif

                                @if($item->supporting_document)
                                <button type="button"
                                    class="btn btn-sm btn-action btn-doc-view"
                                    data-bs-toggle="modal"
                                    data-bs-target="#documentModal{{ $item->id }}">
                                    <i class="fas fa-file-alt me-1"></i> View Doc
                                </button>
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
        <div id="paginationContainer">
            @if(!$DocRequests->isEmpty())
            <div class="d-flex flex-column justify-content-center align-items-center mt-3 pending-pagination">
                {{ $DocRequests->appends(request()->query())->links() }}
                <small class="text-muted mt-1">
                    Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
                </small>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== DECLINE REASON MODAL ===== --}}
<div class="modal fade modal-pending" id="reasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background-color: var(--primary-dark); border-bottom: 2px solid rgba(29,211,176,0.3);">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <div class="header-icon-square" style="width:28px; height:28px; font-size:0.75rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <span style="color: var(--primary-green);">Decline Reason</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <textarea class="form-control" id="reasonInput" rows="3"
                    placeholder="Enter reason for declining..." required
                    style="border-radius: 10px; border: 1px solid #e2e8f0;"></textarea>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-confirm-decline" id="confirmDeclineBtn">
                    <i class="fas fa-ban me-1"></i> Confirm Decline
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== RECEIPT & DOCUMENT MODALS ===== --}}
@foreach ($DocRequests as $item)
{{-- Receipt Modal --}}
@if ($item->receipt)
<div class="modal fade modal-pending" id="receiptModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background-color: var(--primary-dark); border-bottom: 2px solid rgba(29,211,176,0.3);">
                <h5 class="modal-title d-flex align-items-center gap-2 mx-auto">
                    <div class="header-icon-square" style="width:28px; height:28px; font-size:0.75rem;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <span style="color: var(--primary-green);">Receipt #{{ $item->receipt->receipt_no }}</span>
                </h5>
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
                    <span>&#8369;{{ number_format($item->receipt->doc_amount, 2) }}</span>
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
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-cancel-modal w-100" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Supporting Document Modal --}}
@if($item->supporting_document)
<div class="modal fade modal-pending" id="documentModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background-color: var(--primary-dark); border-bottom: 2px solid rgba(29,211,176,0.3);">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <div class="header-icon-square" style="width:28px; height:28px; font-size:0.75rem;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span style="color: var(--primary-green);">Supporting Document - Request No. {{ $item->req_no }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center bg-light">
                @php
                $fileExtension = strtolower(pathinfo($item->supporting_document, PATHINFO_EXTENSION));
                $documentPath = $item->supporting_document;
                @endphp

                @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
               <img src="/public/{{trim($documentPath)}}" target="_blank"
                    alt="Supporting Document"
                    class="img-fluid w-100"
                    style="max-height: 70vh; object-fit: contain;">
                @elseif($fileExtension === 'pdf')
                <div class="p-4">
                    <iframe src="{{ asset($documentPath) }}"
                        width="100%"
                        height="400px"
                        style="border: 1px solid #ddd; border-radius: 8px;"></iframe>
                </div>
                @else
                <div class="p-5 text-center">
                    <i class="fas fa-file" style="font-size: 4rem; color: var(--primary-green);"></i>
                    <h5 class="mt-3">{{ strtoupper($fileExtension) }} Document</h5>
                    <p class="text-muted">{{ basename($item->supporting_document) }}</p>
                    <a href="{{ asset($documentPath) }}" class="btn btn-download-doc" download>
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
            <div class="modal-footer" style="background-color: var(--primary-dark); border-top: 2px solid rgba(29,211,176,0.3);">
                <a href="/public/{{trim($documentPath)}}" target="_blank" class="btn btn-outline-info btn-sm" style="border-radius: 8px;">
                    <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
                </a>
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- ===== JavaScript ===== --}}
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

        toggleClearButton();

        // Show/Hide clear button dynamically
        function toggleClearButton() {
            if (clearSearchBtn) {
                clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
            }
        }

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

        // Auto-search with debounce
        searchInput?.addEventListener('input', function() {
            toggleClearButton();
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
                cancelButtonColor: '#1f2937',
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
            form.action = `{{ url('pending/decline') }}/${id}`;

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
                    btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Accept';
                });
            }
        });
    });
</script>

<style>
    /* ===== CORE VARIABLES ===== */
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
    }

    /* ===== PAGE HEADER ===== */
    .page-header-pending {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-top: 1rem;
        box-shadow: var(--card-shadow);
    }

    .page-icon-square {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary-green) 0%, #17a98b 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .page-title {
        color: white;
        font-size: clamp(1.25rem, 4vw, 1.75rem);
        font-weight: 700;
    }

    .total-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: rgba(29, 211, 176, 0.15);
        color: var(--primary-green);
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: clamp(0.9rem, 3vw, 1.1rem);
        border: 1px solid rgba(29, 211, 176, 0.3);
    }

    .total-count-pill span {
        color: white;
        font-weight: 700;
    }

    /* ===== MAIN CARD ===== */
    .pending-card {
        border-radius: 16px !important;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: box-shadow 0.3s ease;
    }

    .pending-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .pending-card-header {
        background-color: var(--primary-dark) !important;
        color: white;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid rgba(29, 211, 176, 0.3) !important;
    }

    @media (min-width: 768px) {
        .pending-card-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    .header-icon-square {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary-green) 0%, #17a98b 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    /* ===== SEARCH CONTROLS ===== */
    .search-input-group {
        flex: 1 1 auto;
        min-width: 200px;
        max-width: 350px;
    }

    @media (min-width: 768px) {
        .search-input-group {
            width: 300px;
            flex: 0 0 300px;
        }
    }

    .filter-select,
    .sort-select {
        flex: 1 1 auto;
        min-width: 80px;
        max-width: 130px;
    }

    @media (min-width: 768px) {
        .filter-select,
        .sort-select {
            width: 110px;
            flex: 0 0 110px;
        }
    }

    .reset-btn {
        border-radius: 8px !important;
        padding: 0.3rem 0.6rem;
    }

    .reset-btn:hover {
        background: rgba(29, 211, 176, 0.2);
        border-color: var(--primary-green);
    }

    /* ===== FORM CONTROLS FOCUS ===== */
    #searchInput:focus,
    .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* ===== TABLE INFO BANNER ===== */
    .table-info-banner {
        background: rgba(29, 211, 176, 0.08);
        border: 1px solid rgba(29, 211, 176, 0.3);
        border-radius: 10px;
        color: var(--primary-dark);
    }

    .btn-clear-all {
        background: var(--primary-dark);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.2rem 0.75rem;
        font-size: 0.75rem;
        transition: all 0.2s;
    }

    .btn-clear-all:hover {
        background: var(--danger-color);
        color: white;
    }

    /* ===== EMPTY ALERT ===== */
    .empty-alert {
        background: #fff;
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        color: #64748b;
        padding: 2rem;
    }

    /* ===== TABLE STYLES ===== */
    .table-pending {
        font-size: 0.85rem;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-pending thead {
        background: var(--primary-dark);
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .table-pending thead th {
        background: var(--primary-dark);
        color: white;
        white-space: nowrap;
        vertical-align: middle;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        line-height: 1.3;
        border-color: rgba(255,255,255,0.1);
    }

    .table-pending tbody td {
        vertical-align: middle;
        padding: 0.4rem 0.75rem;
        font-size: 0.85rem;
        line-height: 1.3;
        white-space: nowrap;
    }

    .table-pending tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.04);
    }

    .sortable-header a {
        transition: opacity 0.2s;
    }

    .sortable-header a:hover {
        opacity: 0.8;
    }

    /* Remarks column - allow wrapping */
    .remarks-cell {
        white-space: normal !important;
        min-width: 120px;
        max-width: 200px;
    }

    /* ===== STATUS BADGE ===== */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 50px;
        white-space: nowrap;
        font-weight: 600;
    }

    /* ===== ACTION COLUMN ===== */
    .action-column {
        white-space: nowrap !important;
        padding: 0.3rem 0.5rem !important;
    }

    .btn-group-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        gap: 0.3rem;
        align-items: center;
    }

    .btn-action {
        padding: 0.3rem 0.6rem !important;
        font-size: 0.75rem !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        line-height: 1.2 !important;
        border: none !important;
        transition: all 0.2s ease !important;
        font-weight: 500;
    }

    .btn-action i {
        font-size: 0.7rem !important;
    }

    .btn-decline {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .btn-decline:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71d2a 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.4);
    }

    .btn-accept {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
    }

    .btn-accept:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(40, 167, 69, 0.4);
    }

    .btn-edit-action {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #212529;
    }

    .btn-edit-action:hover {
        background: linear-gradient(135deg, #e0a800 0%, #c69500 100%);
        color: #212529;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(255, 193, 7, 0.4);
    }

    .btn-doc-view {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }

    .btn-doc-view:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(23, 162, 184, 0.4);
    }

    /* Make form inline in actions */
    .action-column .accept-form {
        display: inline !important;
        margin: 0 !important;
    }

    /* ===== DOWNLOAD BUTTON (in doc modal) ===== */
    .btn-download-doc {
        background: linear-gradient(135deg, var(--primary-green) 0%, #17a98b 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s;
    }

    .btn-download-doc:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    /* ===== MODAL BUTTONS ===== */
    .btn-cancel-modal {
        background: var(--primary-dark);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .btn-cancel-modal:hover {
        background: #374151;
        color: white;
    }

    .btn-confirm-decline {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .btn-confirm-decline:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71d2a 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    /* ===== PAGINATION ===== */
    .pending-pagination .pagination {
        margin-bottom: 0;
    }

    .pending-pagination .page-link {
        border-radius: 8px !important;
        margin: 0 2px;
        border: 1px solid #e2e8f0;
        color: var(--primary-dark);
        transition: all 0.2s;
    }

    .pending-pagination .page-link:hover {
        background: rgba(29, 211, 176, 0.1);
        border-color: var(--primary-green);
        color: var(--primary-green);
    }

    .pending-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-green) 0%, #17a98b 100%);
        border-color: var(--primary-green);
        color: white;
    }

    /* ===== BUTTON STATES ===== */
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
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

    /* ===== SMOOTH TRANSITIONS ===== */
    .btn,
    .form-control,
    .form-select {
        transition: all 0.2s ease-in-out;
    }

    .fw-semibold {
        font-weight: 600;
    }

    /* ===== RESPONSIVE: TABLET (991px) ===== */
    @media (max-width: 991px) {
        .page-header-pending {
            padding: 1rem 1.25rem;
        }

        .page-icon-square {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .action-column .btn-action {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.7rem !important;
        }
    }

    /* ===== RESPONSIVE: MOBILE (767px) ===== */
    @media (max-width: 767px) {
        .page-header-pending .row {
            text-align: center;
        }

        .page-header-pending .col-12.col-md-6:first-child .d-flex {
            justify-content: center;
        }

        .page-header-pending .text-md-end {
            text-align: center !important;
        }

        .pending-card-header {
            flex-direction: column;
            align-items: stretch;
        }

        .pending-card-header .d-flex:first-child {
            justify-content: center;
        }

        #tableControls {
            flex-direction: column;
        }

        #tableControls .d-flex.align-items-center,
        #tableControls .ms-auto {
            width: 100%;
            margin-left: 0 !important;
        }

        .search-input-group {
            width: 100% !important;
            max-width: 100%;
            flex: 1 1 100%;
        }

        .filter-select,
        .sort-select {
            flex: 1;
            max-width: none;
            min-width: 0;
        }

        .table-pending {
            min-width: 800px;
            font-size: 0.78rem;
        }

        .table-pending thead th,
        .table-pending tbody td {
            padding: 0.35rem 0.5rem;
        }

        .btn-group-actions {
            flex-wrap: wrap;
            gap: 0.2rem;
        }

        .btn-action {
            font-size: 0.65rem !important;
            padding: 0.2rem 0.4rem !important;
        }
    }

    /* ===== RESPONSIVE: SMALL MOBILE (575px) ===== */
    @media (max-width: 575px) {
        .page-header-pending {
            padding: 0.75rem 1rem;
            border-radius: 12px;
        }

        .page-header-pending .row > .col-12:first-child {
            margin-bottom: 0.75rem !important;
        }

        .page-icon-square {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
            border-radius: 10px;
        }

        .page-title {
            font-size: 1.1rem;
        }

        .total-count-pill {
            font-size: 0.85rem;
            padding: 0.35rem 1rem;
        }

        .pending-card {
            border-radius: 12px !important;
        }

        .pending-card-header {
            padding: 0.6rem 0.75rem;
        }

        .header-icon-square {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .pending-card-header h5 {
            font-size: 0.9rem;
        }

        #tableControls .ms-auto.d-flex {
            flex-wrap: wrap;
        }

        .table-pending {
            min-width: 700px;
            font-size: 0.75rem;
        }
    }

    /* ===== RESPONSIVE: EXTRA SMALL (400px) ===== */
    @media (max-width: 400px) {
        .page-header-pending {
            padding: 0.6rem 0.75rem;
        }

        .page-icon-square {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .page-title {
            font-size: 1rem;
        }

        .total-count-pill {
            font-size: 0.8rem;
            padding: 0.3rem 0.75rem;
        }

        .breadcrumb {
            font-size: 0.75rem !important;
        }

        .pending-card-header h5 {
            font-size: 0.85rem;
        }
    }
</style>

@endsection
