@extends('layout.blankpage')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    {{-- Header Section --}}
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h1 class="mt-4">
                <span class="badge page-title-badge" style="background-color: #28a745;">Claimed Requests</span>
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

            <div class="header-right-section">
                {{-- Search/Filter Form --}}
                <form method="GET" action="{{ route('claimed-documents.index') }}" id="searchForm" class="w-100 w-md-auto">
                    <div class="search-controls">
                        {{-- Search Input --}}
                        <div class="input-group search-input-group">
                            <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                                placeholder="Search..." value="{{ request('search') }}">
                            <button class="btn btn-outline-light btn-sm px-2" type="button" id="clearSearch" title="Clear">
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

                        {{-- Search Button --}}
                        <button type="submit" class="btn btn-light btn-sm search-btn px-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body bg-light">
            {{-- Search Info Banner --}}
            @if (request('search'))
                <div class="alert alert-info mb-3 py-2">
                    <small>
                        <i class="fas fa-search me-1"></i>
                        Showing results for: <strong>"{{ request('search') }}"</strong>
                        @if (request('filter') != 'all')
                            in <strong>{{ ucfirst(request('filter')) }}</strong>
                        @endif
                        @if (request('sort') != 'default')
                            - Sorted by <strong>Request No. ({{ request('sort') == 'asc' ? 'A-Z' : 'Z-A' }})</strong>
                        @endif
                        <a href="{{ route('claimed-documents.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear
                            All</a>
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
                            <a href="{{ route('claimed-documents.index') }}"
                                class="btn btn-sm btn-outline-warning ms-2">Clear Search</a>
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
                            <strong>Note:</strong> This action will change the document status back to "For Release" and
                            clear the claimed date.
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
                <form id="reportForm" action="{{ route('claimed-documents.report') }}" method="POST" target="_blank"
                    data-swal-loading="true" data-swal-title="Report Generating"
                    data-swal-text="This may take a few seconds...">
                    @csrf
                    <div class=" modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="startDate" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Start Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="startDate" name="start_date"
                                        required>
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
                            <strong>Note:</strong> This will generate a report for all documents claimed within the selected
                            date range.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-info"
                            formaction="{{ route('claimed-documents.report') }}">
                            <i class="fas fa-eye me-1"></i>View Report
                        </button>
                        <button type="submit" class="btn btn-success"
                            formaction="{{ route('claimed-documents.export-csv') }}">
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
                window.location.href = '{{ route('claimed-documents.index') }}';
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

                    revertForm.action = "{{ route('claimed-documents.index') }}" +
                        `/${requestId}/revert`;
                    revertForm.reset();
                    revertForm.classList.remove('was-validated');

                    // Clear alerts
                    const alerts = revertForm.querySelectorAll('.alert-danger, .alert-success');
                    alerts.forEach(alert => alert.remove());

                    setRevertLoadingState(false);
                });
            });

            // Revert form submission
            // Revert form submission
            revertForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!revertForm.checkValidity()) {
                    e.stopPropagation();
                    revertForm.classList.add('was-validated');
                    return;
                }

                setRevertLoadingState(true);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                'content');

                if (!csrfToken) {
                    showRevertError('Security token not found. Please refresh the page and try again.');
                    setRevertLoadingState(false);
                    return;
                }

                // Get the revert reason value directly
                const revertReason = document.getElementById('revertReason').value;

                // Create URLSearchParams instead of FormData
                const formData = new URLSearchParams();
                formData.append('_method', 'PUT');
                formData.append('revert_reason', revertReason);

                console.log('Sending data:', {
                    revert_reason: revertReason,
                    url: revertForm.action
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
                                console.log('Error response:', errorData); // Debug log

                                if (errorData.message) {
                                    errorMessage = errorData.message;
                                } else if (errorData.errors) {
                                    errorMessage = Object.values(errorData.errors).flat().join(
                                    ', ');
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
                        console.error('Fetch error:', error); // Debug log
                        showRevertError(error.message);
                        setRevertLoadingState(false);
                    });
            });

            // Helper functions
            function setRevertLoadingState(isLoading) {
                const formInputs = revertForm.querySelectorAll('input, textarea, button');

                if (isLoading) {
                    submitRevertBtn.disabled = true;
                    submitRevertBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
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
                    revertForm.querySelector('.modal-body').insertBefore(errorAlert, revertForm.querySelector(
                        '.modal-body').firstChild);
                }

                document.getElementById('revertErrorMessage').textContent = message;
                errorAlert.style.display = 'block';
                errorAlert.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }

            function showRevertSuccess(message) {
                let successAlert = document.getElementById('modalRevertSuccessAlert');
                if (!successAlert) {
                    successAlert = document.createElement('div');
                    successAlert.id = 'modalRevertSuccessAlert';
                    successAlert.className = 'alert alert-success fade show';
                    successAlert.innerHTML =
                        `<i class="fas fa-check-circle me-2"></i><span id="revertSuccessMessage"></span>`;
                    revertForm.querySelector('.modal-body').insertBefore(successAlert, revertForm.querySelector(
                        '.modal-body').firstChild);
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

                    if (confirm(
                            'Are you sure you want to delete this request? This action cannot be undone.'
                            )) {
                        btn.disabled = true;
                        btn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

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
            padding: 0.75rem 1rem;
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
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        #requestsTable thead th {
            white-space: nowrap;
            vertical-align: middle;
            font-weight: 600;
            padding: 0.3rem 0.3rem;
            font-size: 0.8rem;
            line-height: 1;
        }

        #requestsTable tbody td {
            vertical-align: middle;
            padding: 0.3rem 0.3rem;
            font-size: 0.8rem;
            line-height: 1;
        }

        .sortable-header a {
            transition: opacity 0.2s;
        }

        .sortable-header a:hover {
            opacity: 0.8;
        }

        /* ===== ACTION COLUMN ===== */
        .action-column {
            min-width: 200px !important;
            max-width: 200px !important;
            width: 200px !important;
            white-space: normal !important;
        }

        .btn-group-vertical {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            gap: 0.15rem !important;
            width: 100% !important;
        }

        .action-column .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            width: 95px !important;
            min-width: 95px !important;
            max-width: 95px !important;
            display: inline-block !important;
            text-align: center !important;
            margin-bottom: 0 !important;
        }

        .action-column .btn i {
            font-size: 0.75rem !important;
        }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            white-space: nowrap;
        }

        /* ===== BUTTON STATES ===== */
        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
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
        }

        @media (max-width: 576px) {
            #requestsTable {
                font-size: 0.75rem;
            }

            #requestsTable th,
            #requestsTable td {
                padding: 0.25rem 0.25rem;
            }

            .btn-sm {
                font-size: 0.65rem;
                padding: 0.2rem 0.3rem;
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
