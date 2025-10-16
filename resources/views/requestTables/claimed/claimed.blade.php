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
            <span class="badge" style="background-color: #28a745; font-size: 2rem;">Claimed Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Claimed Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls --}}
    <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
         style="background-color: #1f2937;">
        <h5 class="mb-2 mb-md-0">Claimed Document Requests</h5>

        {{-- Search/Filter Form --}}
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">
                <i class="fas fa-chart-bar me-1"></i>Generate Report
            </button>

            <form method="GET" action="{{ route('claimed-documents.index') }}" id="searchForm" class="d-flex gap-2 flex-wrap">
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

                {{-- Search Button --}}
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="card-body bg-light">
        {{-- Search Info Banner --}}
        @if(request('search'))
        <div class="alert alert-info mb-3 py-2">
            <small>
                <i class="fas fa-search me-1"></i>
                Showing results for: <strong>"{{ request('search') }}"</strong>
                @if(request('filter') != 'all')
                    in <strong>{{ ucfirst(request('filter')) }}</strong>
                @endif
                @if(request('sort') != 'default')
                    - Sorted by <strong>Request No. ({{ request('sort') == 'asc' ? 'A-Z' : 'Z-A' }})</strong>
                @endif
                <a href="{{ route('claimed-documents.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
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
                        No claimed document requests found matching your search criteria.
                        <a href="{{ route('claimed-documents.index') }}" class="btn btn-sm btn-outline-warning ms-2">Clear Search</a>
                    @else
                        No claimed document requests found.
                    @endif
                </div>
            @else
                <table class="table table-bordered table-hover align-middle" id="requestsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <a href="{{ route('claimed-documents.index', array_merge(request()->all(), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                            <th>Via</th>
                            <th>Rel Mode</th>
                            <th>Claimer</th>
                            <th>Contact</th>
                            <th>Remarks</th>
                            <th>Req Date</th>
                            <th>App Date</th>
                            <th>Rel Date</th>
                            <th>Claimed Date</th>
                            <th>Claimed Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($DocRequests as $item)
                        <tr>
                            <td>{{ $item->req_no }}</td>
                            <td>{{ strtoupper(optional($item->studentInformation)->full_name) }}</td>
                            <td>{{ $item->documents->DocType }}</td>
                            <td>{{ $item->request_schl_entity }}</td>
                            <td>{{ $item->request_mode }}</td>
                            <td>{{ $item->release_mode }}</td>
                            <td>{{ ($item->claimer->Fname ?? '') . ' ' . ($item->claimer->Lname ?? '') }}</td>
                            <td>{{ $item->claimer->contact_no ?? 'N/A' }}</td>
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
                            <td>
                                @if($item->claimed_time)
                                    <span class="badge bg-success text-white">
                                        {{ \Carbon\Carbon::parse($item->claimed_time)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white">--:-- --</span>
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
            <form id="revertForm" action="{{ route('claimed-documents.revert', '') }}" method="POST">
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

{{-- Report Generation Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar me-2"></i>Generate Claimed Documents Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportForm" action="{{ route('claimed-documents.report') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Start Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endDate" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>End Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This will generate a report for all documents claimed within the selected date range.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-info" formaction="{{ route('claimed-documents.report') }}">
                        <i class="fas fa-eye me-1"></i>View Report
                    </button>
                    <button type="submit" class="btn btn-success" formaction="{{ route('claimed-documents.export-csv') }}">
                        <i class="fas fa-download me-1"></i>Export CSV
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
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filterSelect = document.getElementById('filterSelect');
    const sortSelect = document.getElementById('sortSelect');
    const revertModal = document.getElementById('revertModal');
    const revertForm = document.getElementById('revertForm');
    const submitRevertBtn = document.getElementById('submitRevertBtn');
    const reportForm = document.getElementById('reportForm');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    let searchTimeout = null;

    // Auto-submit form on filter/sort change
    filterSelect?.addEventListener('change', function() {
        searchForm.submit();
    });

    sortSelect?.addEventListener('change', function() {
        searchForm.submit();
    });

    // Clear search button
    clearSearchBtn?.addEventListener('click', function() {
        window.location.href = '{{ route("claimed-documents.index") }}';
    });

    // Auto-search with debounce
    searchInput?.addEventListener('input', function() {
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

    // Handle Revert button clicks
    document.querySelectorAll('.revert-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const requestNo = this.getAttribute('data-request-no');
            const studentName = this.getAttribute('data-student-name');

            document.getElementById('modalRevertRequestNo').textContent = requestNo;
            document.getElementById('modalRevertStudentName').textContent = studentName;

            revertForm.action = `{{ route('claimed-documents.revert', '') }}/${requestId}`;
            revertForm.reset();
            revertForm.classList.remove('was-validated');

            // Clear alerts
            const alerts = revertForm.querySelectorAll('.alert-danger, .alert-success');
            alerts.forEach(alert => alert.remove());

            setRevertLoadingState(false);
        });
    });

    // Revert form submission
    revertForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!revertForm.checkValidity()) {
            e.stopPropagation();
            revertForm.classList.add('was-validated');
            return;
        }

        setRevertLoadingState(true);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!csrfToken) {
            showRevertError('Security token not found. Please refresh the page and try again.');
            setRevertLoadingState(false);
            return;
        }

        const formData = new FormData(revertForm);
        formData.append('_method', 'PUT');

        fetch(revertForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async response => {
                if (!response.ok) {
                    let errorMessage = 'An error occurred while processing the request.';

                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        } else if (errorData.errors) {
                            errorMessage = Object.values(errorData.errors).flat().join(', ');
                        }
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

                showRevertSuccess(result.message || 'Document reverted successfully!');

                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(error => {
                showRevertError(error.message);
                setRevertLoadingState(false);
            });
    });

    // Helper functions
    function setRevertLoadingState(isLoading) {
        const formInputs = revertForm.querySelectorAll('input, textarea, button');

        if (isLoading) {
            submitRevertBtn.disabled = true;
            submitRevertBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
            formInputs.forEach(input => {
                if (input !== submitRevertBtn) input.disabled = true;
            });
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
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

    // Reset revert modal on hide
    revertModal.addEventListener('hidden.bs.modal', function() {
        setRevertLoadingState(false);
        revertForm.classList.remove('was-validated');
        const alerts = revertForm.querySelectorAll('.alert-danger, .alert-success');
        alerts.forEach(alert => alert.remove());
    });

    // Report form: Set default dates (current month)
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    if (!startDateInput.value) {
        startDateInput.value = firstDayOfMonth.toISOString().split('T')[0];
    }
    if (!endDateInput.value) {
        endDateInput.value = lastDayOfMonth.toISOString().split('T')[0];
    }

    // Validate date range
    function validateDateRange() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate > endDate) {
            endDateInput.setCustomValidity('End date must be after start date');
            return false;
        } else {
            endDateInput.setCustomValidity('');
            return true;
        }
    }

    startDateInput.addEventListener('change', validateDateRange);
    endDateInput.addEventListener('change', validateDateRange);

    reportForm.addEventListener('submit', function(e) {
        if (!validateDateRange()) {
            e.preventDefault();
            reportForm.classList.add('was-validated');
            return false;
        }
        reportForm.classList.remove('was-validated');
    });

    // Handle Delete button clicks
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

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput?.focus();
        }
    });

    // Auto-resize textarea
    document.getElementById('revertReason').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Re-enable buttons on page show
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.querySelectorAll('.delete-btn, .revert-btn').forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
                if (btn.classList.contains('delete-btn')) {
                    btn.innerHTML = 'Delete';
                } else if (btn.classList.contains('revert-btn')) {
                    btn.innerHTML = 'Revert';
                }
            });
        }
    });
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
