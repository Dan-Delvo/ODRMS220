@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

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

<ul class="nav nav-tabs" data-bs-theme="dark">
  <li class="nav-item">
    <a class="nav-link text-dark" href="{{ route('pending.index') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link  text-dark" href="{{ route('ongoing.index') }}">Processing</a>
  </li>
  <li class="nav-item">
    <a class="nav-link  text-dark" href="{{ route('tables.index') }}">For Release</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="{{ route('claimed-documents.index') }}">Claimed</a>
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
            <form id="revertForm" action="{{ route('claimed-documents.revert', '') }}" method="POST"
                data-swal-loading="true"
                data-swal-title="Reverting Request to For Release"
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
                data-swal-loading="true"
                data-swal-title="Report Generating"
                data-swal-text="This may take a few seconds...">
                @csrf
                <div class=" modal-body">
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

                revertForm.action = "{{ route('claimed-documents.index') }}" + `/${requestId}/revert`;
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

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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
    /* ============= HEADER & BADGES ============= */
    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* ============= CARD HEADER & CONTROLS ============= */
    .card-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
        border-bottom: 3px solid #1dd3b0;
    }

    .card-header h5 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* ============= TABLE CONTROLS ============= */
    #tableControls {
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    #tableControls .input-group {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 0.375rem;
        overflow: hidden;
    }

    #searchInput,
    #searchInput:focus {
        border: 1px solid #e5e7eb;
        padding: 0.6rem 0.875rem;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    #searchInput:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15), inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    #searchInput::placeholder {
        color: #9ca3af;
    }

    #tableControls .btn-outline-light {
        border-width: 1.5px;
        border-color: rgba(255, 255, 255, 0.6);
        color: rgba(255, 255, 255, 0.9);
        padding: 0.6rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.9375rem;
    }

    #tableControls .btn-outline-light:hover {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: #1f2937;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    #tableControls .btn-light {
        background-color: #f3f4f6;
        border-color: #e5e7eb;
        color: #1f2937;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9375rem;
    }

    #tableControls .btn-light:hover {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    .form-select {
        padding: 0.6rem 0.875rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-select:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .form-select:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    /* ============= TABLE STYLES ============= */
    .table {
        margin-bottom: 0;
        font-size: 0.9375rem;
    }

    #requestsTable thead th {
        background-color: #1f2937;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.8125rem;
        padding: 0.875rem 1rem;
        border-color: #111827;
        vertical-align: middle;
    }

    #requestsTable thead th a {
        color: #1dd3b0;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    #requestsTable thead th a:hover {
        color: white;
        text-shadow: 0 0 8px rgba(29, 211, 176, 0.5);
    }

    #requestsTable tbody tr {
        transition: all 0.2s ease;
        border-color: #e5e7eb;
    }

    #requestsTable tbody tr:hover {
        background-color: #f9fafb;
        box-shadow: inset 0 1px 3px rgba(29, 211, 176, 0.1);
    }

    #requestsTable tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        color: #374151;
    }

    #requestsTable th:nth-child(9),
    #requestsTable td:nth-child(9) {
        white-space: normal;
        word-break: break-word;
        min-width: 120px;
    }

    /* ============= ACTION BUTTONS ============= */
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border-radius: 0.25rem;
    }

    .table .btn-sm {
        margin: 2px;
        white-space: nowrap;
    }

    .btn-primary {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #14a896;
        border-color: #14a896;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    .btn-danger {
        background-color: #dc2626;
        border-color: #dc2626;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
        border-color: #b91c1c;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: white;
        font-weight: 600;
    }

    .btn-warning:hover {
        background-color: #d97706;
        border-color: #d97706;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn-info {
        background-color: #0891b2;
        border-color: #0891b2;
        color: white;
        font-weight: 600;
    }

    .btn-info:hover {
        background-color: #0e7490;
        border-color: #0e7490;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .delete-btn,
    .revert-btn {
        min-width: 70px;
    }

    /* ============= LOADING STATE ============= */
    #loadingSpinner {
        padding: 2rem;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        color: #1dd3b0;
    }

    /* ============= TABLE CONTAINER ============= */
    #tableContainer {
        transition: opacity 0.3s ease;
        overflow-x: auto;
        border-radius: 0.375rem;
        background-color: white;
    }

    #tableContainer::-webkit-scrollbar {
        height: 8px;
    }

    #tableContainer::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    #tableContainer::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    #tableContainer::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ============= PAGINATION ============= */
    .pagination {
        margin-top: 1.5rem;
        gap: 0.25rem;
    }

    .pagination .page-link {
        color: #1dd3b0;
        border-color: #e5e7eb;
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
    }

    .pagination .page-item.active .page-link {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
        color: white;
    }

    /* ============= ALERTS ============= */
    .alert-info {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
    }

    .alert-warning {
        background-color: #fffbeb;
        border: 1px solid #fef08a;
        color: #92400e;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
    }

    /* ============= MODAL STYLES ============= */
    .modal-content {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        border-bottom: 2px solid #1dd3b0;
        padding: 1.25rem;
    }

    .modal-header.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        border-bottom: 2px solid #b45309 !important;
    }

    .modal-header.bg-info {
        background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%) !important;
        border-bottom: 2px solid #0c4a6e !important;
    }

    .modal-title {
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 1.125rem;
        color: white;
    }

    .modal-body {
        padding: 1.5rem;
        background-color: #fafafa;
    }

    .modal-footer {
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    /* ============= FORM CONTROLS IN MODALS ============= */
    .modal-body .form-control,
    .modal-body .form-select,
    .modal-body textarea {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus,
    .modal-body textarea:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .text-danger {
        font-weight: bold;
    }

    .was-validated .form-control:invalid {
        border-color: #dc3545;
    }

    .was-validated .form-control:valid {
        border-color: #1dd3b0;
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
        font-weight: 600;
    }

    input[type="date"]:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* ============= RESPONSIVE DESIGN ============= */
    @media (max-width: 1024px) {
        #requestsTable {
            font-size: 0.875rem;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.75rem 0.875rem;
        }

        #tableControls {
            gap: 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .card-header h5 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .card-header .d-flex {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }

        #tableControls {
            width: 100%;
            flex-direction: column;
            gap: 0.75rem;
        }

        #tableControls .input-group,
        #tableControls .form-select,
        #tableControls button {
            width: 100%;
        }

        .btn-sm {
            display: inline-block;
            padding: 0.5rem 0.625rem;
            font-size: 0.75rem;
            margin: 2px 1px;
        }

        .table {
            font-size: 0.8125rem;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.625rem 0.5rem;
            font-size: 0.75rem;
        }

        #requestsTable th:nth-child(9),
        #requestsTable td:nth-child(9) {
            min-width: 80px;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        #tableContainer {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .modal-body {
            padding: 1rem;
        }

        .modal-header {
            padding: 1rem;
        }

        .modal-title {
            font-size: 1rem;
        }

        .btn-outline-light {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .badge {
            font-size: 1.25rem;
        }

        .card-header h5 {
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        #tableControls {
            padding: 0;
        }

        #tableControls .input-group input,
        #tableControls .form-select,
        #tableControls button {
            font-size: 1rem;
            padding: 0.75rem;
        }

        .btn-sm {
            display: block;
            width: calc(50% - 3px);
            margin: 2px 1px;
            padding: 0.5rem 0.5rem;
        }

        .table {
            font-size: 0.75rem;
        }

        #requestsTable {
            width: max-content;
        }

        #requestsTable th,
        #requestsTable td {
            padding: 0.5rem 0.375rem;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        #requestsTable th:nth-child(9),
        #requestsTable td:nth-child(9) {
            white-space: normal;
            min-width: 60px;
        }

        .text-muted {
            font-size: 0.75rem;
        }

        .breadcrumb {
            font-size: 0.8rem;
        }

        .alert {
            padding: 0.75rem;
            font-size: 0.8rem;
        }
    }

    /* ============= UTILITY CLASSES ============= */
    .transition-all {
        transition: all 0.3s ease;
    }

    .shadow-lg {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }

    /* Smooth transitions */
    .btn {
        transition: all 0.2s ease-in-out;
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
        background-color: #1dd3b0;
        color: white;
    }
</style>

@endsection
