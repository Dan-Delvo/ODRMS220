@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row align-items-center">
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <h1 class="mt-4">
            <span class="badge page-title-badge">Declined Requests</span>
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Declined Requests</li>
        </ol>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <h1 class="mt-md-4">
            <span class="badge count-badge">Total: {{ $DocRequests->total() }}</span>
        </h1>
    </div>
</div>

<x-tabs page='Declined' />

{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header card-header-custom">
        <h5 class="mb-0">Declined Document Requests</h5>

        {{-- Search/Filter Form --}}
        <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
            {{-- Search Input --}}
            <div class="input-group" style="width: 300px;">
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
                <a href="{{ route('declined-documents.index') }}" class="btn btn-sm btn-outline-info ms-2" id="clearAllBtn">Clear All</a>
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
                No declined document requests found matching your search criteria.
                @else
                No declined document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                <thead class="table-dark">
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
                        <td><span class="badge bg-danger text-white status-badge">{{ $item->status }}</span></td>
                        <td>{{ $item->request_date }}</td>
                        <td class="action-column">
                            <div class="btn-group-vertical btn-group-sm d-md-inline" role="group">
                                <form action="{{ route('document-request.complete', $item->id) }}"
                                    method="POST" class="d-inline accept-form" data-swal-loading="true"
                                    data-swal-title="Reaccepting Declined Request"
                                    data-swal-text="This may take a few seconds...">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm accept-btn"
                                        data-original-text="Accept">
                                        <i class="fas fa-check me-1"></i>Accept
                                    </button>
                                </form>

                                @if ($item->supporting_document)
                                <button type="button" class="btn btn-primary btn-sm"
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
                                    <button type="submit" class="btn btn-danger btn-sm decline-btn">
                                        <i class="fas fa-times-circle me-1"></i>Decline
                                    </button>
                                </form>
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0" style="background-color: #1f2937;">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="bi bi-chat-left-dots me-2" style="color: #1dd3b0;"></i>
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
            <div class="modal-footer border-0">
                <button type="button" class="btn rounded-pill px-4" 
                    style="background-color: #1dd3b0; color: white;" 
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0" style="background-color: #1f2937;">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="fas fa-comment-dots me-2" style="color: #1dd3b0;"></i>
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
            <div class="modal-footer border-0">
                <button type="button" class="btn rounded-pill px-4" 
                    style="background-color: #1dd3b0; color: white;" 
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
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header text-white" style="background-color: #1f2937; padding: 1rem 1.5rem;">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="fas fa-file-alt me-2 text-teal"></i>
                    Supporting Document - <span class="text-teal ms-1">#{{ $item->req_no }}</span>
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
            <div class="modal-footer d-flex justify-content-center" style="background-color: #1f2937; padding: 0.75rem;">
                <button type="button" class="btn btn-outline-light px-4 py-2 rounded-pill" data-bs-dismiss="modal" style="border-color: #1dd3b0; color: #1dd3b0;">
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0" style="background-color: #1f2937;">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="fas fa-envelope me-2" style="color: #1dd3b0;"></i>Additional Decline Reason
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
            <div class="modal-footer border-0">
                <button type="button" class="btn rounded-pill px-4" style="background-color: #1f2937; color: white;" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="proceedToConfirmBtn">
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

        // ====== INITIAL STATE ======
        toggleClearButton();
        attachEventListeners();

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

                    tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                    const oldInfoBanner = document.querySelector('.table-info-banner');
                    if (oldInfoBanner) oldInfoBanner.remove();

                    if (newInfoBanner) {
                        const cardBody = document.querySelector('.card-body.bg-light');
                        cardBody.insertBefore(newInfoBanner, cardBody.firstChild);
                    }

                    if (paginationContainer && newPaginationWrapper) {
                        paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                    } else if (paginationContainer && !newPaginationWrapper) {
                        paginationContainer.innerHTML = '';
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
            searchTimeout = setTimeout(() => performAjaxSearch(), 400);
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

                        document.querySelector('.card').scrollIntoView({
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

    /* ===== DATE COLUMN - ONE LINE ===== */
    #requestsTable tbody td:nth-child(10),
    #requestsTable thead th:nth-child(10) {
        min-width: 95px !important;
        max-width: 95px !important;
        width: 95px !important;
        white-space: nowrap !important;
    }

    /* ===== ACTION COLUMN - DYNAMIC WIDTH ===== */
    .action-column {
        min-width: 80px !important;
        max-width: 280px !important;
        width: auto !important;
        white-space: nowrap !important;
        padding: 0.25rem 0.3rem !important;
    }

    .btn-group-vertical {
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 0.3rem !important;
        align-items: center !important;
        justify-content: flex-start !important;
    }

    .action-column .btn {
        padding: 0.3rem 0.5rem !important;
        font-size: 0.75rem !important;
        width: auto !important;
        min-width: fit-content !important;
        max-width: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        margin: 0 !important;
        white-space: nowrap !important;
        line-height: 1.2 !important;
        flex-shrink: 0 !important;
    }

    .action-column .btn i {
        font-size: 0.7rem !important;
        margin-right: 0.2rem !important;
    }

    .action-column .accept-form,
    .action-column .decline-form {
        display: inline !important;
        margin: 0 !important;
    }

    .action-column .accept-btn {
        min-width: 68px !important;
    }

    .action-column .btn-primary {
        min-width: 80px !important;
    }

    .action-column .decline-btn {
        min-width: 68px !important;
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

    .text-teal {
        color: #1dd3b0 !important;
    }

    .bg-gray-100 {
        background-color: #f8fafc !important;
    }
</style>

@endsection