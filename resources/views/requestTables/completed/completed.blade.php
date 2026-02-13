
@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('layout.partials.message')

<div class="container-fluid px-4 py-4">

{{-- Page Header --}}
<div class="page-header-completed">
    <div>
        <h1><i class="fas fa-clipboard-check me-2"></i>For Release Requests</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">For Release Requests</li>
        </ol>
    </div>
    <div class="total-counter">
        Total: <span>{{ $totalCount }}</span>
    </div>
</div>

<x-tabs page='ForRelease' :filteredCount="$filteredCount" :searchCounts="$searchCounts" />

{{-- Main Card --}}
<div class="completed-card mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="completed-card-header">
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
    <div class="completed-card-body">
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
                <a href="{{ route('tables.index') }}" class="btn btn-sm btn-clear-all ms-2" id="clearAllBtn">Clear All</a>
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
                <i class="fas fa-search me-2"></i>No For Release document requests found matching your search criteria.
                @else
                <i class="fas fa-inbox me-2"></i>No For Release document requests found.
                @endif
            </div>
            @else
            <table class="table table-hover table-completed" id="requestsTable">
                <thead>
                    <tr>
                        <th class="sortable-header">
                            <a href="{{ route('tables.index', array_merge(request()->except('page'), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th>Document</th>
                        <th>School</th>
                        <th>For Release Days</th>
                        <th>Status</th>
                        <th>Rel Date</th>
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
                        <td>
                            @php
                                $releaseDate = \Carbon\Carbon::parse($item->forRelease_date);
                                $today = \Carbon\Carbon::now();
                                $daysSinceRelease = floor($releaseDate->diffInDays($today));
                            @endphp
                            <span class="days-badge {{ $daysSinceRelease > 7 ? 'days-danger' : ($daysSinceRelease > 3 ? 'days-warning' : 'days-ok') }}">
                                {{ $daysSinceRelease }} {{ $daysSinceRelease == 1 ? 'day' : 'days' }}
                            </span>
                        </td>
                        <td><span class="status-forrelease">{{ $item->status }}</span></td>
                        <td>{{ $item->forRelease_date }}</td>
                        <td class="action-column">
                            <div class="action-btn-group" role="group">
                                <button type="button" class="btn btn-action btn-action-complete complete-btn"
                                    data-request-id="{{ $item->id }}" data-request-no="{{ $item->req_no }}"
                                    data-student-name="{{ $item->studentInformation->full_name }}"
                                    data-bs-toggle="modal" data-bs-target="#claimerModal">
                                    <i class="fas fa-check me-1"></i>Claimed
                                </button>

                                @if (!empty($PermissionEdit))
                                <a href="{{ route('tables.edit', $item->id) }}"
                                    class="btn btn-sm btn-action btn-action-edit">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @endif

                                <button type="button" class="btn btn-action btn-action-revert revert-btn"
                                    data-request-id="{{ $item->id }}"
                                    data-request-no="{{ $item->req_no }}"
                                    data-student-name="{{ $item->studentInformation->full_name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#revertModal">
                                    <i class="fas fa-undo me-1"></i>Revert
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

{{-- Remarks Modals --}}
@foreach ($DocRequests as $item)
@if($item->remarks && strlen($item->remarks) > 50)
<div class="modal fade" id="remarksModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title fw-semibold d-flex align-items-center">
                    <i class="bi bi-chat-left-dots me-2"></i>Full Remarks
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
@endforeach

{{-- Revert Modal --}}
<div class="modal fade" id="revertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>Revert Document to Processing
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="revertForm" action="{{ route('tables.revert', '') }}" method="POST"
                data-swal-loading="true" data-swal-title="Reverting Request to Processing"
                data-swal-text="This may take a few seconds...">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-warning">
                            <strong>Request No:</strong> <span id="modalRevertRequestNo"></span><br>
                            <strong>Student:</strong> <span id="modalRevertStudentName"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="revertReason" class="form-label">
                            <i class="fas fa-comment me-1"></i>Reason for Revert <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="revertReason" name="revert_reason" rows="3" required
                            placeholder="Please provide a reason for reverting this document to Processing status..."></textarea>
                        <div class="invalid-feedback">
                            Please provide a reason for reverting this document.
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This action will change the document status back to "Processing" and clear the for release date.
                    </div>
                </div>
                <div class="modal-footer modal-footer-styled">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-modal-confirm-revert" id="submitRevertBtn">
                        <i class="fas fa-undo me-1"></i>Revert to Processing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Claimer Information Modal --}}
<div class="modal fade" id="claimerModal" tabindex="-1" aria-labelledby="claimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-styled">
            <div class="modal-header modal-header-styled">
                <h5 class="modal-title" id="claimerModalLabel">
                    <i class="fas fa-user-check me-2"></i>Document Claim Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="claimerForm" action="{{ route('document-request3.complete', 0) }}" method="POST"
                data-swal-loading="true" data-swal-title="Releasing Document Request"
                data-swal-text="This may take a few seconds...">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>Request No:</strong> <span id="modalRequestNo"></span><br>
                            <strong>Student:</strong> <span id="modalStudentName"></span>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input thick-outline" type="checkbox" id="sameAsStudent">
                        <label class="form-check-label fw-bold" for="sameAsStudent">
                            Claimer is the same as the requestor
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="claimerFirstName" class="form-label">
                                    <i class="fas fa-user me-1"></i>First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="claimerFirstName"
                                    name="claimer_first_name" required>
                                <div class="invalid-feedback">
                                    Please provide the claimer's first name.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="claimerLastName" class="form-label">
                                    <i class="fas fa-user me-1"></i>Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="claimerLastName"
                                    name="claimer_last_name" required>
                                <div class="invalid-feedback">
                                    Please provide the claimer's last name.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="claimerDate" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="claimerDate" name="claimer_date" required>
                        <div class="invalid-feedback">
                            Please provide a valid date.
                        </div>
                        <small class="form-text text-muted">Select the appropriate date</small>
                    </div>

                </div>
                <div class="modal-footer modal-footer-styled">
                    <button type="button" class="btn btn-modal-cancel"
                        data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-modal-confirm"
                        id="submitClaimBtn">
                        <i class="fas fa-check me-1"></i>Mark as Claimed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ======= CHECK FOR SESSION MESSAGES (after page reload) =======
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

        // ======= ELEMENT REFERENCES =======
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const filterSelect = document.getElementById('filterSelect');
        const sortSelect = document.getElementById('sortSelect');
        const resetBtn = document.getElementById('resetBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableContainer = document.getElementById('tableContainer');
        let searchTimeout = null;
        let isInitialLoad = true;

        // ======= INITIAL STATE =======
        toggleClearButton();
        attachCompleteButtonListeners();
        attachRevertButtonListeners();
        attachClearAllListener();

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

        // ======= CLEAR BUTTON VISIBILITY =======
        function toggleClearButton() {
            clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
        }

        // ======= SEARCH INPUT =======
        searchInput.addEventListener('input', function() {
            toggleClearButton();
            clearTimeout(searchTimeout);
            // Don't trigger search during initial load sync
            if (!isInitialLoad) {
                searchTimeout = setTimeout(() => performAjaxSearch(), 400);
            }
        });

        // ======= CLEAR SEARCH BUTTON =======
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearButton();
            performAjaxSearch();
        });

        // ======= FILTER AND SORT SELECTS =======
        [filterSelect, sortSelect].forEach(el => {
            el?.addEventListener('change', performAjaxSearch);
        });

        // ======= RESET BUTTON =======
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterSelect.value = 'all';
            sortSelect.value = 'default';
            toggleClearButton();
            performAjaxSearch();
        });

        // ======= AJAX SEARCH FUNCTION =======
        function performAjaxSearch() {
            const search = searchInput.value.trim();
            const filter = filterSelect.value;
            const sort = sortSelect.value;

            loadingSpinner.style.display = 'block';
            tableContainer.style.opacity = '0.5';

            const url = `{{ route('tables.index') }}?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newTableContainer = doc.querySelector('#tableContainer');
                    const newInfoBanner = doc.querySelector('.table-info-banner');
                    const newPaginationWrapper = doc.querySelector('#paginationContainer');
                    const newSearchCounter = doc.querySelector('#searchResultsCounter');

                    // Update table
                    tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                    // Update info banner
                    const oldInfoBanner = document.querySelector('.table-info-banner');
                    if (oldInfoBanner) oldInfoBanner.remove();

                    if (newInfoBanner) {
                        const cardBody = document.querySelector('.completed-card-body');
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

                    attachCompleteButtonListeners();
                    attachClearAllListener();

                    loadingSpinner.style.display = 'none';
                    tableContainer.style.opacity = '1';
                })
                .catch(err => {
                    console.error('AJAX Search Error:', err);
                    loadingSpinner.style.display = 'none';
                    tableContainer.style.opacity = '1';
                });
        }

        // ======= AJAX PAGINATION HANDLER =======
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
                        document.querySelector('.completed-card').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        attachCompleteButtonListeners();
                        attachRevertButtonListeners();
                        attachClearAllListener();
                    })
                    .catch(err => {
                        console.error('AJAX Pagination Error:', err);
                        loadingSpinner.style.display = 'none';
                        tableContainer.style.opacity = '1';
                    });
            }
        });

        // ======= CLEAR ALL BUTTON (AJAX) =======
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

        // ======= REVERT BUTTON HANDLER =======
        function attachRevertButtonListeners() {
            document.querySelectorAll('.revert-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const requestId = this.dataset.requestId;
                    const requestNo = this.dataset.requestNo;
                    const studentName = this.dataset.studentName;

                    document.getElementById('modalRevertRequestNo').textContent = requestNo;
                    document.getElementById('modalRevertStudentName').textContent = studentName;
                    document.getElementById('revertForm').action = `{{ url('tables/revert') }}/${requestId}`;
                });
            });
        }

        // ======= COMPLETE BUTTON HANDLER =======
        function attachCompleteButtonListeners() {
            document.querySelectorAll('.complete-btn').forEach(btn => {
                btn.addEventListener('click', handleCompleteClick);
            });
        }

        // ======= MODAL HANDLING =======
        const sameAsStudentCheckbox = document.getElementById('sameAsStudent');
        const claimerFirstName = document.getElementById('claimerFirstName');
        const claimerLastName = document.getElementById('claimerLastName');
        const claimerDate = document.getElementById('claimerDate');
        const claimerModal = document.getElementById('claimerModal');
        const claimerForm = document.getElementById('claimerForm');
        const submitClaimBtn = document.getElementById('submitClaimBtn');

        sameAsStudentCheckbox?.addEventListener('change', fillClaimerInfo);

        function fillClaimerInfo() {
            const studentName = document.getElementById('modalStudentName').textContent.trim();
            if (sameAsStudentCheckbox.checked && studentName) {
                const nameParts = studentName.split(' ');
                claimerLastName.value = nameParts.pop() || '';
                claimerFirstName.value = nameParts.join(' ');
                claimerFirstName.readOnly = true;
                claimerLastName.readOnly = true;
            } else {
                claimerFirstName.value = '';
                claimerLastName.value = '';
                claimerFirstName.readOnly = false;
                claimerLastName.readOnly = false;
            }
        }

        function handleCompleteClick() {
            const requestId = this.dataset.requestId;
            const requestNo = this.dataset.requestNo;
            const studentName = this.dataset.studentName;

            document.getElementById('modalRequestNo').textContent = requestNo;
            document.getElementById('modalStudentName').textContent = studentName;

            claimerForm.action = `{{ url('tables/completeRequest') }}/${requestId}`;
            claimerForm.reset();
            claimerForm.classList.remove('was-validated');
            sameAsStudentCheckbox.checked = false;
            claimerFirstName.readOnly = false;
            claimerLastName.readOnly = false;

            const today = new Date().toISOString().split('T')[0];
            claimerDate.value = today;

            claimerForm.querySelectorAll('.alert-danger, .alert-success').forEach(alert => {
                if (!alert.classList.contains('alert-info')) alert.remove();
            });

            setLoadingState(false);
        }

        claimerModal?.addEventListener('hidden.bs.modal', function() {
            claimerForm.classList.remove('was-validated');
            claimerForm.querySelectorAll('.alert-danger, .alert-success').forEach(alert => {
                if (!alert.classList.contains('alert-info')) alert.remove();
            });
            setLoadingState(false);
        });

        // ======= CLAIMER FORM SUBMIT =======
        claimerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!claimerForm.checkValidity()) {
                e.stopPropagation();
                claimerForm.classList.add('was-validated');
                return;
            }

            setLoadingState(true);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formDataObject = {
                claimer_first_name: claimerFirstName.value,
                claimer_last_name: claimerLastName.value,
                claimer_date: claimerDate.value
            };

            fetch(claimerForm.action, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formDataObject),
                })
                .then(async response => {
                    // Log the response for debugging
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);

                    // Try to parse as JSON
                    let result;
                    try {
                        result = await response.json();
                        console.log('Response data:', result);
                    } catch (e) {
                        console.error('Failed to parse JSON:', e);
                        showRevertError('Invalid response from server. Please try again.');
                        setLoadingState(false);
                        return;
                    }

                    if (!response.ok) {
                        // Handle validation errors (422) or other errors
                        let errorMessage = result.message || 'An error occurred while processing the request.';

                        if (result.errors) {
                            // Display validation errors
                            errorMessage = Object.values(result.errors).flat().join(' ');
                        }

                        console.error('Error response:', errorMessage);
                        showRevertError(errorMessage);
                        setLoadingState(false);
                        return;
                    }

                    // Success - Close modal and reload page
                    console.log('Success! Message:', result.message);

                    // Close the modal using Bootstrap 5 method
                    const modalElement = document.getElementById('claimerModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Show success message and reload after modal closes
                    setTimeout(() => {
                        alert('✓ ' + (result.message || 'Document marked as claimed successfully!'));
                        window.location.reload();
                    }, 300);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showRevertError('A network error occurred. Please check your connection and try again.');
                    setLoadingState(false);
                });
        });

        // ======= LOADING STATES =======
        function setLoadingState(isLoading) {
            const formInputs = claimerForm.querySelectorAll('input, select, textarea, button');
            if (isLoading) {
                submitClaimBtn.disabled = true;
                submitClaimBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;
                formInputs.forEach(i => i.disabled = true);
            } else {
                submitClaimBtn.disabled = false;
                submitClaimBtn.innerHTML = `<i class="fas fa-check me-1"></i>Mark as Claimed`;
                formInputs.forEach(i => i.disabled = false);
            }
        }

        // ======= SUCCESS ALERT INSIDE MODAL =======
        function showRevertSuccess(message) {
            let successAlert = document.getElementById('modalRevertSuccessAlert');
            if (!successAlert) {
                successAlert = document.createElement('div');
                successAlert.id = 'modalRevertSuccessAlert';
                successAlert.className = 'alert alert-success fade show mt-2';
                successAlert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                <span id="revertSuccessMessage"></span>
            `;
                claimerForm.querySelector('.modal-body').insertBefore(
                    successAlert,
                    claimerForm.querySelector('.modal-body').firstChild
                );
            }

            document.getElementById('revertSuccessMessage').textContent = message;
            successAlert.style.display = 'block';
        }

        // ======= ERROR ALERT =======
        function showRevertError(message) {
            let errorAlert = document.getElementById('modalRevertErrorAlert');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.id = 'modalRevertErrorAlert';
                errorAlert.className = 'alert alert-danger fade show mt-2';
                errorAlert.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="revertErrorMessage"></span>
            `;
                claimerForm.querySelector('.modal-body').insertBefore(
                    errorAlert,
                    claimerForm.querySelector('.modal-body').firstChild
                );
            }

            document.getElementById('revertErrorMessage').textContent = message;
            errorAlert.style.display = 'block';
        }

        // ======= REVERT FORM SUBMISSION =======
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

            const formData = new FormData(revertForm);

            // Explicitly append revert_reason if not captured by FormData
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
                    // Reload immediately - success message will show after page loads
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to revert document');
                }
            })
            .catch(error => {
                setRevertLoadingState(false);
                showRevertErrorModal(error.message || 'An error occurred while reverting the document.');
            });
        });

        function setRevertLoadingState(isLoading) {
            submitRevertBtn.disabled = isLoading;
            submitRevertBtn.innerHTML = isLoading
                ? '<span class="spinner-border spinner-border-sm me-1"></span>Reverting...'
                : '<i class="fas fa-undo me-1"></i>Revert to Processing';
            revertReason.disabled = isLoading;
        }

        function showRevertErrorModal(message) {
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

        // Auto-resize revert textarea
        revertReason?.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // ======= KEYBOARD SHORTCUTS =======
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
    .page-header-completed {
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
    .page-header-completed h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }
    .page-header-completed .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }
    .page-header-completed .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }
    .page-header-completed .breadcrumb-item.active {
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
    .completed-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .completed-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    /* ===== CARD HEADER ===== */
    .completed-card-header {
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

    /* ===== CARD BODY ===== */
    .completed-card-body {
        padding: 1.5rem;
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
    .table-completed {
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .table-completed thead th {
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
    .table-completed tbody tr {
        transition: all 0.2s ease;
    }
    .table-completed tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }
    .table-completed tbody td {
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
    .table-completed tbody td:first-child,
    .table-completed thead th:first-child {
        min-width: 120px; max-width: 120px; width: 120px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* ===== DATE COLUMN ===== */
    .table-completed tbody td:nth-child(7),
    .table-completed thead th:nth-child(7) {
        min-width: 95px; max-width: 95px; width: 95px; white-space: nowrap;
    }

    /* ===== DAYS BADGE ===== */
    .days-badge {
        display: inline-block;
        padding: 0.3rem 0.65rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .days-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .days-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1f2937;
    }
    .days-ok {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
    }

    /* ===== STATUS BADGE ===== */
    .status-forrelease {
        display: inline-block;
        padding: 0.3rem 0.65rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1f2937;
        white-space: nowrap;
    }

    /* ===== ACTION BUTTONS ===== */
    .action-column {
        min-width: 80px; max-width: 220px; width: auto;
        white-space: nowrap; padding: 0.35rem 0.5rem !important;
    }
    .action-btn-group {
        display: inline-flex;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 0.35rem;
        align-items: center;
    }
    .btn-action {
        border: none; border-radius: 8px;
        padding: 0.3rem 0.6rem; font-size: 0.75rem; font-weight: 600;
        color: white; transition: all 0.2s;
        display: inline-flex; align-items: center; white-space: nowrap;
    }
    .btn-action:hover { transform: translateY(-1px); color: white; }
    .btn-action i { font-size: 0.7rem; margin-right: 0.2rem; }
    .btn-action-complete {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .btn-action-complete:hover {
        box-shadow: 0 3px 8px rgba(16, 185, 129, 0.4);
    }
    .btn-action-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .btn-action-edit:hover {
        box-shadow: 0 3px 8px rgba(245, 158, 11, 0.4);
    }
    .btn-action-revert {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .btn-action-revert:hover {
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        color: white;
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

    /* ===== VIEW BUTTONS ===== */
    .view-remarks-btn {
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
    .modal-styled .form-check-label { color: #e2e8f0; }
    .modal-styled .alert-warning {
        background: rgba(251, 191, 36, 0.15);
        border-color: rgba(251, 191, 36, 0.3);
        color: #fbbf24;
    }
    .modal-styled .alert-info {
        background: rgba(29, 211, 176, 0.1);
        border-color: rgba(29, 211, 176, 0.2);
        color: #a7f3d0;
    }
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
    .btn-modal-confirm {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none; border-radius: 10px;
        color: white; font-weight: 600; padding: 0.4rem 1rem;
        transition: all 0.2s;
    }
    .btn-modal-confirm:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }
    .btn-modal-confirm-revert {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border: none; border-radius: 10px;
        color: #1f2937; font-weight: 600; padding: 0.4rem 1rem;
        transition: all 0.2s;
    }
    .btn-modal-confirm-revert:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
        color: #1f2937;
    }
    .modal-dialog { max-width: 600px; }
    .thick-outline {
        width: 1.2rem; height: 1.2rem;
        border: 2.5px solid #475569 !important;
        accent-color: var(--primary-green);
        cursor: pointer;
    }
    .thick-outline:focus {
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.4);
    }

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
    .modal #tableSearchAlert { display: none !important; }

    /* ===== RESPONSIVE ===== */
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
        .page-header-completed {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-completed h1 {
            font-size: 1.35rem;
        }

        .total-counter {
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
        }

        .total-counter span {
            font-size: 1.1rem;
        }

        .completed-card {
            border-radius: 12px;
        }

        .completed-card-header {
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

        .completed-card-body {
            padding: 1rem;
        }

        .table-completed {
            font-size: 0.8rem;
        }

        .table-completed thead th {
            font-size: 0.725rem;
            padding: 0.6rem 0.5rem;
        }
    }
    @media (max-width: 575px) {
        .page-header-completed h1 { font-size: 1.15rem; }
        .total-counter { font-size: 0.8rem; padding: 0.35rem 0.9rem; }
        .table-completed { font-size: 0.75rem; }
        .table-completed th, .table-completed td { padding: 0.4rem 0.35rem; }
        .table-completed tbody td:first-child { min-width: 100px; max-width: 100px; width: 100px; }
        .action-column { min-width: 180px; max-width: 180px; width: 180px; }
        .action-btn-group { flex-wrap: wrap; }
        .btn-action { min-width: 85px; font-size: 0.65rem; padding: 0.25rem 0.4rem; }
    }
</style>

</div> {{-- close container-fluid --}}

@endsection
