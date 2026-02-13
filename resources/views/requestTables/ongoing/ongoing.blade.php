@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('layout.partials.message')

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-ongoing">
        <div>
            <h1><i class="fas fa-cogs me-2"></i>Processing Requests</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Processing Requests</li>
            </ol>
        </div>
        <div class="total-counter">
            Total: <span>{{ $totalCount }}</span>
        </div>
    </div>

    <x-tabs page='Processing' :filteredCount="$filteredCount" :searchCounts="$searchCounts" />

    <!-- Main Card -->
    <div class="ongoing-card">
        <!-- Card Header with Search/Filter Controls -->
        <div class="ongoing-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-tasks"></i></span>
                <h5>Processing Queue</h5>
            </div>
            <div class="header-controls" id="tableControls">
                <div class="input-group search-input-group">
                    <input type="text"
                        name="search"
                        id="searchInput"
                        class="form-control form-control-sm"
                        placeholder="Search requests..."
                        value="{{ request('search') }}"
                        autocomplete="off">
                    <button class="btn btn-outline-light btn-sm"
                        type="button"
                        id="clearSearch"
                        title="Clear search"
                        style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select name="filter" id="filterSelect" class="form-select form-select-sm filter-select">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document</option>
                    <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School</option>
                    <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Req No.</option>
                </select>
                <select name="sort" id="sortSelect" class="form-select form-select-sm sort-select">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Sort</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A-Z</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z-A</option>
                </select>
                <button type="button" class="btn-reset" id="resetBtn">
                    <i class="fas fa-redo me-1"></i>
                </button>
            </div>
        </div>

        <!-- Card Body -->
        <div class="ongoing-card-body">
            <!-- Search Info Banner -->
            @if(!empty(request('search')) || (request('filter') && request('filter') !== 'all') || (request('sort') && request('sort') !== 'default'))
            <div class="alert alert-info mb-3 py-2 table-info-banner">
                <small>
                    <i class="fas fa-search me-1"></i>
                    @if(request('search'))
                    Showing results for: <strong>"{{ request('search') }}"</strong>
                    @endif
                    @if(request('filter') && request('filter') !== 'all')
                    in <strong>{{ ucfirst(request('filter')) }}</strong>
                    @endif
                    @if(request('sort') && request('sort') !== 'default')
                    - Sorted by <strong>Request No. ({{ request('sort') === 'asc' ? 'A-Z' : 'Z-A' }})</strong>
                    @endif
                    <a href="{{ route('tables.index') }}" class="btn btn-sm btn-clear-all ms-2" id="clearAllBtn">Clear All</a>
                </small>
            </div>
            @endif

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center my-4" style="display: none;">
                <div class="spinner-border" style="color: var(--primary-green);" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-responsive" id="tableContainer">
                @if ($DocRequests->isEmpty())
                <div class="alert alert-warning text-center my-3" style="border-radius: 12px; border: none;">
                    @if (request('search'))
                    <i class="fas fa-search me-2"></i>No processing document requests found matching your search criteria.
                    @else
                    <i class="fas fa-inbox me-2"></i>No processing document requests found.
                    @endif
                </div>
                @else
                <table class="table table-hover table-ongoing" id="requestsTable">
                    <thead>
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
                            <th>Days Processing</th>
                            <th>Status</th>
                            <th>Req Date</th>
                            <th>App Date</th>
                            <th class="action-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($DocRequests as $item)
                        <tr>
                            <td><strong>{{ $item->req_no }}</strong></td>
                            <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                            <td>{{ $item->documents->DocType }}</td>
                            <td>{{ strtoupper($item->request_schl_entity) }}</td>
                            <td>
                                @php
                                    $approveDate = \Carbon\Carbon::parse($item->approve_date);
                                    $daysProcessing = floor($approveDate->diffInDays(\Carbon\Carbon::now()));
                                @endphp
                                <span class="badge days-badge
                                    @if($daysProcessing >= 7) days-danger
                                    @elseif($daysProcessing >= 3) days-warning
                                    @else days-ok
                                    @endif">
                                    {{ $daysProcessing }} {{ $daysProcessing == 1 ? 'day' : 'days' }}
                                </span>
                            </td>
                            <td><span class="badge status-badge status-processing">{{ $item->status }}</span></td>
                            <td>{{ $item->request_date }}</td>
                            <td>{{ $item->approve_date }}</td>
                            <td class="action-column">
                                <div class="action-btn-group" role="group">
                                    @if (!empty($approveOngoing))
                                    <form action="{{ route('document-request2.complete', $item->id) }}"
                                        method="POST" class="d-inline complete-form" data-swal-loading="true"
                                        data-swal-title="Completing Document Request"
                                        data-swal-text="This may take a few seconds...">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-action btn-action-complete complete-btn">
                                            <i class="fas fa-check me-1"></i>Complete
                                        </button>
                                    </form>

                                    @if ($item->documents->DocType == 'Good Moral')
                                    <form action="{{ route('doc.print', $item->id) }}" method="POST"
                                        class="d-inline print-form">
                                        @csrf
                                        <button type="submit" class="btn btn-action btn-action-print print-btn">
                                            <i class="fas fa-print me-1"></i>Print
                                        </button>
                                    </form>
                                    @endif
                                    @endif

                                    @if (!empty($PermissionEdit))
                                    <a href="{{ route('ongoing.edit', $item->id) }}"
                                        class="btn btn-action btn-action-edit">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    @endif

                                    @if (!empty($approveOngoing))
                                    <button type="button" class="btn btn-action btn-action-revert revert-btn"
                                        data-request-id="{{ $item->id }}"
                                        data-request-no="{{ $item->req_no }}"
                                        data-student-name="{{ optional($item->studentInformation)->full_name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#revertModal">
                                        <i class="fas fa-undo me-1"></i>Revert
                                    </button>
                                    @endif

                                    <!-- @if (!empty($approveOngoing))
                                    <button type="button" class="btn btn-action btn-action-delete delete-btn"
                                        data-id="{{ $item->id }}"
                                        data-reqno="{{ $item->req_no }}">
                                        <i class="fas fa-trash me-1"></i>Delete
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

            <!-- Pagination -->
            <div id="paginationContainer">
                @if (!$DocRequests->isEmpty())
                <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                    {{ $DocRequests->appends(request()->query())->links() }}
                    <small class="text-muted mt-1">
                        Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of
                        {{ $DocRequests->total() }}
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Remarks Modals --}}
@foreach ($DocRequests as $item)
@if($item->remarks && strlen($item->remarks) > 50)
<div class="modal fade" id="remarksModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background:#1e293b; color:#f1f5f9; border:1px solid #334155; border-radius:1rem;">
            <div class="modal-header" style="background:#0f172a; border-bottom:1px solid #334155;">
                <h5 class="modal-title" style="color:#1dd3b0;">
                    <i class="bi bi-chat-left-dots me-2"></i>Full Remarks
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info mb-3" style="background:#334155; border:1px solid #475569; color:#e2e8f0;">
                    <strong>Request #{{ $item->req_no }}</strong>
                </div>
                <div class="p-3 rounded" style="background:#0f172a; border:1px solid #334155; word-wrap: break-word; white-space: pre-line;">{{ $item->remarks }}</div>
            </div>
            <div class="modal-footer" style="background:#0f172a; border-top:1px solid #334155;">
                <button type="button" class="btn btn-sm"
                    style="background:#1dd3b0; color:#0f172a;"
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

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

{{-- Revert Modal --}}
<div class="modal fade" id="revertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title" style="color: #1dd3b0;">
                    <i class="fas fa-undo me-2"></i>Revert Document to Pending
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="revertForm" action="{{ route('ongoing.revert', '') }}" method="POST"
                data-swal-loading="true" data-swal-title="Reverting Request to Pending"
                data-swal-text="This may take a few seconds...">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="alert" style="background:#334155; border:1px solid #475569; color:#e2e8f0; border-radius: 10px;">
                            <strong>Request No:</strong> <span id="modalRevertRequestNo"></span><br>
                            <strong>Student:</strong> <span id="modalRevertStudentName"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="revertReason" class="form-label" style="color: #e2e8f0;">
                            <i class="fas fa-comment me-1"></i>Reason for Revert <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="revertReason" name="revert_reason" rows="3" required
                            placeholder="Please provide a reason for reverting this document to Pending status..."></textarea>
                        <div class="invalid-feedback">
                            Please provide a reason for reverting this document.
                        </div>
                    </div>

                    <div class="alert" style="background: rgba(29, 211, 176, 0.1); border: 1px solid rgba(29, 211, 176, 0.3); color: #a7f3d0; border-radius: 10px;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This action will change the document status back to "Pending" and clear the approved date.
                    </div>
                </div>
                <div class="modal-footer modal-footer-styled">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-modal-confirm-revert" id="submitRevertBtn">
                        <i class="fas fa-undo me-1"></i>Revert to Pending
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---- CHECK FOR SESSION MESSAGES (after page reload) ----
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            showConfirmButton: true
        });
    @endif

    // ---- ELEMENTS ----
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filterSelect = document.getElementById('filterSelect');
    const sortSelect = document.getElementById('sortSelect');
    const resetBtn = document.getElementById('resetBtn');
    const tableContainer = document.getElementById('tableContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');

    let searchTimeout = null;
    let isInitialLoad = true;

    // ---- INITIAL STATE ----
    toggleClearButton();

    // If there's a search parameter in URL, trigger search immediately
    const urlParams = new URLSearchParams(window.location.search);
    const urlSearch = urlParams.get('search');
    if (urlSearch && urlSearch.trim()) {
        searchInput.value = urlSearch;
        toggleClearButton();
        performAjaxSearch();
    }

    // Mark initial load as complete after a short delay
    setTimeout(() => {
        isInitialLoad = false;
    }, 500);

    // ---- FUNCTIONS ----
    function toggleClearButton() {
        clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
    }

    // AJAX search/filter/sort refresh
    function performAjaxSearch() {
        const search = searchInput.value.trim();
        const filter = filterSelect.value;
        const sort = sortSelect.value;

        loadingSpinner.style.display = 'block';
        tableContainer.style.opacity = '0.5';

        const url = `{{ route('ongoing.index') }}?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableContainer = doc.querySelector('#tableContainer');
                const newInfoBanner = doc.querySelector('.table-info-banner');
                const newPaginationWrapper = doc.querySelector('#paginationContainer');
                const newSearchCounter = doc.querySelector('#searchResultsCounter');

                // Update table
                tableContainer.innerHTML = newTableContainer ?
                    newTableContainer.innerHTML :
                    '<div class="alert alert-warning text-center my-3">No results found.</div>';

                // Update info banner
                const oldInfoBanner = document.querySelector('.table-info-banner');
                if (oldInfoBanner) oldInfoBanner.remove();

                if (newInfoBanner) {
                    const cardBody = document.querySelector('.ongoing-card-body');
                    cardBody.insertBefore(newInfoBanner, cardBody.firstChild);
                }

                // Update pagination
                const paginationContainer = document.querySelector('#paginationContainer');
                if (paginationContainer && newPaginationWrapper) {
                    paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                } else if (paginationContainer && !newPaginationWrapper) {
                    paginationContainer.innerHTML = '';
                }

                // Update search counter
                const searchResultsCounter = document.getElementById('searchResultsCounter');
                if (search || (filter && filter !== 'all') || (sort && sort !== 'default')) {
                    if (newSearchCounter) {
                        searchResultsCounter.innerHTML = newSearchCounter.innerHTML;
                        searchResultsCounter.style.display = 'block';
                    }
                } else {
                    searchResultsCounter.style.display = 'none';
                }

                loadingSpinner.style.display = 'none';
                tableContainer.style.opacity = '1';

                attachClearAllAjax();
                reattachActionButtons();
            })
            .catch(err => {
                console.error('AJAX Search Error:', err);
                loadingSpinner.style.display = 'none';
                tableContainer.style.opacity = '1';
            });
    }

    // ---- EVENT LISTENERS ----

    // Debounced live search
    searchInput.addEventListener('input', function() {
        toggleClearButton();
        clearTimeout(searchTimeout);
        // Don't trigger search during initial load sync
        if (!isInitialLoad) {
            searchTimeout = setTimeout(() => performAjaxSearch(), 400);
        }
    });

    // Clear search input
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        toggleClearButton();
        performAjaxSearch();
    });

    // Filter & sort trigger AJAX
    [filterSelect, sortSelect].forEach(el => el?.addEventListener('change', performAjaxSearch));

    // Reset button click
    resetBtn?.addEventListener('click', function() {
        searchInput.value = '';
        filterSelect.value = 'all';
        sortSelect.value = 'default';
        toggleClearButton();
        performAjaxSearch();
    });

    // AJAX Pagination Handler
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const url = paginationLink.href;

            loadingSpinner.style.display = 'block';
            tableContainer.style.opacity = '0.5';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableContainer = doc.querySelector('#tableContainer');
                    const newPaginationWrapper = doc.querySelector('#paginationContainer');

                    // Update table
                    tableContainer.innerHTML = newTableContainer ?
                        newTableContainer.innerHTML :
                        '<div class="alert alert-warning text-center my-3">No results found.</div>';

                    // Update pagination
                    const paginationContainer = document.querySelector('#paginationContainer');
                    if (paginationContainer && newPaginationWrapper) {
                        paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                    }

                    loadingSpinner.style.display = 'none';
                    tableContainer.style.opacity = '1';

                    // Scroll to top of card
                    document.querySelector('.ongoing-card').scrollIntoView({ behavior: 'smooth', block: 'start' });

                    attachClearAllAjax();
                    reattachActionButtons();
                })
                .catch(err => {
                    console.error('AJAX Pagination Error:', err);
                    loadingSpinner.style.display = 'none';
                    tableContainer.style.opacity = '1';
                });
        }
    });

    // "Clear All" button inside alert-info (AJAX)
    function attachClearAllAjax() {
        const clearAllBtn = document.getElementById('clearAllBtn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchInput.value = '';
                filterSelect.value = 'all';
                sortSelect.value = 'default';
                toggleClearButton();
                performAjaxSearch();
            });
        }
    }
    attachClearAllAjax();

    // ---- ACTION BUTTON LOGIC ----
    function reattachActionButtons() {
        // Revert button
        document.querySelectorAll('.revert-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const requestId = this.dataset.requestId;
                const requestNo = this.dataset.requestNo;
                const studentName = this.dataset.studentName;

                document.getElementById('modalRevertRequestNo').textContent = requestNo;
                document.getElementById('modalRevertStudentName').textContent = studentName;
                document.getElementById('revertForm').action = `{{ url('ongoing/revert') }}/${requestId}`;
            });
        });

        // Complete button
        document.querySelectorAll('.complete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('.complete-btn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
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

        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const reqNo = this.dataset.reqno;

                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    html: `You are about to <strong>permanently delete</strong> Request No. <strong>${reqNo}</strong>.<br><br>This action cannot be undone!`,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#1f2937',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) {
                        submitDeleteForm(id);
                    }
                });
            });
        });

        // Print button
        document.querySelectorAll('.print-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('.print-btn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Printing...';
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
    }

    // Initial attachment of action buttons
    reattachActionButtons();

    // Delete Workflow
    function submitDeleteForm(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('ongoing') }}/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;

        Swal.fire({
            title: 'Deleting...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        document.body.appendChild(form);
        form.submit();
    }

    // ---- REVERT FORM SUBMISSION ----
    const revertForm = document.getElementById('revertForm');
    const revertModal = document.getElementById('revertModal');
    const submitRevertBtn = document.getElementById('submitRevertBtn');
    const revertReason = document.getElementById('revertReason');

    revertForm?.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!revertReason.value.trim()) {
            revertReason.classList.add('is-invalid');
            return;
        }

        revertReason.classList.remove('is-invalid');
        setRevertLoadingState(true);

        // Create FormData and explicitly add the revert_reason
        const formData = new FormData(revertForm);

        // Force add the revert_reason value (in case FormData doesn't capture it)
        if (!formData.has('revert_reason')) {
            formData.append('revert_reason', revertReason.value.trim());
        }

        const actionUrl = revertForm.action;

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Reverted Successfully',
                    text: data.message || 'Document has been reverted to Pending status.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to revert document');
            }
        })
        .catch(error => {
            setRevertLoadingState(false);
            showRevertError(error.message || 'An error occurred while reverting the document.');
        });
    });

    function setRevertLoadingState(isLoading) {
        submitRevertBtn.disabled = isLoading;
        submitRevertBtn.innerHTML = isLoading
            ? '<span class="spinner-border spinner-border-sm me-1"></span>Reverting...'
            : '<i class="fas fa-undo me-1"></i>Revert to Pending';
        revertReason.disabled = isLoading;
    }

    function showRevertError(message) {
        const existingAlert = revertForm.querySelector('.alert-danger');
        if (existingAlert) existingAlert.remove();

        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        revertForm.querySelector('.modal-body').insertBefore(alertDiv, revertForm.querySelector('.modal-body').firstChild);
    }

    revertModal?.addEventListener('hidden.bs.modal', function() {
        revertReason.value = '';
        revertReason.classList.remove('is-invalid');
        revertForm.querySelectorAll('.alert-danger, .alert-success').forEach(a => a.remove());
    });

    // Auto-resize textarea
    revertReason?.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Keyboard shortcut: Ctrl+F focuses search
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    // Re-enable buttons when returning to page (browser back)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.querySelectorAll('.complete-btn, .delete-btn, .print-btn').forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
                if (btn.classList.contains('complete-btn')) {
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>Complete';
                } else if (btn.classList.contains('delete-btn')) {
                    btn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
                } else if (btn.classList.contains('print-btn')) {
                    btn.innerHTML = '<i class="fas fa-print me-1"></i>Print';
                }
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
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* ===== PAGE HEADER ===== */
    .page-header-ongoing {
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

    .page-header-ongoing h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-ongoing .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-ongoing .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-ongoing .breadcrumb-item.active {
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

    /* ===== MAIN CARD ===== */
    .ongoing-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .ongoing-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    /* ===== CARD HEADER ===== */
    .ongoing-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .ongoing-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ongoing-card-header .header-icon {
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

    .ongoing-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .header-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }

    .header-controls .search-input-group {
        width: 200px;
        min-width: 120px;
    }

    .header-controls .search-input-group .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 0.85rem;
    }

    .header-controls .search-input-group .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .header-controls .search-input-group .form-control:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        color: white;
    }

    .header-controls .filter-select,
    .header-controls .sort-select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 0.85rem;
        min-width: 90px;
        max-width: 120px;
    }

    .header-controls .filter-select option,
    .header-controls .sort-select option {
        background: var(--primary-dark);
        color: white;
    }

    .header-controls .filter-select:focus,
    .header-controls .sort-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    .btn-reset {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 0.35rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: white;
        transition: all 0.2s;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-reset:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* ===== CARD BODY ===== */
    .ongoing-card-body {
        padding: 1.5rem;
    }

    /* ===== TABLE STYLES ===== */
    .table-ongoing {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .table-ongoing thead th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 0.75rem;
        border: none;
        white-space: nowrap;
        vertical-align: middle;
    }

    .table-ongoing tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-ongoing tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }

    .table-ongoing tbody td {
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .sortable-header a {
        transition: opacity 0.2s;
    }

    .sortable-header a:hover {
        opacity: 0.8;
    }

    /* ===== DAYS BADGES ===== */
    .days-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
        font-weight: 600;
        white-space: nowrap;
    }

    .days-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .days-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #1f2937;
    }

    .days-ok {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
    }

    /* ===== STATUS BADGE ===== */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.7rem;
        white-space: nowrap;
        border-radius: 8px;
        font-weight: 600;
    }

    .status-processing {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #1f2937;
    }

    /* ===== ACTION COLUMN & BUTTONS ===== */
    .action-column {
        min-width: 280px;
        white-space: nowrap;
    }

    .action-btn-group {
        display: inline-flex;
        flex-wrap: nowrap;
        gap: 0.35rem;
        align-items: center;
    }

    .btn-action {
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        transition: all 0.2s;
        border: none;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-action:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .btn-action i {
        font-size: 0.7rem;
    }

    .btn-action-complete {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
    }

    .btn-action-complete:hover {
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .btn-action-print {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }

    .btn-action-print:hover {
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4);
        color: white;
    }

    .btn-action-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        text-decoration: none;
    }

    .btn-action-edit:hover {
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-action-revert {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .btn-action-revert:hover {
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        color: white;
    }

    .btn-action-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .btn-action-delete:hover {
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        color: white;
    }

    /* ===== FORM INLINE ===== */
    .action-btn-group .complete-form,
    .action-btn-group .print-form {
        display: inline;
        margin: 0;
    }

    /* ===== CLEAR ALL BUTTON ===== */
    .btn-clear-all {
        background: rgba(29, 211, 176, 0.15);
        border: 1px solid rgba(29, 211, 176, 0.3);
        color: #1dd3b0;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-clear-all:hover {
        background: var(--primary-green);
        color: white;
    }

    /* ===== BUTTON STATES ===== */
    .btn:disabled, .btn-action:disabled {
        cursor: not-allowed;
        opacity: 0.5;
        transform: none !important;
        box-shadow: none !important;
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
    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
    }

    .alert-info {
        background-color: #ecfdf5;
        border: 1px solid rgba(29, 211, 176, 0.3);
        color: #065f46;
    }

    /* ===== MODAL STYLES ===== */
    .modal-styled {
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid #334155;
        border-radius: 1rem;
    }

    .modal-header-styled {
        background: #0f172a;
        border-bottom: 1px solid #334155;
    }

    .modal-footer-styled {
        background: #0f172a;
        border-top: 1px solid #334155;
    }

    .btn-modal-cancel {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 8px;
        padding: 0.4rem 1rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-modal-cancel:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .btn-modal-confirm-revert {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
        color: #1f2937;
        border-radius: 8px;
        padding: 0.4rem 1rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-modal-confirm-revert:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        color: #1f2937;
    }

    .modal-styled .form-control {
        background: #0f172a;
        border: 1px solid #334155;
        color: #f1f5f9;
        border-radius: 10px;
    }

    .modal-styled .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    .modal-dialog {
        max-width: 600px;
    }

    /* ===== VIEW BUTTONS ===== */
    .view-remarks-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .view-remarks-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        color: white;
    }

    /* ===== RESPONSIVE TABLE ===== */
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    /* ===== REQ NUMBER COLUMN ===== */
    .table-ongoing tbody td:first-child,
    .table-ongoing thead th:first-child {
        min-width: 120px;
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== DATE COLUMNS ===== */
    .table-ongoing tbody td:nth-child(7),
    .table-ongoing thead th:nth-child(7),
    .table-ongoing tbody td:nth-child(8),
    .table-ongoing thead th:nth-child(8) {
        min-width: 105px;
        white-space: nowrap;
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

    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 991px) {
        .header-controls {
            width: 100%;
            justify-content: flex-start;
        }

        .header-controls .search-input-group {
            width: 200px;
            flex: 1 1 auto;
        }
    }

    @media (max-width: 767px) {
        .page-header-ongoing {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-ongoing h1 {
            font-size: 1.35rem;
        }

        .total-counter {
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
        }

        .total-counter span {
            font-size: 1.1rem;
        }

        .ongoing-card {
            border-radius: 12px;
        }

        .ongoing-card-header {
            padding: 0.875rem 1rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .header-controls {
            width: 100%;
            flex-wrap: wrap;
        }

        .header-controls .search-input-group {
            width: 100%;
            flex: 1 1 100%;
        }

        .header-controls .filter-select,
        .header-controls .sort-select {
            flex: 1;
            min-width: 0;
        }

        .ongoing-card-body {
            padding: 1rem;
        }

        .table-ongoing {
            font-size: 0.8rem;
        }

        .table-ongoing thead th {
            font-size: 0.725rem;
            padding: 0.6rem 0.5rem;
        }

        .table-ongoing tbody td {
            padding: 0.5rem 0.5rem;
        }

        .action-column {
            min-width: 200px;
        }

        .action-btn-group {
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .btn-action {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }
    }

    @media (max-width: 575px) {
        .page-header-ongoing h1 {
            font-size: 1.15rem;
        }

        .ongoing-card-header h5 {
            font-size: 0.875rem;
        }

        .table-ongoing {
            font-size: 0.75rem;
        }

        .table-ongoing thead th {
            font-size: 0.675rem;
            padding: 0.5rem 0.4rem;
        }

        .table-ongoing tbody td {
            padding: 0.4rem 0.35rem;
        }

        .action-column {
            min-width: 170px;
        }

        .btn-action {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }

        .days-badge,
        .status-badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
    }
</style>

@endsection
