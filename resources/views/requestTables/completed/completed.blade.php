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
            <span class="badge" style="background-color: #1f2937; font-size: 2rem;">Total For Release: {{ $totalCount }}</span>
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

                            <!-- @if(!empty($deleteCompleted))
                            <form action="{{ route('tables.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-swal-loading="true" data-swal-delete="true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1 delete-btn">Delete</button>
                            </form>
                            @endif -->
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
    document.addEventListener("DOMContentLoaded", function() {
        // --- Search/Filter/Sort Logic ---
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
            const url = new URL(searchForm.action);
            const params = new URLSearchParams(url.search);
            params.set('sort', this.value);
            params.delete('page');
            url.search = params.toString();
            searchForm.action = url.toString();
            searchForm.submit();
        });

        // Clear search button
        clearSearchBtn?.addEventListener('click', function() {
            if (searchInput.value || '{{ request('
                filter ') }}' != 'all' || '{{ request('
                sort ') }}' != 'default') {
                window.location.href = '{{ route("tables.index") }}';
            } else {
                searchInput.value = '';
            }
        });

        // Show loading spinner on form submit
        searchForm?.addEventListener('submit', function() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('tableContainer').style.opacity = '0.5';
        });

        // --- Claimer Modal Logic ---
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
                    claimerLastName.value = nameParts.pop() || ''; // Assume last word is last name
                    claimerFirstName.value = nameParts.join(' '); // Remainder is first name

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
                // Fixed to match the actual route: /tables/completeRequest/{id}
                form.action = `{{ url('tables/completeRequest') }}/${requestId}`;

                // Reset form state
                form.reset();
                form.classList.remove('was-validated');
                sameAsStudentCheckbox.checked = false;

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

        // Reset modal state when hidden
        claimerModal?.addEventListener('hidden.bs.modal', function() {
            const claimerForm = document.getElementById('claimerForm');
            claimerForm.classList.remove('was-validated');
            document.getElementById('claimerForm').querySelectorAll('.alert-danger, .alert-success').forEach(alert => {
                if (!alert.classList.contains('alert-info')) {
                    alert.remove();
                }
            });
            setLoadingState(false);
        });

        // CLAIMER FORM SUBMISSION
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

            // Ensure date is set
            if (!claimerDate.value) {
                claimerDate.value = new Date().toISOString().split('T')[0];
            }

            setLoadingState(true);

            // Get CSRF token
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                csrfToken = claimerForm.querySelector('input[name="_token"]')?.value;
            }

            if (!csrfToken) {
                console.error('CSRF token not found!');
                showError('Security token missing. Please refresh the page.');
                setLoadingState(false);
                return;
            }

            // --- FIX STARTS HERE: Prepare data as a JSON object ---
            const formDataObject = {
                claimer_first_name: claimerFirstName.value,
                claimer_last_name: claimerLastName.value,
                claimer_date: claimerDate.value,
                _method: 'PUT',
                _token: csrfToken // Include the token in the payload
            };

            const actionUrl = claimerForm.action;
            // --- FIX ENDS HERE ---

            // Submit using fetch
            fetch(actionUrl, {
                    method: 'POST', // Use POST with _method: 'PUT' override
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json', // CRITICAL FIX: Set Content-Type for JSON
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formDataObject), // CRITICAL FIX: Send data as JSON string
                    credentials: 'same-origin'
                })
                .then(async response => {
                    // Explicitly check for 419 session expired
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page (Error 419).');
                    }

                    if (!response.ok) {
                        let errorMessage = 'An error occurred while processing the request.';
                        try {
                            const errorData = await response.json();

                            if (errorData.message) {
                                errorMessage = errorData.message;
                            } else if (errorData.errors) {
                                const errors = Object.values(errorData.errors).flat();
                                errorMessage = 'Validation Failed: ' + errors.join('; ');
                            } else if (response.status === 404) {
                                errorMessage = 'Request not found. Please refresh the page (Error 404).';
                            }
                        } catch (e) {
                            errorMessage = `Network or server error (Status ${response.status}).`;
                        }
                        throw new Error(errorMessage);
                    }

                    // Handle successful response
                    let result = {};
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        result = await response.json();
                    }

                    const successMsg = result.message || 'Document marked as claimed successfully!';
                    showSuccess(successMsg);

                    // Redirect or reload after short delay
                    setTimeout(() => {
                        if (result.redirect) {
                            window.location.href = result.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
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
                // Insert error message after the alert-info box in the modal body
                claimerForm.querySelector('.modal-body').insertBefore(errorAlert, claimerForm.querySelector('.modal-body > .mb-3:first-child').nextSibling);
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
                // Insert success message after the alert-info box in the modal body
                claimerForm.querySelector('.modal-body').insertBefore(successAlert, claimerForm.querySelector('.modal-body > .mb-3:first-child').nextSibling);
            }

            document.getElementById('successMessage').textContent = message;
            successAlert.style.display = 'block';
        }

        // --- Delete Logic ---
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
            field?.addEventListener('input', function() {
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });
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

    #requestsTable th:nth-child(5),
    #requestsTable td:nth-child(5) {
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

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .delete-btn,
    .complete-btn {
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

    .modal-title {
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 1.125rem;
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

    /* ============= CHECKBOX STYLING ============= */
    .thick-outline {
        width: 1.2rem;
        height: 1.2rem;
        border: 2.5px solid #1f2937 !important;
        accent-color: #1dd3b0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .thick-outline:hover {
        border-color: #1dd3b0 !important;
        box-shadow: 0 0 0 2px rgba(29, 211, 176, 0.2);
    }

    .thick-outline:focus {
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.4);
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

        #requestsTable th:nth-child(5),
        #requestsTable td:nth-child(5) {
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

        #requestsTable th:nth-child(5),
        #requestsTable td:nth-child(5) {
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
</style>

@endsection
