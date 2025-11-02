@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">For Release Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">For Release Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color: #1f2937; font-size: 2rem;">Total For Release: <span id="totalCount">{{ $totalCount }}</span></span>
        </h1>
    </div>
</div>

<ul class="nav nav-tabs" data-bs-theme="dark">
  <li class="nav-item">
    <a class="nav-link text-dark " href="{{ route('pending.index') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link  text-dark" href="{{ route('ongoing.index') }}">Processing</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="{{ route('tables.index') }}">For Release</a>
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
        <h5 class="mb-2 mb-md-0">For Release Document Requests</h5>

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
            <select name="filter" id="filterSelect" class="form-select form-select-sm" style="width: auto;">
                <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Fields</option>
                <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student Name</option>
                <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document Type</option>
                <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School/Entity</option>
                <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Request No.</option>
            </select>

            {{-- Sort Dropdown --}}
            <select name="sort" id="sortSelect" class="form-select form-select-sm" style="width: auto;">
                <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default Order</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Req No. (A-Z)</option>
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Req No. (Z-A)</option>
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
            @if($DocRequests->isEmpty())
            <div class="alert alert-warning text-center my-3">
                @if(request('search'))
                No For Release document requests found matching your search criteria.
                @else
                No For Release document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable" style="font-size: 0.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th>
                            {{-- Uniform sorting link on Req # header --}}
                            <a href="{{ route('tables.index', array_merge(request()->except('page'), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th>Document</th>
                        <th title="School/Entity">School</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th title="For Release Date">Rel Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($DocRequests as $item)
                    <tr>
                        <td>{{ $item->req_no }}</td>
                        <td>{{ strtoupper($item->studentInformation->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ strtoupper($item->request_schl_entity) }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td><span class="badge bg-success text-white px-2 py-1">{{ $item->status }}</span></td>
                        <td>{{ $item->forRelease_date }}</td>
                        <td class="text-nowrap">
                            {{-- ACTION BUTTONS --}}
                            <button type="button" class="btn btn-success btn-sm complete-btn"
                                data-request-id="{{ $item->id }}"
                                data-request-no="{{ $item->req_no }}"
                                data-student-name="{{ $item->studentInformation->full_name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#claimerModal">
                                Claimed
                            </button>

                            @if(!empty($PermissionEdit))
                            <a href="{{ route('tables.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
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

{{-- Claimer Information Modal --}}
<div class="modal fade" id="claimerModal" tabindex="-1" aria-labelledby="claimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #1f2937;">
                <h5 class="modal-title" id="claimerModalLabel">
                    <i class="fas fa-user-check me-2"></i>Document Claim Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="claimerForm" action="{{ route('document-request3.complete', 0) }}" method="POST"
                data-swal-loading="true"
                data-swal-title="Releasing Document Request"
                data-swal-text="This may take a few seconds...">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-info d-none" id="modalRequestInfo">
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
                                <input type="text" class="form-control" id="claimerFirstName" name="claimer_first_name" required>
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
                                <input type="text" class="form-control" id="claimerLastName" name="claimer_last_name" required>
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
                    <button type="button" class="btn text-white" style="background-color: #1f2937;" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn text-white" style="background-color: #1dd3b0" id="submitClaimBtn">
                        <i class="fas fa-check me-1"></i>Mark as Claimed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ======= ELEMENT REFERENCES =======
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filterSelect = document.getElementById('filterSelect');
    const sortSelect = document.getElementById('sortSelect');
    const resetBtn = document.getElementById('resetBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const tableContainer = document.getElementById('tableContainer');
    const totalCount = document.getElementById('totalCount');
    let searchTimeout = null;

    // ======= INITIAL STATE =======
    toggleClearButton();
    attachCompleteButtonListeners();
    attachClearAllListener();

    // ======= CLEAR BUTTON VISIBILITY =======
    function toggleClearButton() {
        clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
    }

    // ======= SEARCH INPUT =======
    searchInput.addEventListener('input', function () {
        toggleClearButton();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performAjaxSearch(), 400);
    });

    // ======= CLEAR SEARCH BUTTON =======
    clearSearchBtn.addEventListener('click', function () {
        searchInput.value = '';
        toggleClearButton();
        performAjaxSearch();
    });

    // ======= FILTER AND SORT SELECTS =======
    [filterSelect, sortSelect].forEach(el => {
        el?.addEventListener('change', performAjaxSearch);
    });

    // ======= RESET BUTTON =======
    resetBtn.addEventListener('click', function () {
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

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTableContainer = doc.querySelector('#tableContainer');
                const newPagination = doc.querySelector('.pagination')?.parentElement;
                const newTotalCount = doc.querySelector('#totalCount');
                const newInfoBanner = doc.querySelector('.table-info-banner');
                const oldInfoBanner = document.querySelector('.table-info-banner');

                // Update table
                tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                // Update count
                if (newTotalCount) totalCount.textContent = newTotalCount.textContent;

                // Update info alert (table only, not modal)
                if (oldInfoBanner && newInfoBanner) {
                    oldInfoBanner.outerHTML = newInfoBanner.outerHTML;
                } else if (oldInfoBanner && !newInfoBanner) {
                    oldInfoBanner.remove();
                } else if (!oldInfoBanner && newInfoBanner) {
                    document.querySelector('.card-body').insertBefore(newInfoBanner, tableContainer);
                }

                // Update pagination
                const oldPagination = document.querySelector('.pagination')?.parentElement;
                if (oldPagination && newPagination) {
                    oldPagination.outerHTML = newPagination.outerHTML;
                } else if (oldPagination && !newPagination) {
                    oldPagination.remove();
                }

                attachCompleteButtonListeners();
                attachClearAllListener();

                loadingSpinner.style.display = 'none';
                tableContainer.style.opacity = '1';
            })
            .catch(err => {
                console.error('AJAX Search Error:', err);
                showErrorToast('Error loading table.');
                loadingSpinner.style.display = 'none';
                tableContainer.style.opacity = '1';
            });
    }

    // ======= CLEAR ALL BUTTON (AJAX) =======
    function attachClearAllListener() {
        const clearAllBtn = document.getElementById('clearAllBtn');
        if (!clearAllBtn) return;

        clearAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            tableContainer.innerHTML = `<div class="text-center my-4">
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;

            fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Replace table
                    const newTable = doc.querySelector('#tableContainer').innerHTML;
                    tableContainer.innerHTML = newTable;

                    // Remove info banner
                    const infoBanner = document.querySelector('.table-info-banner');
                    if (infoBanner) infoBanner.remove();

                    // Reset search/filter/sort fields
                    searchInput.value = '';
                    filterSelect.value = 'all';
                    sortSelect.value = 'default';
                    toggleClearButton();

                    // Update pagination
                    const newPagination = doc.querySelector('.pagination');
                    const oldPagination = document.querySelector('.pagination');
                    if (newPagination && oldPagination) oldPagination.outerHTML = newPagination.outerHTML;

                    attachCompleteButtonListeners();
                    attachClearAllListener();

                    showSuccessToast('All filters cleared!');
                })
                .catch(error => {
                    console.error('Clear All failed:', error);
                    showErrorToast('Failed to clear filters.');
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
        document.getElementById('modalRequestInfo').classList.remove('d-none');

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

    claimerModal?.addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalRequestInfo').classList.add('d-none');
        claimerForm.classList.remove('was-validated');
        claimerForm.querySelectorAll('.alert-danger, .alert-success').forEach(alert => {
            if (!alert.classList.contains('alert-info')) alert.remove();
        });
        setLoadingState(false);
    });

    // ======= CLAIMER FORM SUBMIT =======
    claimerForm.addEventListener('submit', function (e) {
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

    // ======= TOAST-STYLE ALERTS INSIDE MODAL =======
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

    // ======= ERROR ALERT (same style) =======
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


    // ======= SHORTCUTS =======
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
        }
    });
});
</script>



<style>
    /* CORE STYLES */
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

    .modal #tableSearchAlert {
        display: none !important;
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

    /* Table layout adjustments */
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

    /* Make remarks column wider and allow wrapping */
    #requestsTable th:nth-child(5),
    #requestsTable td:nth-child(5) {
        white-space: normal;
        min-width: 100px;
    }

    /* Checkbox styling */
    .thick-outline {
        width: 1.2rem;
        height: 1.2rem;
        border: 2.5px solid #1f2937 !important;
        accent-color: #1dd3b0;
        cursor: pointer;
    }

    .thick-outline:focus {
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.4);
    }

    /* Smooth transitions */
    .btn {
        transition: all 0.2s ease-in-out;
    }

    /* Ensure buttons maintain size during loading */
    .delete-btn,
    .complete-btn {
        min-width: 70px;
    }

    /* Modal styling */
    .modal-dialog {
        max-width: 600px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .alert-info {
        background-color: #e3f2fd;
        border-color: #1976d2;
        color: #1565c0;
    }
</style>

@endsection
