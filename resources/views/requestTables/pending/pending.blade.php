@extends('layout.blankpage')
@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row align-items-center">
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <h1 class="mt-4">
            <span class="badge page-title-badge">Pending Requests</span>
        </h1>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <h1 class="mt-md-4">
            <span class="badge count-badge">Total: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

<x-tabs page='Pending' :filteredCount="$filteredCount" :searchCounts="$searchCounts" />

{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header card-header-custom">
        {{-- Search/Filter Form --}}
        <div class="d-flex w-100 gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
            {{-- Search Input (left) --}}
            <div class="d-flex align-items-center" style="min-width:0;">
                <div class="input-group search-input-group" style="width: 300px;">
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
            </div>

            {{-- Filters (right) - pushed to the end with ms-auto so it stays right-aligned on wide screens --}}
            <div class="ms-auto d-flex align-items-center gap-2">
                {{-- Filter Dropdown --}}
                <select name="filter" id="filterSelect" class="form-select form-select-sm filter-select">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document</option>
                    <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School</option>
                    <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Req No.</option>
                </select>

                {{-- Sort Dropdown --}}
                <select name="sort" id="sortSelect" class="form-select form-select-sm sort-select">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Sort</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A-Z</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z-A</option>
                </select>

                {{-- Reset Button --}}
                <button type="button" class="btn btn-light btn-sm" id="resetBtn">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>

        </div>
    </div>

    {{-- Card Body --}}
    <div class="card-body bg-light">
        {{-- Search Info Banner --}}
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
                <a href="{{ route('tables.index') }}" class="btn btn-sm btn-outline-info ms-2" id="clearAllBtn">Clear All</a>
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
                No pending document requests found matching your search criteria.
                @else
                No pending document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                <thead class="table-dark">
                    <tr>
                        <th class="sortable-header">
                            <a href="{{ route('pending.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th>Requestor</th>
                        <th>Student</th>
                        <th>Doc</th>
                        <th>School</th>
                        <th>Days Pending</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Req Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($DocRequests as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->req_no }}</td>
                        <td>
                            @if($item->relationship)
                            {{ strtoupper($item->relationship) }}
                            @else
                            <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ strtoupper($item->request_schl_entity) }}</td>
                        <td>
                            @php
                            $requestDate = \Carbon\Carbon::parse($item->request_date);
                            $daysPending = floor($requestDate->diffInDays(\Carbon\Carbon::now()));
                            @endphp
                            <span class="badge 
                                @if($daysPending >= 7) bg-danger
                                @elseif($daysPending >= 3) bg-warning text-dark
                                @else bg-success
                                @endif">
                                {{ $daysPending }} {{ $daysPending == 1 ? 'day' : 'days' }}
                            </span>
                        </td>
                        <td>
                            @if($item->reason)
                            @if(strlen($item->reason) > 50)
                            <button class="btn btn-sm btn-info view-reason-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#reasonViewModal{{ $item->id }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                            @else
                            {{ $item->reason }}
                            @endif
                            @else
                            <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary text-white status-badge">{{ $item->status }}</span></td>
                        <td>{{ $item->request_date }}</td>
                        <td class="action-column">
                            <div class="btn-group-vertical btn-group-sm d-md-inline" role="group">
                                @if (!empty($approvePending))
                                <button type="button" class="btn btn-sm btn-danger decline-btn mb-1"
                                    data-id="{{ $item->id }}">
                                    <i class="fas fa-times me-1"></i>Decline
                                </button>
                                <form action="{{ route('document-request.complete', $item->id) }}"
                                    method="POST" class="d-inline accept-form" data-swal-loading="true"
                                    data-swal-title="Accepting Document Request"
                                    data-swal-text="This may take a few seconds...">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success accept-btn mb-1">
                                        <i class="fas fa-check me-1"></i>Accept
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-danger mb-1 delete-btn"
                                    data-id="{{ $item->id }}"
                                    data-reqno="{{ $item->req_no }}">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                                @endif

                                @if (!empty($PermissionEdit))
                                <a href="{{ route('pending.edit', $item->id) }}"
                                    class="btn btn-sm btn-warning mb-1">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @endif

                                @if ($item->supporting_document)
                                <button type="button" class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal"
                                    data-bs-target="#documentModal{{ $item->id }}">
                                    <i class="fas fa-file-alt me-1"></i>View Doc
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
                <textarea class="form-control" id="reasonInput" rows="3" placeholder="Enter reason for declining..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn text-white" style="background-color: #1f2937;"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeclineBtn">Confirm Decline</button>
            </div>
        </div>
    </div>
</div>

{{-- Remarks & Reason View Modals --}}
@foreach ($DocRequests as $item)
{{-- Remarks Modal (only if remarks > 50 chars) --}}
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

{{-- Reason View Modal (only if reason > 50 chars) --}}
@if($item->reason && strlen($item->reason) > 50)
<div class="modal fade" id="reasonViewModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background:#1e293b; color:#f1f5f9; border:1px solid #334155; border-radius:1rem;">
            <div class="modal-header" style="background:#0f172a; border-bottom:1px solid #334155;">
                <h5 class="modal-title" style="color:#1dd3b0;">
                    <i class="bi bi-journal-text me-2"></i>Request Reason
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info mb-3" style="background:#334155; border:1px solid #475569; color:#e2e8f0;">
                    <strong>Request #{{ $item->req_no }}</strong>
                </div>
                <div class="p-3 rounded" style="background:#0f172a; border:1px solid #334155; word-wrap: break-word; white-space: pre-line;">{{ $item->reason }}</div>
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

{{-- Supporting Document Modal --}}
@if ($item->supporting_document)
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

                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                <img src="/public/{{ trim($documentPath) }}" target="_blank" alt="Supporting Document"
                    class="img-fluid w-100" style="max-height: 70vh; object-fit: contain;">
                @elseif($fileExtension === 'pdf')
                <div class="p-4">
                    <iframe src="/public/{{ trim($documentPath) }}" width="100%" height="400px"
                        style="border: 1px solid #ddd;"></iframe>
                </div>
                @else
                <div class="p-5 text-center">
                    <i class="fas fa-file text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">{{ strtoupper($fileExtension) }} Document</h5>
                    <p class="text-muted">{{ basename($item->supporting_document) }}</p>
                    <a href="/public/{{ trim($documentPath) }}" class="btn btn-primary" download>
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
                <a href="/public/{{ trim($documentPath) }}" target="_blank"
                    class="btn btn-outline-primary btn-sm">
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
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const filterSelect = document.getElementById('filterSelect');
        const sortSelect = document.getElementById('sortSelect');
        const resetBtn = document.getElementById('resetBtn');
        const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
        const reasonInput = document.getElementById('reasonInput');
        const confirmDeclineBtn = document.getElementById('confirmDeclineBtn');
        const tableContainer = document.getElementById('tableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');

        let pendingDeclineId = null;
        let searchTimeout = null;
        let isInitialLoad = true;

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

        // ✅ Show/Hide clear button dynamically
        function toggleClearButton() {
            clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
        }

        // ✅ Debounced AJAX search
        searchInput.addEventListener('input', function() {
            toggleClearButton();
            clearTimeout(searchTimeout);
            // Don't trigger search during initial load sync
            if (!isInitialLoad) {
                searchTimeout = setTimeout(() => performAjaxSearch(), 400);
            }
        });

        // ✅ Clear search
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearButton();
            performAjaxSearch();
        });

        // ✅ Trigger AJAX when filter/sort changes
        [filterSelect, sortSelect].forEach(el => el?.addEventListener('change', performAjaxSearch));

        // ✅ Reset Button logic
        resetBtn?.addEventListener('click', function() {
            searchInput.value = '';
            filterSelect.value = 'all';
            sortSelect.value = 'default';
            toggleClearButton();
            performAjaxSearch();
        });

        // ✅ AJAX Search + Sort + Filter Refresh - FIXED
        function performAjaxSearch() {
            const search = searchInput.value.trim();
            const filter = filterSelect.value;
            const sort = sortSelect.value;

            loadingSpinner.style.display = 'block';
            tableContainer.style.opacity = '0.5';

            const url = `{{ route('pending.index') }}?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

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

                    // Replace table content
                    tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                    // Replace info banner
                    const oldInfoBanner = document.querySelector('.table-info-banner');
                    if (oldInfoBanner) oldInfoBanner.remove();

                    if (newInfoBanner) {
                        const cardBody = document.querySelector('.card-body.bg-light');
                        cardBody.insertBefore(newInfoBanner, cardBody.firstChild);
                    }

                    // Replace pagination - FIXED
                    const paginationContainer = document.querySelector('#paginationContainer');
                    if (paginationContainer && newPaginationWrapper) {
                        paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                    } else if (paginationContainer && !newPaginationWrapper) {
                        paginationContainer.innerHTML = ''; // Clear pagination if no results
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
                    reattachDeclineButtons();
                })
                .catch(err => {
                    console.error('AJAX Search Error:', err);
                    loadingSpinner.style.display = 'none';
                    tableContainer.style.opacity = '1';
                });
        }

        // ✅ AJAX Pagination Handler - FIXED
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

                        // Replace table content
                        tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                        // Replace pagination - FIXED
                        const paginationContainer = document.querySelector('#paginationContainer');
                        if (paginationContainer && newPaginationWrapper) {
                            paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                        }

                        loadingSpinner.style.display = 'none';
                        tableContainer.style.opacity = '1';

                        // Scroll to top of table
                        document.querySelector('.card').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        attachClearAllAjax();
                        reattachDeclineButtons();
                    })
                    .catch(err => {
                        console.error('AJAX Pagination Error:', err);
                        loadingSpinner.style.display = 'none';
                        tableContainer.style.opacity = '1';
                    });
            }
        });

        // ✅ Turn "Clear All" into AJAX
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

        // ✅ Reattach decline buttons after AJAX reload
        function reattachDeclineButtons() {
            document.querySelectorAll('.decline-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    pendingDeclineId = this.dataset.id;
                    reasonInput.value = '';
                    reasonModal.show();
                });
            });

            // Reattach delete buttons
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

            // Reattach accept form handlers
            document.querySelectorAll('.accept-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.accept-btn');

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
        }

        attachClearAllAjax(); // Initial bind
        reattachDeclineButtons(); // Initial bind

        // 🟥 Decline Workflow
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
            }).then(result => {
                if (result.isConfirmed) submitDeclineForm(pendingDeclineId, reason);
            });
        });

        function submitDeclineForm(id, reason) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('pending/decline') }}/${id}`;
            form.innerHTML = `@csrf @method('DELETE') <input type="hidden" name="remarks" value="${reason}">`;

            Swal.fire({
                title: 'Declining...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            document.body.appendChild(form);
            form.submit();
        }

        // 🗑️ Delete Workflow
        function submitDeleteForm(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('pending') }}/${id}`;
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

        // ✅ Keyboard shortcut: Ctrl+F focuses search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput?.focus();
            }
        });

        // ✅ Re-enable buttons when using browser back
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.querySelectorAll('.accept-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>Accept';
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

    /* ===== TABLE STYLES - COMPRESSED ROWS ===== */
    #requestsTable {
        font-size: 0.85rem;
        margin-bottom: 0;
    }

    #requestsTable thead th {
        white-space: nowrap;
        vertical-align: middle;
        font-weight: 600;
        padding: 0.4rem 0.5rem;
        font-size: 0.85rem;
        line-height: 1.3;
    }

    #requestsTable tbody td {
        vertical-align: middle;
        padding: 0.35rem 0.5rem;
        font-size: 0.85rem;
        line-height: 1.3;
    }

    .sortable-header a {
        transition: opacity 0.2s;
    }

    .sortable-header a:hover {
        opacity: 0.8;
    }

    /* ===== REQ NUMBER COLUMN ===== */
    #requestsTable tbody td:first-child,
    #requestsTable thead th:first-child {
        min-width: 120px !important;
        max-width: 120px !important;
        width: 120px !important;
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== REQ DATE COLUMN - ONE LINE ===== */
    #requestsTable tbody td:nth-child(9),
    #requestsTable thead th:nth-child(9) {
        min-width: 105px !important;
        max-width: 105px !important;
        width: 105px !important;
        white-space: nowrap !important;
    }

    /* ===== ACTION COLUMN - COMPRESSED ===== */
    .action-column {
        min-width: 310px !important;
        max-width: 310px !important;
        width: 310px !important;
        white-space: nowrap !important;
        padding: 0.25rem 0.3rem !important;
    }

    .btn-group-vertical {
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 0.3rem !important;
        align-items: center !important;
    }

    .action-column .btn {
        padding: 0.3rem 0.5rem !important;
        font-size: 0.75rem !important;
        width: auto !important;
        min-width: 70px !important;
        max-width: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        margin: 0 !important;
        white-space: nowrap !important;
        line-height: 1.2 !important;
    }

    .action-column .btn i {
        font-size: 0.7rem !important;
        margin-right: 0.2rem !important;
    }

    /* Make form inline */
    .action-column .accept-form {
        display: inline !important;
        margin: 0 !important;
    }

    /* Override Bootstrap mb-1 class */
    .action-column .mb-1 {
        margin-bottom: 0 !important;
    }

    /* Specific button width adjustments */
    .action-column .decline-btn {
        min-width: 68px !important;
    }

    .action-column .accept-btn {
        min-width: 68px !important;
    }

    .action-column .btn-warning {
        min-width: 58px !important;
    }

    .action-column .btn-primary {
        min-width: 80px !important;
    }

    /* ===== VIEW BUTTONS ===== */
    .view-remarks-btn,
    .view-reason-btn {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
        white-space: nowrap;
    }

    /* ===== STATUS BADGE ===== */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        white-space: nowrap;
    }

    /* ===== BUTTON STATES ===== */
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    .btn-sm {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
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

    .form-control:focus {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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
            padding: 0.3rem 0.25rem;
        }

        #requestsTable tbody td:first-child {
            min-width: 100px !important;
            max-width: 100px !important;
            width: 100px !important;
        }

        .btn-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }

        /* Stack buttons on mobile */
        .action-column {
            min-width: 180px !important;
            max-width: 180px !important;
            width: 180px !important;
        }

        .btn-group-vertical {
            flex-wrap: wrap !important;
        }

        .action-column .btn {
            min-width: 85px !important;
            font-size: 0.65rem !important;
            padding: 0.25rem 0.4rem !important;
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