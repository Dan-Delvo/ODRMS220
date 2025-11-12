@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

{{-- Header Section --}}
<div class="row align-items-center">
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <h1 class="mt-4">
            <span class="badge page-title-badge" style="background-color: #1dd3b0;">Claimed Requests</span>
        </h1>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Claimed Requests</li>
        </ol>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <h1 class="mt-md-4">
            <span class="badge count-badge">Total: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

<x-tabs page='Claimed' />


{{-- Main Card --}}
{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header card-header-custom">
        <h5 class="mb-0">Claimed Document Requests</h5>

        {{-- Search/Filter Form --}}
        <div class="d-flex gap-2 flex-wrap align-items-center">
            {{-- Search Input --}}
            <div class="input-group" style="width: 300px;">
                <input type="text"
                    name="search"
                    id="searchInput"
                    class="form-control form-control-sm"
                    placeholder="Search claimed requests..."
                    value="{{ request('search') }}">
                <button class="btn btn-outline-light btn-sm"
                    type="button"
                    id="clearSearch"
                    title="Clear search"
                    style="display: none;"> {{-- Add this style --}}
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
                <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
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
                No claimed document requests found matching your search criteria.
                @else
                No claimed document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                <thead class="table-dark">
                    <tr>
                        <th class="sortable-header">
                            <a href="{{ route('claimed-documents.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th>Claimer</th>
                        <th>Remarks</th>
                        <th>Req Date</th>
                        <th>App Date</th>
                        <th>Rel Date</th>
                        <th>Claimed Date</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($DocRequests as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->req_no }}</td>
                        <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ strtoupper($item->request_schl_entity) }}</td>
                        <td>{{ ($item->claimer->Fname ?? '') . ' ' . ($item->claimer->Lname ?? '') }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td>{{ $item->request_date }}</td>
                        <td>{{ $item->approve_date }}</td>
                        <td>{{ $item->forRelease_date }}</td>
                        <td>
                            @if ($item->claimed_date)
                            <span class="badge bg-success text-white status-badge">
                                {{ \Carbon\Carbon::parse($item->claimed_date)->format('M d, Y') }}
                            </span>
                            @else
                            <span class="badge bg-secondary text-white status-badge">Not Claimed</span>
                            @endif
                        </td>
                        <td class="action-column">
                            <div class="btn-group-vertical btn-group-sm d-md-inline" role="group">
                                <button type="button" class="btn btn-warning btn-sm revert-btn mb-1"
                                    data-request-id="{{ $item->id }}" data-request-no="{{ $item->req_no }}"
                                    data-student-name="{{ $item->studentInformation->full_name }}"
                                    data-bs-toggle="modal" data-bs-target="#revertModal">
                                    <i class="fas fa-undo me-1"></i>Revert
                                </button>

                                @if (!empty($PermissionEdit))
                                <a href="{{ route('claimed-documents.edit', $item->id) }}"
                                    class="btn btn-info btn-sm mb-1">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @endif

                                @if (!empty($deleteClaimed))
                                <form action="{{ route('claimed-documents.destroy', $item->id) }}"
                                    method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-btn mb-1">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
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

{{-- Revert Modal --}}
<div class="modal fade" id="revertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>Revert Document to For Release
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="revertForm" action="{{ route('claimed-documents.revert', '') }}" method="POST"
                data-swal-loading="true" data-swal-title="Reverting Request to For Release"
                data-swal-text="This may take a few seconds...">
                @csrf
                @method('PUT')
                <div class=" modal-body">
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
                            placeholder="Please provide a reason for reverting this document to For Release status..."></textarea>
                        <div class="invalid-feedback">
                            Please provide a reason for reverting this document.
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This action will change the document status back to "For Release" and clear the claimed date.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitRevertBtn">
                        <i class="fas fa-undo me-1"></i>Revert to For Release
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const filterSelect = document.getElementById('filterSelect');
        const sortSelect = document.getElementById('sortSelect');
        const resetBtn = document.getElementById('resetBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableContainer = document.getElementById('tableContainer');
        const revertModal = document.getElementById('revertModal');
        const revertForm = document.getElementById('revertForm');
        const submitRevertBtn = document.getElementById('submitRevertBtn');
        const revertReason = document.getElementById('revertReason');
        let searchTimeout = null;

        // ====== INITIAL STATE ======
        toggleClearButton();
        attachEventListeners();

        // ====== CLEAR BUTTON VISIBILITY ======
        function toggleClearButton() {
            clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
        }

        // ====== AJAX SEARCH FUNCTION ======
        // ====== AJAX SEARCH FUNCTION ======
        // ====== AJAX SEARCH FUNCTION ======
        function performAjaxSearch() {
            const search = searchInput.value.trim();
            const filter = filterSelect.value;
            const sort = sortSelect.value;

            loadingSpinner.style.display = 'block';
            tableContainer.style.opacity = '0.5';

            const url = `{{ route('claimed-documents.index') }}?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

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

                    // Update table
                    tableContainer.innerHTML = newTableContainer ? newTableContainer.innerHTML : '<div class="alert alert-warning text-center my-3">No results found.</div>';

                    // Update info banner
                    const oldInfoBanner = document.querySelector('.table-info-banner');
                    if (oldInfoBanner) oldInfoBanner.remove();

                    if (newInfoBanner) {
                        const cardBody = document.querySelector('.card-body.bg-light');
                        cardBody.insertBefore(newInfoBanner, cardBody.firstChild);
                    }

                    // Update pagination - FIXED THIS PART
                    const paginationContainer = document.querySelector('#paginationContainer');
                    if (paginationContainer && newPaginationWrapper) {
                        paginationContainer.innerHTML = newPaginationWrapper.innerHTML;
                    } else if (paginationContainer && !newPaginationWrapper) {
                        paginationContainer.innerHTML = ''; // Clear pagination if no results
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

        // ====== AJAX PAGINATION HANDLER ======
        function attachPaginationListeners() {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.href;

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

                            // Update pagination - FIXED THIS PART
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

                            attachEventListeners();
                        })
                        .catch(err => {
                            console.error('AJAX Pagination Error:', err);
                            loadingSpinner.style.display = 'none';
                            tableContainer.style.opacity = '1';
                        });
                });
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
        function attachPaginationListeners() {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.href;

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
                            const newPaginationWrapper = doc.querySelector('.d-flex.flex-column.justify-content-center.align-items-center.mt-3');

                            // Update table
                            tableContainer.innerHTML = newTableContainer ?
                                newTableContainer.innerHTML :
                                '<div class="alert alert-warning text-center my-3">No results found.</div>';

                            // Update pagination
                            const oldPagination = document.querySelector('.d-flex.flex-column.justify-content-center.align-items-center.mt-3');
                            if (oldPagination && newPaginationWrapper) {
                                oldPagination.innerHTML = newPaginationWrapper.innerHTML;
                            }

                            loadingSpinner.style.display = 'none';
                            tableContainer.style.opacity = '1';

                            // Scroll to top of card
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
                });
            });
        }

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
            attachPaginationListeners();
            attachClearAllListener();

            // Revert buttons
            document.querySelectorAll('.revert-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const requestId = this.getAttribute('data-request-id');
                    const requestNo = this.getAttribute('data-request-no');
                    const studentName = this.getAttribute('data-student-name');

                    document.getElementById('modalRevertRequestNo').textContent = requestNo;
                    document.getElementById('modalRevertStudentName').textContent = studentName;

                    revertForm.action = `{{ route('claimed-documents.index') }}/${requestId}/revert`;
                    revertForm.reset();
                    revertForm.classList.remove('was-validated');

                    revertForm.querySelectorAll('.alert-danger, .alert-success').forEach(a => a.remove());
                    setRevertLoadingState(false);
                });
            });

            // Delete forms
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.delete-btn');
                    if (confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
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
        }

        // ====== REVERT FORM SUBMISSION ======
        revertForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!revertForm.checkValidity()) {
                e.stopPropagation();
                revertForm.classList.add('was-validated');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                showRevertError('Security token not found. Please refresh the page and try again.');
                return;
            }

            const revertReasonValue = document.getElementById('revertReason').value;
            const formData = new URLSearchParams();
            formData.append('_method', 'PUT');
            formData.append('revert_reason', revertReasonValue);

            // Show Swal loading
            Swal.fire({
                title: 'Reverting Document',
                text: 'Please wait while the document is being reverted...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(revertForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                })
                .then(async response => {
                    if (!response.ok) {
                        let errorMessage = 'An error occurred while processing the request.';

                        try {
                            const errorData = await response.json();
                            if (errorData.message) errorMessage = errorData.message;
                            else if (errorData.errors) errorMessage = Object.values(errorData.errors).flat().join(', ');
                        } catch {
                            const errorText = await response.text();
                            if (errorText.includes('419')) {
                                errorMessage = 'Session expired. Please refresh and try again.';
                            }
                        }

                        throw new Error(errorMessage);
                    }

                    const contentType = response.headers.get('content-type');
                    let result = {};
                    if (contentType && contentType.includes('application/json')) {
                        result = await response.json();
                    }

                    // After successful revert
                    setTimeout(() => {
                        Swal.close();
                        const modal = bootstrap.Modal.getInstance(revertModal);
                        modal.hide();

                        // Clean up backdrop & body state
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');

                        // Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Reverted Successfully!',
                            text: result.message || 'Document reverted successfully!',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        performAjaxSearch(); // Reload table data
                    }, 3000);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });
                });
        });

        // ====== UTILITIES ======
        function setRevertLoadingState(isLoading) {
            const formInputs = revertForm.querySelectorAll('input, textarea, button');
            if (isLoading) {
                submitRevertBtn.disabled = true;
                submitRevertBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
                formInputs.forEach(input => input.disabled = true);
            } else {
                submitRevertBtn.disabled = false;
                submitRevertBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Revert to For Release';
                formInputs.forEach(input => input.disabled = false);
            }
        }

        function showRevertError(message) {
            let errorAlert = document.getElementById('modalRevertErrorAlert');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.id = 'modalRevertErrorAlert';
                errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                errorAlert.innerHTML = `
                <strong>Error:</strong> <span id="revertErrorMessage"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
                revertForm.querySelector('.modal-body').insertBefore(errorAlert, revertForm.querySelector('.modal-body').firstChild);
            }
            document.getElementById('revertErrorMessage').textContent = message;
            errorAlert.style.display = 'block';
        }

        revertModal.addEventListener('hidden.bs.modal', function() {
            setRevertLoadingState(false);
            revertForm.classList.remove('was-validated');
            revertForm.querySelectorAll('.alert-danger, .alert-success').forEach(a => a.remove());
        });

        // Keyboard shortcut: Ctrl+F focuses search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput?.focus();
            }
        });

        // Auto-resize textarea
        revertReason?.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
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
        padding: 1rem 1.25rem;
    }

    @media (min-width: 768px) {
        .card-header-custom {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    .header-right-section {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
    }

    @media (min-width: 768px) {
        .header-right-section {
            width: auto;
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
        min-width: 150px;
        max-width: 250px;
    }

    @media (min-width: 768px) {
        .search-input-group {
            width: 180px;
            flex: 0 0 180px;
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
    #searchInput:focus {
        border-color: var(--success-color);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* ===== TABLE STYLES ===== */
    #requestsTable {
        font-size: 0.9rem;
        margin-bottom: 0;
        table-layout: auto;
        width: max-content;
        min-width: 100%;
    }

    #requestsTable thead th {
        white-space: nowrap;
        vertical-align: middle;
        font-weight: 600;
        padding: 0.75rem 0.75rem;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    #requestsTable tbody td {
        vertical-align: middle;
        padding: 0.75rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        white-space: nowrap;
    }

    .sortable-header a {
        transition: opacity 0.2s;
    }

    .sortable-header a:hover {
        opacity: 0.8;
    }

    /* ===== ACTION COLUMN ===== */
    .action-column {
        min-width: 220px !important;
        max-width: 220px !important;
        width: 220px !important;
        white-space: normal !important;
    }

    .btn-group-vertical {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 0.3rem !important;
        width: 100% !important;
    }

    .action-column .btn {
        padding: 0.4rem 0.65rem !important;
        font-size: 0.8rem !important;
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
        display: inline-block !important;
        text-align: center !important;
        margin-bottom: 0 !important;
    }

    .action-column .btn i {
        font-size: 0.8rem !important;
    }

    /* ===== STATUS BADGE ===== */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        white-space: nowrap;
        font-weight: 500;
    }

    /* ===== BUTTON STATES ===== */
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    .btn-sm {
        font-size: 0.8rem;
        padding: 0.35rem 0.65rem;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15rem;
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

    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }

    /* ===== MODAL STYLES ===== */
    .modal-dialog {
        max-width: 600px;
    }

    .modal-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .modal-header.bg-info {
        background-color: var(--info-color) !important;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control:focus {
        border-color: var(--success-color);
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    input[type="date"]:focus {
        border-color: var(--info-color);
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    .was-validated .form-control:invalid {
        border-color: var(--danger-color);
    }

    .was-validated .form-control:valid {
        border-color: var(--success-color);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: var(--danger-color);
    }

    #revertReason {
        resize: vertical;
        min-height: 80px;
        max-height: 200px;
    }

    /* ===== RESPONSIVE TABLE ===== */
    .table-responsive {
        border-radius: 0.25rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    @media (max-width: 576px) {
        #requestsTable {
            font-size: 0.8rem;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.5rem 0.5rem;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.3rem 0.5rem;
        }

        .btn-outline-light {
            width: 100%;
        }
    }

    /* ===== PAGINATION ===== */
    .pagination {
        margin-bottom: 0;
    }

    /* ===== SMOOTH TRANSITIONS ===== */
    .btn,
    .form-control,
    .form-select,
    .modal-body input,
    .modal-body select,
    .modal-body textarea {
        transition: all 0.2s ease-in-out;
    }

    /* ===== UTILITY CLASSES ===== */
    .fw-semibold {
        font-weight: 600;
    }
</style>

@endsection
