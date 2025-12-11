
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
            <span class="badge page-title-badge">For Release Requests</span>
        </h1>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <h1 class="mt-md-4">
            <span class="badge count-badge">Total: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

<x-tabs page='ForRelease' :filteredCount="$filteredCount" :searchCounts="$searchCounts" />

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
                No For Release document requests found matching your search criteria.
                @else
                No For Release document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                <thead class="table-dark">
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
                            <span class="badge {{ $daysSinceRelease > 7 ? 'bg-danger' : ($daysSinceRelease > 3 ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ $daysSinceRelease }} {{ $daysSinceRelease == 1 ? 'day' : 'days' }}
                            </span>
                        </td>
                        <td><span class="badge text-black status-badge" style="background-color: #FFFF00">{{ $item->status }}</span></td>
                        <td>{{ $item->forRelease_date }}</td>
                        <td class="action-column">
                            <div class="btn-group-vertical btn-group-sm d-md-inline" role="group">
                                <button type="button" class="btn btn-success btn-sm complete-btn"
                                    data-request-id="{{ $item->id }}" data-request-no="{{ $item->req_no }}"
                                    data-student-name="{{ $item->studentInformation->full_name }}"
                                    data-bs-toggle="modal" data-bs-target="#claimerModal">
                                    <i class="fas fa-check me-1"></i>Claimed
                                </button>

                                @if (!empty($PermissionEdit))
                                <a href="{{ route('tables.edit', $item->id) }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
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
@endforeach

{{-- Claimer Information Modal --}}
<div class="modal fade" id="claimerModal" tabindex="-1" aria-labelledby="claimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #1f2937;">
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
                <div class="modal-footer">
                    <button type="button" class="btn text-white" style="background-color: #1f2937;"
                        data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn text-white" style="background-color: #1dd3b0"
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
                        const cardBody = document.querySelector('.card-body.bg-light');
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
                        document.querySelector('.card').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        attachCompleteButtonListeners();
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
                claimer_date: claimerDate.value,
                _method: 'PUT',
                _token: csrfToken
            };

            fetch(claimerForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formDataObject),
                })
                .then(async response => {
                    if (!response.ok) throw new Error('Error updating claim.');
                    const result = await response.json();
                    showRevertSuccess(result.message);
                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(error => {
                    console.error(error);
                    showRevertError(error.message);
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

    /* ===== DATE COLUMNS - ONE LINE ===== */
    /* Rel Date */
    #requestsTable tbody td:nth-child(7),
    #requestsTable thead th:nth-child(7) {
        min-width: 95px !important;
        max-width: 95px !important;
        width: 95px !important;
        white-space: nowrap !important;
    }

    /* ===== ACTION COLUMN - DYNAMIC WIDTH ===== */
    .action-column {
        min-width: 80px !important;
        max-width: 200px !important;
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

    /* Specific button width adjustments */
    .action-column .complete-btn {
        min-width: 70px !important;
    }

    .action-column .btn-warning {
        min-width: 58px !important;
    }

    /* ===== VIEW BUTTONS ===== */
    .view-remarks-btn {
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
        border-color: var(--success-color);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    input[type="date"]:focus {
        border-color: var(--info-color);
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    .thick-outline {
        width: 1.2rem;
        height: 1.2rem;
        border: 2.5px solid var(--secondary-color) !important;
        accent-color: var(--primary-color);
        cursor: pointer;
    }

    .thick-outline:focus {
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.4);
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

    /* ===== HIDE MODAL SEARCH ALERT ===== */
    .modal #tableSearchAlert {
        display: none !important;
    }
</style>

@endsection
