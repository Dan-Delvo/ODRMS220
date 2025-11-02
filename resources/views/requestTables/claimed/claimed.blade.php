@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

{{-- Header Section --}}
<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Claimed Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Claimed Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;" id="totalCountBadge">Total: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

<ul class="nav nav-tabs" data-bs-theme="dark">
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('pending.index') }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('ongoing.index') }}">Processing</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('tables.index') }}">For Release</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="{{ route('claimed-documents.index') }}">Claimed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="{{ route('declined-documents.index') }}">Declined</a>
    </li>
</ul>

{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
        style="background-color: #1f2937;">
        <h5 class="mb-2 mb-md-0">Claimed Document Requests</h5>

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
                <option value="claimer" {{ request('filter') == 'claimer' ? 'selected' : '' }}>Claimer</option>
            </select>

            {{-- Sort Dropdown --}}
            <select name="sort" id="sortSelect" class="form-select form-select-sm" style="width: auto;">
                <option value="default" {{ request('sort', 'default') == 'default' ? 'selected' : '' }}>Default Order</option>
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
        <div id="searchInfoBanner" style="display: none;" class="alert alert-info mb-3 py-2">
            <small>
                <i class="fas fa-search me-1"></i>
                <span id="searchInfoText"></span>
                <button type="button" class="btn btn-sm btn-outline-info ms-2" id="clearFiltersBtn">Clear All</button>
            </small>
        </div>

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
                No claimed document requests found matching your search criteria.
                @else
                No claimed document requests found.
                @endif
            </div>
            @else
            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                <thead class="table-dark">
                    <tr>
                        <th>Req #</th>
                        <th>Student</th>
                        <th>Doc</th>
                        <th>School</th>
                        <th>Claimer</th>
                        <th>Remarks</th>
                        <th>Req Date</th>
                        <th>App Date</th>
                        <th>Rel Date</th>
                        <th>Claimed Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($DocRequests as $item)
                    <tr>
                        <td>{{ $item->req_no }}</td>
                        <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                        <td>{{ $item->documents->DocType }}</td>
                        <td>{{ strtoupper($item->request_schl_entity)}}</td>
                        <td>{{ ($item->claimer->Fname ?? '') . ' ' . ($item->claimer->Lname ?? '') }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td>{{ $item->request_date }}</td>
                        <td>{{ $item->approve_date }}</td>
                        <td>{{ $item->forRelease_date }}</td>
                        <td>
                            @if($item->claimed_date)
                            <span class="badge bg-success text-white">
                                {{ \Carbon\Carbon::parse($item->claimed_date)->format('M d, Y') }}
                            </span>
                            @else
                            <span class="badge bg-secondary text-white">Not Claimed</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            {{-- Revert Button --}}
                            <button type="button"
                                class="btn btn-warning btn-sm revert-btn"
                                data-request-id="{{ $item->id }}"
                                data-request-no="{{ $item->req_no }}"
                                data-student-name="{{ $item->studentInformation->full_name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#revertModal">
                                Revert
                            </button>

                            @if(!empty($PermissionEdit))
                            <a href="{{ route('claimed-documents.edit', $item->id) }}" class="btn btn-info btn-sm">Edit</a>
                            @endif

                            @if(!empty($deleteClaimed))
                            <form action="{{ route('claimed-documents.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>
                            </form>
                            @endif
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
            <form id="revertForm" method="POST">
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
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        const searchInfoBanner = document.getElementById('searchInfoBanner');
        const searchInfoText = document.getElementById('searchInfoText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableContainer = document.getElementById('tableContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const totalCountBadge = document.getElementById('totalCountBadge');
        const revertModal = document.getElementById('revertModal');
        const revertForm = document.getElementById('revertForm');
        const submitRevertBtn = document.getElementById('submitRevertBtn');
        const revertReason = document.getElementById('revertReason');
        let searchTimeout = null;

        // ====== SWEETALERT HELPERS ======
        function showSwalLoading(title = 'Processing...', text = 'Please wait...') {
            Swal.fire({
                title: title,
                text: text,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
        }

        function closeSwalLoading() {
            Swal.close();
        }

        // ====== LOAD DATA VIA AJAX ======
        function loadData(page = 1) {
            const params = new URLSearchParams({
                search: searchInput.value,
                filter: filterSelect.value,
                sort: sortSelect.value,
                page: page
            });

            loadingSpinner.style.display = 'block';
            tableContainer.style.opacity = '0.5';
            paginationContainer.style.opacity = '0.5';

            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({}, '', newUrl);
            updateSearchInfo();

            fetch(`{{ route('claimed-documents.index') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTableContent = doc.querySelector('#tableContainer');
                if (newTableContent) tableContainer.innerHTML = newTableContent.innerHTML;

                const newPaginationContent = doc.querySelector('#paginationContainer');
                if (newPaginationContent) paginationContainer.innerHTML = newPaginationContent.innerHTML;

                const newTotalCount = doc.querySelector('#totalCountBadge');
                if (newTotalCount) totalCountBadge.textContent = newTotalCount.textContent;

                loadingSpinner.style.display = 'none';
                tableContainer.style.opacity = '1';
                paginationContainer.style.opacity = '1';

                attachEventListeners();
            })
            .catch(error => {
                console.error('Error loading data:', error);
                loadingSpinner.style.display = 'none';
                tableContainer.style.opacity = '1';
                paginationContainer.style.opacity = '1';
                Swal.fire('Error', 'Failed to load table data.', 'error');
            });
        }

        // ====== UPDATE SEARCH INFO ======
        function updateSearchInfo() {
            const searchValue = searchInput.value.trim();
            const filterValue = filterSelect.value;
            const sortValue = sortSelect.value;

            if (searchValue || filterValue !== 'all' || sortValue !== 'default') {
                let infoText = '';

                if (searchValue) {
                    infoText += `Showing results for: <strong>"${searchValue}"</strong>`;
                    if (filterValue !== 'all') {
                        infoText += ` in <strong>${filterValue.charAt(0).toUpperCase() + filterValue.slice(1)}</strong>`;
                    }
                }

                if (sortValue !== 'default') {
                    if (infoText) infoText += ' - ';
                    infoText += `Sorted by <strong>Request No. (${sortValue === 'asc' ? 'A-Z' : 'Z-A'})</strong>`;
                }

                searchInfoText.innerHTML = infoText;
                searchInfoBanner.style.display = 'block';
            } else {
                searchInfoBanner.style.display = 'none';
            }
        }

        // ====== SEARCH / FILTER / SORT ======
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                    loadData();
                }
            }, 500);
        });

        filterSelect.addEventListener('change', () => loadData());
        sortSelect.addEventListener('change', () => loadData());
        clearSearchBtn.addEventListener('click', () => { searchInput.value = ''; loadData(); });
        resetBtn.addEventListener('click', () => { searchInput.value = ''; filterSelect.value = 'all'; sortSelect.value = 'default'; loadData(); });
        clearFiltersBtn.addEventListener('click', () => { searchInput.value = ''; filterSelect.value = 'all'; sortSelect.value = 'default'; loadData(); });

        // ====== EVENT LISTENERS FOR NEW ELEMENTS ======
        function attachEventListeners() {
            document.querySelectorAll('#paginationContainer a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page') || 1;
                    loadData(page);
                });
            });

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

            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.delete-btn');
                    if (confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
                        const row = this.closest('tr');
                        row.querySelectorAll('button, a.btn').forEach(b => {
                            if (b !== btn) { b.disabled = true; b.style.opacity = '0.5'; }
                        });
                        setTimeout(() => this.submit(), 100);
                    }
                });
            });
        }

        // ====== REVERT FORM SUBMISSION ======
        // Revert form submission
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

            const revertReason = document.getElementById('revertReason').value;
            const formData = new URLSearchParams();
            formData.append('_method', 'PUT');
            formData.append('revert_reason', revertReason);

            // 🌀 Show Swal loading
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

                // ✅ After successful revert
                setTimeout(() => {
                    Swal.close(); // Close loading
                    const modal = bootstrap.Modal.getInstance(revertModal);
                    modal.hide(); // Hide modal exactly when loading ends

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

                    loadData(); // Reload table data
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

        function showRevertSuccess(message) {
            let successAlert = document.getElementById('modalRevertSuccessAlert');
            if (!successAlert) {
                successAlert = document.createElement('div');
                successAlert.id = 'modalRevertSuccessAlert';
                successAlert.className = 'alert alert-success fade show';
                successAlert.innerHTML = `<i class="fas fa-check-circle me-2"></i><span id="revertSuccessMessage"></span>`;
                revertForm.querySelector('.modal-body').insertBefore(successAlert, revertForm.querySelector('.modal-body').firstChild);
            }
            document.getElementById('revertSuccessMessage').textContent = message;
            successAlert.style.display = 'block';
        }

        revertModal.addEventListener('hidden.bs.modal', function() {
            setRevertLoadingState(false);
            revertForm.classList.remove('was-validated');
            revertForm.querySelectorAll('.alert-danger, .alert-success').forEach(a => a.remove());
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput?.focus();
            }
        });

        revertReason.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        attachEventListeners();
        updateSearchInfo();
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
    #searchInput:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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

    /* Action buttons */
    .table td.text-nowrap .btn-sm {
        margin: 1px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .delete-btn,
    .revert-btn {
        min-width: 70px;
    }

    /* Table layout */
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

    /* Make remarks column wider */
    #requestsTable th:nth-child(9),
    #requestsTable td:nth-child(9) {
        white-space: normal;
        min-width: 100px;
    }

    /* Modal styling */
    .modal-dialog {
        max-width: 600px;
    }

    .modal-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .text-danger {
        font-weight: bold;
    }

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

    .was-validated .form-control:invalid {
        border-color: #dc3545;
    }

    .was-validated .form-control:valid {
        border-color: #28a745;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    /* Textarea styling */
    #revertReason {
        resize: vertical;
        min-height: 80px;
        max-height: 200px;
    }

    /* Badge styling */
    .badge.bg-success,
    .badge.bg-secondary {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    /* Report modal */
    .modal-header.bg-info {
        background-color: #17a2b8 !important;
    }

    input[type="date"]:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }

        #searchForm {
            width: 100%;
        }

        #searchForm .input-group,
        #searchForm select {
            width: 100% !important;
            margin-bottom: 0.5rem;
        }

        .table-responsive {
            font-size: 0.75rem;
        }

        .btn-outline-light {
            width: 100%;
        }
    }

    /* Smooth transitions */
    .modal-body input,
    .modal-body select,
    .modal-body textarea {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Dropdown menu */
    .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
    }

    .filter-option:hover,
    .sort-option:hover {
        background-color: #f8f9fa;
    }

    .filter-option.active,
    .sort-option.active {
        background-color: #28a745;
        color: white;
    }
</style>

@endsection
