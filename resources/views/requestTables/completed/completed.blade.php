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
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total For Release: {{ $totalCount }}</span>
        </h1>
    </div>
</div>

{{-- Main Card --}}
<div class="card shadow-lg border-0 rounded-lg mt-3">
    {{-- Card Header with Search/Filter Controls (UPDATED for uniformity) --}}
    <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
         style="background-color: #1f2937;">
        <h5 class="mb-2 mb-md-0">For Release Document Requests</h5>

        {{-- Search/Filter Form (Uniform Structure) --}}
        <form method="GET" action="{{ route('tables.index') }}" id="searchForm">
            <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
                {{-- Search Input --}}
                <div class="input-group" style="width: 300px;">
                    <input type="text"
                           name="search"
                           id="searchInput"
                           class="form-control form-control-sm"
                           placeholder="Search requests..."
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
                </select>

                {{-- Sort Dropdown --}}
                <select name="sort" id="sortSelect" class="form-select form-select-sm" style="width: auto;">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default Order</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Req No. (A-Z)</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Req No. (Z-A)</option>
                </select>

                {{-- Search Button --}}
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
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
                <a href="{{ route('tables.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
            </small>
        </div>
        @endif

        {{-- Loading Spinner (Kept for visual feedback on form submit) --}}
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
                        <a href="{{ route('tables.index') }}" class="btn btn-sm btn-outline-warning ms-2">Clear Search</a>
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
                            <td>{{ $item->studentInformation->full_name }}</td>
                            <td>{{ $item->documents->DocType }}</td>
                            <td>{{ $item->request_schl_entity }}</td>
                            <td>{{ $item->remarks }}</td>
                            <td><span class="badge bg-success text-white px-2 py-1">{{ $item->status }}</span></td>
                            <td>{{ $item->forRelease_date }}</td>
                            <td class="text-nowrap">
                                {{-- ACTION BUTTONS (Kept from original file) --}}
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

                                @if(!empty($deleteCompleted))
                                <form action="{{ route('tables.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-swal-loading="true" data-swal-delete="true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1 delete-btn">Delete</button>
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

{{-- Claimer Information Modal (KEPT AS IS) --}}
<div class="modal fade" id="claimerModal" tabindex="-1" aria-labelledby="claimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="claimerModalLabel">
                    <i class="fas fa-user-check me-2"></i>Document Claim Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="claimerForm" action="{{ route('document-request3.complete', '') }}" method="POST"
                data-swal-loading="true"
                data-swal-title="The request is being claim"
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="submitClaimBtn">
                        <i class="fas fa-check me-1"></i>Mark as Claimed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript (UPDATED to use form-based search logic and keep Claimer Modal functionality) --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Uniform Search/Filter/Sort Logic (Server-Side) ---

    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filterSelect = document.getElementById('filterSelect');
    const sortSelect = document.getElementById('sortSelect');

    // Auto-submit form on filter/sort change
    filterSelect?.addEventListener('change', function() {
        searchForm.submit();
    });

    sortSelect?.addEventListener('change', function() {
        // When sorting, the page number should be reset to 1
        const url = new URL(searchForm.action);
        const params = new URLSearchParams(url.search);

        params.set('sort', this.value);
        params.delete('page'); // Remove existing page parameter
        url.search = params.toString();

        searchForm.action = url.toString();
        searchForm.submit();
    });

    // Clear search button
    clearSearchBtn?.addEventListener('click', function() {
        // Only clear if search is active, otherwise perform full clear
        if (searchInput.value || '{{ request('filter') }}' != 'all' || '{{ request('sort') }}' != 'default') {
            window.location.href = '{{ route("tables.index") }}';
        } else {
            searchInput.value = ''; // Just clear input if nothing else is filtered
        }
    });

    // Show loading spinner on form submit
    searchForm?.addEventListener('submit', function() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('tableContainer').style.opacity = '0.5';
    });

    // --- Original Claimer Modal Logic (Client-Side) ---

    const sameAsStudentCheckbox = document.getElementById('sameAsStudent');
    const claimerFirstName = document.getElementById('claimerFirstName');
    const claimerLastName = document.getElementById('claimerLastName');
    const claimerDate = document.getElementById('claimerDate');
    const claimerModal = document.getElementById('claimerModal');

    function fillClaimerInfo() {
        const studentName = document.getElementById('modalStudentName').textContent.trim();
        if (sameAsStudentCheckbox.checked) {
            if (studentName) {
                const nameParts = studentName.split(' ');
                claimerFirstName.value = nameParts.slice(0, -1).join(' ');
                claimerLastName.value = nameParts[nameParts.length - 1] || '';

                claimerFirstName.setAttribute('readonly', true);
                claimerLastName.setAttribute('readonly', true);
            }
        } else {
            claimerFirstName.value = '';
            claimerLastName.value = '';

            claimerFirstName.removeAttribute('readonly');
            claimerLastName.removeAttribute('readonly');
        }
    }

    sameAsStudentCheckbox.addEventListener('change', fillClaimerInfo);

    // Handle Claimed button clicks - populate modal
    document.querySelectorAll('.complete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const requestNo = this.getAttribute('data-request-no');
            const studentName = this.getAttribute('data-student-name');

            document.getElementById('modalRequestNo').textContent = requestNo;
            document.getElementById('modalStudentName').textContent = studentName;

            const form = document.getElementById('claimerForm');
            // Assuming your route is named 'document-request3.complete' and accepts the ID
            form.action = `{{ url('document-request3/complete') }}/${requestId}`;

            // Reset form state
            form.reset();
            form.classList.remove('was-validated');
            sameAsStudentCheckbox.checked = false; // Reset checkbox

            // Clear name fields and remove readonly
            claimerFirstName.value = '';
            claimerLastName.value = '';
            claimerFirstName.removeAttribute('readonly');
            claimerLastName.removeAttribute('readonly');

            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            claimerDate.value = today;

            // Remove previous error/success alerts
            document.getElementById('claimerForm').querySelectorAll('.alert-danger, .alert-success').forEach(alert => {
                if (!alert.classList.contains('alert-info')) {
                    alert.remove();
                }
            });

            setLoadingState(false);
        });
    });

    // Reset modal state when hidden (cleanup)
    claimerModal?.addEventListener('hidden.bs.modal', function() {
        const claimerForm = document.getElementById('claimerForm');
        claimerForm.classList.remove('was-validated');

        // Clean up alerts
        document.getElementById('claimerForm').querySelectorAll('.alert-danger, .alert-success').forEach(alert => {
            if (!alert.classList.contains('alert-info')) {
                alert.remove();
            }
        });
        setLoadingState(false);
    });

    // IMPROVED CLAIMER FORM SUBMISSION (Kept with necessary cleanup/fixes)
    const claimerForm = document.getElementById('claimerForm');
    const submitClaimBtn = document.getElementById('submitClaimBtn');

    claimerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!claimerForm.checkValidity()) {
            e.stopPropagation();
            claimerForm.classList.add('was-validated');
            return;
        }

        // Ensure date is set before submission
        if (!claimerDate.value) {
            claimerDate.value = new Date().toISOString().split('T')[0];
        }

        setLoadingState(true);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const actionUrl = claimerForm.action;
        const formData = new FormData(claimerForm);

        // Submit using fetch
        fetch(actionUrl, {
                method: 'POST', // Use POST for form submission
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: (() => {
                    formData.append('_method', 'PUT'); // Add method spoofing
                    return formData;
                })()
            })
            .then(async response => {
                if (!response.ok) {
                    let errorMessage = 'An error occurred while processing the request.';
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        } else if (errorData.errors) {
                            const errors = Object.values(errorData.errors).flat();
                            errorMessage = errors.join(', ');
                        }
                    } catch (e) {
                        errorMessage = await response.text();
                        if (errorMessage.includes('419')) {
                            errorMessage = 'Session expired. Please refresh the page.';
                        } else if (errorMessage.includes('404')) {
                            errorMessage = 'Request not found. Please refresh the page.';
                        }
                    }
                    throw new Error(errorMessage);
                }

                // Handle successful response
                let result = {};
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                }

                // Show success message if provided
                const successMsg = result.message || 'Document marked as claimed successfully!';
                showSuccess(successMsg);

                // Redirect or reload after short delay
                setTimeout(() => {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showError(error.message);
                setLoadingState(false);
            });
    });

    function setLoadingState(isLoading) {
        const formInputs = claimerForm.querySelectorAll('input, select, textarea, button');

        if (isLoading) {
            submitClaimBtn.disabled = true;
            submitClaimBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            Processing...
        `;
            formInputs.forEach(input => {
                if (input !== submitClaimBtn) {
                    input.disabled = true;
                }
            });
        } else {
            submitClaimBtn.disabled = false;
            submitClaimBtn.innerHTML = `
            <i class="fas fa-check me-1"></i>Mark as Claimed
        `;
            formInputs.forEach(input => {
                input.disabled = false;
            });
        }
    }

    function showError(message) {
        let errorAlert = document.getElementById('modalErrorAlert');
        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.id = 'modalErrorAlert';
            errorAlert.className = 'alert alert-danger alert-dismissible fade show';
            errorAlert.innerHTML = `
            <strong>Error:</strong> <span id="errorMessage"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
            claimerForm.querySelector('.modal-body').insertBefore(errorAlert, claimerForm.querySelector('.modal-body').firstChild);
        }

        document.getElementById('errorMessage').textContent = message;
        errorAlert.style.display = 'block';
        errorAlert.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    function showSuccess(message) {
        let successAlert = document.getElementById('modalSuccessAlert');
        if (!successAlert) {
            successAlert = document.createElement('div');
            successAlert.id = 'modalSuccessAlert';
            successAlert.className = 'alert alert-success fade show';
            successAlert.innerHTML = `
            <i class="fas fa-check-circle me-2"></i><span id="successMessage"></span>
        `;
            claimerForm.querySelector('.modal-body').insertBefore(successAlert, claimerForm.querySelector('.modal-body').firstChild);
        }

        document.getElementById('successMessage').textContent = message;
        successAlert.style.display = 'block';
    }


    // --- Original Delete Logic (Client-Side) ---

    const deleteForms = document.querySelectorAll(".delete-form");
    deleteForms.forEach(form => {
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const deleteBtn = form.querySelector(".delete-btn");

            if (confirm("Are you sure you want to delete this completed request? This action cannot be undone.")) {
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Deleting...
            `;

                const row = form.closest('tr');
                const allButtons = row.querySelectorAll('button, a.btn');
                allButtons.forEach(btn => {
                    if (btn !== deleteBtn) {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                    }
                });

                setTimeout(() => {
                    form.submit();
                }, 200);
            }
        });
    });

    // Reset button states on page show
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = 'Delete';
            });
            document.querySelectorAll('.complete-btn').forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = 'Claimed';
            });
        }
    });

    // --- Styling and Minor Helpers ---

    // Initial loading spinner simulation (removed for server-side search)
    // The spinner now only shows on form submit.

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    // Auto-capitalize name fields
    const nameFields = ['claimerFirstName', 'claimerLastName'];
    nameFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        field.addEventListener('input', function() {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });
    });
});
</script>

<style>
/* CORE STYLES FROM PENDING FILE FOR UNIFORMITY */
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

/* Make remarks column wider and allow wrapping only there */
#requestsTable th:nth-child(7),
#requestsTable td:nth-child(7) {
    white-space: normal;
    min-width: 100px;
}


/* Additional styling for better loading states (from original completed.blade.php) */
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

/* Smooth transitions for button states */
.btn {
    transition: all 0.2s ease-in-out;
}

/* Ensure buttons maintain their size during loading */
.delete-btn,
.complete-btn {
    min-width: 70px;
}

/* Modal styling enhancements */
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
