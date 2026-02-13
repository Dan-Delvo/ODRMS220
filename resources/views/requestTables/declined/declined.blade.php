@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="container-fluid px-4 py-4">
<div class="page-header-declined">
    <div>
        <h1><i class="fas fa-times-circle me-2"></i>Declined Requests</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Declined Requests</li>
        </ol>
    </div>
    <div class="total-counter">
        Total: <span>{{ $DocRequests->total() }}</span>
    </div>
</div>

<x-tabs page='Declined' :filteredCount="$filteredCount" :searchCounts="$searchCounts" />

{{-- Main Card --}}
<div class="declined-card">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="declined-card-header">
        <div class="header-left">
            <span class="header-icon"><i class="fas fa-list-alt"></i></span>
            <h5>Request Queue</h5>
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

    {{-- Card Body --}}
    <div class="declined-card-body">
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
                <a href="{{ route('declined-documents.index') }}" class="btn btn-sm btn-clear-all ms-2" id="clearAllBtn">Clear All</a>
            </small>
        </div>
        @endif

        {{-- Loading Spinner --}}
        <div id="loadingSpinner" class="text-center my-4" style="display: none;">
            <div class="spinner-border" style="color: var(--primary-green);" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="table-responsive" id="tableContainer">
            @if ($DocRequests->isEmpty())
            <div class="alert alert-warning text-center my-3" style="border-radius: 12px; border: none;">
                @if (request('search'))
                <i class="fas fa-search me-2"></i>No declined document requests found matching your search criteria.
                @else
                <i class="fas fa-inbox me-2"></i>No declined document requests found.
                @endif
            </div>
            @else
            <table class="table table-hover table-declined" id="requestsTable">
                <thead>
                    <tr>
                        <th class="sortable-header">
                            <a href="{{ route('declined-documents.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th>Via</th>
                        <th>Rel Mode</th>
                        <th>Remarks</th>
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
                        <td>{{ strtoupper($item->studentInformation->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ strtoupper($item->request_schl_entity) }}</td>
                        <td>{{ $item->request_mode }}</td>
                        <td>{{ $item->release_mode }}</td>
                        <td>
                            @if($item->remarks)
                                @if(strlen($item->remarks) > 50)
                                    <button class="btn btn-sm btn-info view-remarks-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#remarksModal{{ $item->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                @else
                                    {{ $item->remarks }}
                                @endif
                            @else
                                <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td>
                            @if($item->decline_reason)
                                @if(strlen($item->decline_reason) > 50)
                                    <button class="btn btn-sm btn-outline-danger view-reason-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#reasonViewModal{{ $item->id }}">
                                        <i class="fas fa-eye me-1"></i>View Reason
                                    </button>
                                @else
                                    <span class="text-danger">{{ $item->decline_reason }}</span>
                                @endif
                            @else
                                <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td><span class="badge status-declined status-badge">{{ $item->status }}</span></td>
                        <td>{{ $item->request_date }}</td>
                        <td class="action-column">
                            <div class="action-btn-group" role="group">
                                <form action="{{ route('document-request.complete', $item->id) }}"
                                    method="POST" class="d-inline accept-form" data-swal-loading="true"
                                    data-swal-title="Reaccepting Declined Request"
                                    data-swal-text="This may take a few seconds...">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-action btn-action-accept accept-btn"
                                        data-original-text="Accept">
                                        <i class="fas fa-check me-1"></i>Accept
                                    </button>
                                </form>

                                @if ($item->supporting_document)
                                <button type="button" class="btn btn-action btn-action-viewdoc"
                                    data-bs-toggle="modal"
                                    data-bs-target="#documentModal{{ $item->id }}">
                                    <i class="fas fa-file-alt me-1"></i>View Doc
                                </button>
                                @endif

                                <form action="{{ route('pending.decline', $item->id) }}" method="POST"
                                    class="d-inline decline-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="remarks" class="decline-reason" value="">
                                    <input type="hidden" name="indicator" value="1">
                                    <button type="submit" class="btn btn-action btn-action-decline decline-btn">
                                        <i class="fas fa-times-circle me-1"></i>Decline
                                    </button>
                                </form>

                                <!-- <form action="{{ route('pending.destroy', $item->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form> -->
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
            <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                {{ $DocRequests->appends(request()->query())->links() }}
                <small class="text-muted">
                    Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
                </small>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Remarks & Reason Modals --}}
@foreach ($DocRequests as $item)
{{-- Remarks Modal --}}
@if($item->remarks && strlen($item->remarks) > 50)
<div class="modal fade" id="remarksModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="bi bi-chat-left-dots me-2"></i>
                    Full Remarks
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="alert alert-info m-3">
                    <strong>Request #{{ $item->req_no }}</strong>
                </div>
                <div class="p-4" style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.6;">
                    {{ $item->remarks }}
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn btn-modal-cancel"
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Reason View Modal --}}
@if($item->decline_reason && strlen($item->decline_reason) > 50)
<div class="modal fade" id="reasonViewModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="fas fa-comment-dots me-2"></i>
                    Decline Reason Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="alert alert-danger m-3">
                    <strong>Request #{{ $item->req_no }}</strong>
                </div>
                <div class="p-4" style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.6;">
                    {{ $item->decline_reason }}
                </div>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn btn-modal-cancel"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Document Modal --}}
@if ($item->image || $item->supporting_document)
<div class="modal fade" id="documentModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-lg-down" style="max-width: 1400px;">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="fas fa-file-alt me-2"></i>
                    Supporting Document - <span class="ms-1">#{{ $item->req_no }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light p-3" style="min-height: 75vh;">
                <div class="row g-3 h-100">
                    <div class="col-md-6 h-100">
                        <div class="border rounded-3 bg-white shadow-sm h-100 d-flex flex-column">
                            <div class="p-2 text-center border-bottom bg-gray-100">
                                <strong class="text-secondary fs-6">
                                    <i class="fas fa-history me-1 text-muted"></i>Old Document
                                </strong>
                            </div>
                            <div class="flex-fill d-flex align-items-center justify-content-center p-2" style="min-height: calc(75vh - 60px); overflow: hidden;">
                                @php
                                $oldPath = $item->image;
                                $oldExt = $oldPath ? strtolower(pathinfo($oldPath, PATHINFO_EXTENSION)) : null;
                                @endphp
                                @if ($oldPath)
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    @include('layout.partials.file-preview', ['filePath' => $oldPath, 'ext' => $oldExt, 'id' => 'old_' . $item->id])
                                </div>
                                @else
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-file text-secondary mb-2" style="font-size: 4rem;"></i>
                                    <p class="mb-0">No old document</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 h-100">
                        <div class="border rounded-3 bg-white shadow-sm h-100 d-flex flex-column">
                            <div class="p-2 text-center border-bottom bg-gray-100">
                                <strong class="text-secondary fs-6">
                                    <i class="fas fa-file-upload me-1 text-muted"></i>New Document
                                </strong>
                            </div>
                            <div class="flex-fill d-flex align-items-center justify-content-center p-2" style="min-height: calc(75vh - 60px); overflow: hidden;">
                                @php
                                $newPath = $item->supporting_document;
                                $newExt = $newPath ? strtolower(pathinfo($newPath, PATHINFO_EXTENSION)) : null;
                                @endphp
                                @if ($newPath)
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    @include('layout.partials.file-preview', ['filePath' => $newPath, 'ext' => $newExt, 'id' => 'new_' . $item->id])
                                </div>
                                @else
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-file text-secondary mb-2" style="font-size: 4rem;"></i>
                                    <p class="mb-0">No new document</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer-styled d-flex justify-content-center">
                <button type="button" class="btn btn-modal-cancel px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- Reason Input Modal (For Additional Decline) --}}
<div class="modal fade" id="reasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="fas fa-envelope me-2"></i>Additional Decline Reason
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label for="reasonInput" class="form-label">
                    <strong>Enter additional reason to send to student:</strong>
                </label>
                <textarea class="form-control" id="reasonInput" rows="3"
                    placeholder="Enter additional reason for declining this request again"></textarea>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    This will send another decline notification email to the student.
                </small>
            </div>
            <div class="modal-footer modal-footer-styled">
                <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-modal-confirm-decline" id="proceedToConfirmBtn">
                    <i class="fas fa-paper-plane me-1"></i>Send Notification
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ====== ELEMENT REFERENCES ======
        let targetForm;
        const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const filterSelect = document.getElementById('filterSelect');
        const sortSelect = document.getElementById('sortSelect');
        const resetBtn = document.getElementById('resetBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableContainer = document.getElementById('tableContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        let searchTimeout = null;
        let isInitialLoad = true;

        // ====== INITIAL STATE ======
        toggleClearButton();
        attachEventListeners();

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

        // ====== CLEAR BUTTON VISIBILITY ======
        function toggleClearButton() {
            clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
        }

        // ====== AJAX SEARCH FUNCTION ======
        function performAjaxSearch() {
            const search = searchInput.value.trim();
            const filter = filterSelect.value;
            const sort = sortSelect.value;

            loadingSpinner.style.display = 'block';
            tableContainer.style.opacity = '0.5';

            const url = `{{ route('declined-documents.index') }}?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

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

                    tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                    const oldInfoBanner = document.querySelector('.table-info-banner');
                    if (oldInfoBanner) oldInfoBanner.remove();

                    if (newInfoBanner) {
                        const cardBody = document.querySelector('.declined-card-body');
                        cardBody.insertBefore(newInfoBanner, cardBody.firstChild);
                    }

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

                    attachEventListeners();
                })
                .catch(err => {
                    console.error('AJAX Search Error:', err);
                    loadingSpinner.style.display = 'none';
                    tableContainer.style.opacity = '1';
                });
        }

        // ====== SEARCH INPUT ======
        searchInput.addEventListener('input', function() {
            toggleClearButton();
            clearTimeout(searchTimeout);
            // Don't trigger search during initial load sync
            if (!isInitialLoad) {
                searchTimeout = setTimeout(() => performAjaxSearch(), 400);
            }
        });

        // ====== CLEAR SEARCH BUTTON ======
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearButton();
            performAjaxSearch();
        });

        // ====== FILTER AND SORT SELECTS ======
        [filterSelect, sortSelect].forEach(el => {
            el?.addEventListener('change', performAjaxSearch);
        });

        // ====== RESET BUTTON ======
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterSelect.value = 'all';
            sortSelect.value = 'default';
            toggleClearButton();
            performAjaxSearch();
        });

        // ====== AJAX PAGINATION HANDLER ======
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

                        tableContainer.innerHTML = newTableContainer ?
                            newTableContainer.innerHTML :
                            '<div class="alert alert-warning text-center my-3">No results found.</div>';

                        if (paginationContainer && newPaginationWrapper) {
                            paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                        }

                        loadingSpinner.style.display = 'none';
                        tableContainer.style.opacity = '1';

                        document.querySelector('.declined-card').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        attachEventListeners();
                    })
                    .catch(err => {
                        console.error('AJAX Pagination Error:', err);
                        loadingSpinner.style.display = 'none';
                        tableContainer.style.opacity = '1';
                    });
            }
        });

        // ====== CLEAR ALL BUTTON ======
        function attachClearAllListener() {
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

        // ====== EVENT LISTENERS FOR NEW ELEMENTS ======
        function attachEventListeners() {
            attachClearAllListener();

            // Decline buttons
            document.querySelectorAll('.decline-btn').forEach(function(btn) {
                const form = btn.closest('form');
                if (form && form.querySelector('input[name="_method"][value="DELETE"]')) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        targetForm = form;
                        document.getElementById('reasonInput').value = '';
                        reasonModal.show();
                    });
                }
            });

            // Accept forms
            document.querySelectorAll('.accept-form').forEach(form => {
                let manualSubmit = false;
                form.addEventListener('submit', function(e) {
                    if (!manualSubmit) {
                        e.preventDefault();
                        const acceptBtn = form.querySelector('.accept-btn');
                        acceptBtn.disabled = true;
                        acceptBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Processing...`;
                        const row = form.closest('tr');
                        if (row) {
                            row.querySelectorAll('button, a.btn').forEach(btn => {
                                if (btn !== acceptBtn) {
                                    btn.disabled = true;
                                    btn.style.opacity = '0.5';
                                }
                            });
                        }
                        manualSubmit = true;
                        setTimeout(() => form.submit(), 100);
                    }
                });
            });
        }

        // ====== DECLINE CONFIRMATION ======
        document.getElementById('proceedToConfirmBtn').addEventListener('click', function() {
            const reason = document.getElementById('reasonInput').value.trim();

            if (!reason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please enter a reason!',
                    text: 'An additional decline reason is required.',
                    confirmButtonColor: '#1dd3b0'
                });
                return;
            }

            let reasonInput = targetForm.querySelector('.decline-reason');
            if (!reasonInput) {
                reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'remarks';
                reasonInput.className = 'decline-reason';
                targetForm.appendChild(reasonInput);
            }
            reasonInput.value = reason;

            reasonModal.hide();

            Swal.fire({
                icon: 'warning',
                title: 'Send Additional Decline Notification?',
                html: `You are about to send another decline notification with reason:<br><strong>"${reason}"</strong><br><br>The student will receive this email notification.`,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#1f2937',
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sending Email...',
                        html: 'Please wait while we send the notification...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const declineBtn = targetForm.querySelector(".decline-btn");
                    if (declineBtn) {
                        declineBtn.disabled = true;
                        declineBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Sending...`;
                    }

                    const row = targetForm.closest('tr');
                    if (row) {
                        row.querySelectorAll('button').forEach(b => {
                            if (b !== declineBtn) {
                                b.disabled = true;
                                b.style.opacity = '0.5';
                            }
                        });
                    }

                    targetForm.submit();
                }
            });
        });

        // ====== RE-ENABLE BUTTONS ON PAGE SHOW ======
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.querySelectorAll('.accept-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>Accept';
                });
                document.querySelectorAll('.decline-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-times-circle me-1"></i>Decline';
                });
            }
        });

        // ====== KEYBOARD SHORTCUTS ======
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput?.focus();
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
    .page-header-declined {
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
    .page-header-declined h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }
    .page-header-declined .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }
    .page-header-declined .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }
    .page-header-declined .breadcrumb-item.active {
        color: #d1d5db;
    }
    .total-counter {
        background: rgba(29, 211, 176, 0.15);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .total-counter span {
        color: var(--primary-green);
        font-weight: 700;
    }

    /* ===== CARD ===== */
    .declined-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .declined-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    /* ===== CARD HEADER ===== */
    .declined-card-header {
        background: var(--primary-dark);
        color: white;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .header-icon {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }
    .header-left h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .header-controls {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.5rem;
    }
    .header-controls .form-control,
    .header-controls .form-select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        font-size: 0.85rem;
        border-radius: 8px;
    }
    .header-controls .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }
    .header-controls .form-control:focus,
    .header-controls .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        color: white;
    }
    .header-controls .form-select option {
        background: var(--primary-dark);
        color: white;
    }

    /* ===== CARD BODY ===== */
    .declined-card-body {
        padding: 1.5rem;
    }

    /* ===== SEARCH CONTROLS ===== */
    .search-input-group {
        min-width: 200px;
        max-width: 300px;
    }
    .filter-select, .sort-select {
        min-width: 80px;
        max-width: 120px;
    }

    /* ===== RESET BUTTON ===== */
    .btn-reset {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        border-radius: 8px;
        padding: 0.3rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-reset:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* ===== TABLE STYLES ===== */
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    .table-declined {
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .table-declined thead th {
        background: var(--primary-dark);
        color: white;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.75rem;
        white-space: nowrap;
        vertical-align: middle;
        border: none;
    }
    .table-declined tbody tr {
        transition: all 0.2s ease;
    }
    .table-declined tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }
    .table-declined tbody td {
        vertical-align: middle;
        padding: 0.6rem 0.75rem;
        font-size: 0.85rem;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }
    .sortable-header a {
        transition: opacity 0.2s;
        color: white !important;
    }
    .sortable-header a:hover { opacity: 0.8; }

    /* ===== REQ NUMBER COLUMN ===== */
    .table-declined tbody td:first-child,
    .table-declined thead th:first-child {
        min-width: 120px; max-width: 120px; width: 120px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* ===== DATE COLUMN ===== */
    .table-declined tbody td:nth-child(10), .table-declined thead th:nth-child(10) {
        min-width: 95px; max-width: 95px; width: 95px; white-space: nowrap;
    }

    /* ===== ACTION BUTTONS ===== */
    .action-column {
        min-width: 80px; max-width: 280px; width: auto;
        white-space: nowrap; padding: 0.35rem 0.5rem !important;
    }
    .action-btn-group {
        display: inline-flex;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 0.35rem;
        align-items: center;
    }
    .action-column .accept-form,
    .action-column .decline-form,
    .action-column .delete-form {
        display: inline !important;
        margin: 0 !important;
    }
    .btn-action {
        border: none; border-radius: 8px;
        padding: 0.3rem 0.6rem; font-size: 0.75rem; font-weight: 600;
        color: white; transition: all 0.2s;
        display: inline-flex; align-items: center; white-space: nowrap;
    }
    .btn-action:hover { transform: translateY(-1px); color: white; }
    .btn-action i { font-size: 0.7rem; margin-right: 0.2rem; }
    .btn-action-accept {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
    }
    .btn-action-accept:hover {
        box-shadow: 0 3px 8px rgba(16, 185, 129, 0.4);
    }
    .btn-action-viewdoc {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    }
    .btn-action-viewdoc:hover {
        box-shadow: 0 3px 8px rgba(59, 130, 246, 0.4);
    }
    .btn-action-decline {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
    }
    .btn-action-decline:hover {
        box-shadow: 0 3px 8px rgba(239, 68, 68, 0.4);
    }

    /* ===== CLEAR ALL BUTTON ===== */
    .btn-clear-all {
        background: transparent;
        border: 1px solid var(--primary-green);
        color: var(--primary-green);
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-clear-all:hover {
        background: var(--primary-green);
        color: white;
    }

    /* ===== STATUS BADGE ===== */
    .status-declined {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        color: white;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        white-space: nowrap;
        border-radius: 6px;
    }

    /* ===== VIEW BUTTONS ===== */
    .view-remarks-btn, .view-reason-btn {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
        white-space: nowrap;
    }

    /* ===== MODAL STYLES ===== */
    .modal-styled {
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid #334155;
        border-radius: 16px;
        overflow: hidden;
    }
    .modal-header-styled {
        background: #0f172a;
        border-bottom: 1px solid #334155;
        padding: 1rem 1.25rem;
    }
    .modal-styled .modal-title {
        color: var(--primary-green);
        font-weight: 600;
    }
    .modal-styled .modal-body { padding: 1.25rem; }
    .modal-footer-styled {
        background: #0f172a;
        border-top: 1px solid #334155;
        padding: 0.75rem 1.25rem;
    }
    .modal-styled .modal-body .alert-info {
        background: rgba(29, 211, 176, 0.1);
        border-color: rgba(29, 211, 176, 0.2);
        color: #a7f3d0;
    }
    .modal-styled .modal-body .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    .modal-styled .form-control,
    .modal-styled .form-select {
        background: #0f172a; border: 1px solid #475569;
        color: #e2e8f0; border-radius: 10px;
    }
    .modal-styled .form-control:focus,
    .modal-styled .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        background: #0f172a; color: #e2e8f0;
    }
    .modal-styled .form-label { color: #cbd5e1; font-weight: 500; }
    .modal-styled .text-muted { color: #94a3b8 !important; }
    .modal-styled .modal-body > div[style*="white-space"] { color: #e2e8f0; }
    .btn-modal-cancel {
        background: transparent;
        border: 1px solid #475569;
        border-radius: 10px;
        color: #e2e8f0;
        font-weight: 600;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }
    .btn-modal-cancel:hover { background: #334155; color: white; }
    .btn-modal-confirm-decline {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        border: none; border-radius: 10px;
        color: white; font-weight: 600; padding: 0.4rem 1rem;
        transition: all 0.2s;
    }
    .btn-modal-confirm-decline:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }
    .modal-dialog { max-width: 600px; }

    /* ===== DOCUMENT MODAL OVERRIDES ===== */
    .modal-fullscreen-lg-down .modal-styled .modal-body {
        background: #1e293b;
    }
    .modal-styled .modal-body .border { border-color: #334155 !important; }
    .modal-styled .modal-body .bg-white { background: #0f172a !important; }
    .modal-styled .modal-body .bg-gray-100 { background: #1e293b !important; }
    .modal-styled .modal-body .text-secondary { color: #94a3b8 !important; }
    .modal-styled .modal-body .text-muted { color: #64748b !important; }
    .modal-styled .modal-body .border-bottom { border-color: #334155 !important; }
    .modal-styled .modal-body .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }

    /* ===== BUTTON STATES ===== */
    .btn:disabled { cursor: not-allowed; opacity: 0.65; }
    .spinner-border-sm { width: 0.875rem; height: 0.875rem; border-width: 0.125rem; }

    /* ===== LOADING STATE ===== */
    #tableContainer { transition: opacity 0.3s ease; overflow-x: auto; }

    /* ===== ALERT STYLES ===== */
    .table-info-banner {
        background: rgba(29, 211, 176, 0.08);
        border: 1px solid rgba(29, 211, 176, 0.2);
        color: #1f2937;
        border-radius: 10px;
    }
    .alert-warning {
        background-color: #fffbeb;
        border-color: #fcd34d;
        color: #92400e;
    }

    /* ===== PAGINATION ===== */
    .pagination { margin-bottom: 0; }

    /* ===== SMOOTH TRANSITIONS ===== */
    .btn, .form-control, .form-select { transition: all 0.2s ease-in-out; }

    /* ===== UTILITY ===== */
    .fw-semibold { font-weight: 600; }
    .text-teal { color: #1dd3b0 !important; }
    .bg-gray-100 { background-color: #f8fafc !important; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991px) {
        .declined-card-header {
            flex-direction: column;
            align-items: stretch;
        }
        .header-controls {
            flex-direction: column;
        }
        .search-input-group {
            max-width: 100%;
        }
        .header-controls .form-select {
            max-width: 100%;
        }
    }
    @media (max-width: 767px) {
        .page-header-declined {
            padding: 1.25rem;
            border-radius: 12px;
            flex-direction: column;
            align-items: flex-start;
        }
        .page-header-declined h1 { font-size: 1.35rem; }
        .declined-card { border-radius: 12px; }
        .declined-card-header { padding: 0.875rem 1rem; }
        .declined-card-body { padding: 1rem; }
    }
    @media (max-width: 575px) {
        .page-header-declined h1 { font-size: 1.15rem; }
        .total-counter { font-size: 0.8rem; padding: 0.35rem 0.9rem; }
        .table-declined { font-size: 0.75rem; }
        .table-declined th, .table-declined td { padding: 0.4rem 0.35rem; }
        .table-declined tbody td:first-child { min-width: 100px; max-width: 100px; width: 100px; }
        .action-column { min-width: 180px; max-width: 180px; width: 180px; }
        .action-btn-group { flex-wrap: wrap; }
        .btn-action { min-width: 85px; font-size: 0.65rem; padding: 0.25rem 0.4rem; }
    }
</style>

</div> {{-- close container-fluid --}}

@endsection
