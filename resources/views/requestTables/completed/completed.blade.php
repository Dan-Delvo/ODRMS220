@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

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

<div class="row">
    <div class="col-md-12">

        @if(session('Status'))
        <div class="alert alert-success">
            {{ session('Status') }}
        </div>
        @endif

        @if(session('Danger'))
        <div class="alert alert-danger">
            {{ session('Danger') }}
        </div>
        @endif

        <div class="card shadow-lg border-0 rounded-lg mt-3">
            <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="background-color: #1f2937;">
                <h5 class="mb-2 mb-md-0">For Release Document Requests</h5>

                <!-- Search Bar -->
                <div class="search-container d-flex gap-2 mt-2 mt-md-0">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search requests..." style="border-radius: 0.375rem 0 0 0.375rem;">
                        <button class="btn btn-outline-light btn-sm" type="button" id="clearSearch" title="Clear search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">All Records</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="req-no">Request No.</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="student">Student Name</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="document">Document Type</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="school">School/Entity</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="status">Status</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body bg-light">
                <div id="spinner" class="text-center my-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Search Results Info -->
                <div id="searchInfo" class="mb-3" style="display: none;">
                    <div class="alert alert-info mb-2 py-2">
                        <small>
                            <i class="fas fa-search me-1"></i>
                            <span id="searchResultText">Showing all records</span>
                            <span id="searchQuery" class="fw-bold"></span>
                        </small>
                    </div>
                </div>

                <div class="table-responsive" id="requestTable">
                    @if($DocRequests->isEmpty())
                    <div class="alert alert-warning text-center my-3">
                        No For Release document requests found.
                    </div>
                    @else
                    <table class="table table-sm table-bordered table-hover align-middle text-nowrap" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th>Req No</th>
                                <th>Student</th>
                                <th>Document</th>
                                <th title="School/Entity">School</th>
                                <th title="Requested Via">Via</th>
                                <th title="Release Mode">Rel Mode</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th title="Request Date">Req Date</th>
                                <th title="Approved Date">App Date</th>
                                <th title="For Release Date">Rel Date</th>
                                <th title="Claimed Date">Clm Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach ($DocRequests as $item)
                            <tr class="table-row"
                                data-req-no="{{ strtolower($item->req_no) }}"
                                data-student="{{ strtolower($item->studentInformation->full_name) }}"
                                data-document="{{ strtolower($item->documents->DocType) }}"
                                data-school="{{ strtolower($item->request_schl_entity) }}"
                                data-via="{{ strtolower($item->request_mode) }}"
                                data-release-mode="{{ strtolower($item->release_mode) }}"
                                data-remarks="{{ strtolower($item->remarks) }}"
                                data-status="{{ strtolower($item->status) }}"
                                data-request-date="{{ $item->request_date }}"
                                data-approve-date="{{ $item->approve_date }}"
                                data-release-date="{{ $item->forRelease_date }}"
                                data-claimed-date="{{ $item->claimed_date }}">
                                <td>{{ $item->req_no }}</td>
                                <td>{{ $item->studentInformation->full_name }}</td>
                                <td>{{ $item->documents->DocType }}</td>
                                <td>{{ $item->request_schl_entity }}</td>
                                <td>{{ $item->request_mode }}</td>
                                <td>{{ $item->release_mode }}</td>
                                <td>{{ $item->remarks }}</td>
                                <td><span class="badge bg-success text-white px-2 py-1">{{ $item->status }}</span></td>
                                <td>{{ $item->request_date }}</td>
                                <td>{{ $item->approve_date }}</td>
                                <td>{{ $item->forRelease_date }}</td>
                                <td>{{ $item->claimed_date }}</td>
                                <td class="text-nowrap">
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
                                    <form action="{{ route('tables.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
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

                    <!-- No Results Message -->
                    <div id="noResults" class="alert alert-warning text-center my-3" style="display: none;">
                        <i class="fas fa-search me-2"></i>
                        No records found matching your search criteria.
                        <button class="btn btn-sm btn-outline-warning ms-2" onclick="clearSearch()">Clear Search</button>
                    </div>
                    @endif
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center mt-3" id="paginationContainer">
                    {{ $DocRequests->links() }}
                    <small class="text-muted">
                        Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Claimer Information Modal -->
<div class="modal fade" id="claimerModal" tabindex="-1" aria-labelledby="claimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="claimerModalLabel">
                    <i class="fas fa-user-check me-2"></i>Document Claim Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="claimerForm" action="{{ route('document-request3.complete', '') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>Request No:</strong> <span id="modalRequestNo"></span><br>
                            <strong>Student:</strong> <span id="modalStudentName"></span>
                        </div>
                    </div>

                    <!-- Checkbox: Claimer same as Student -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="sameAsStudent">
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
                        <label for="claimerContact" class="form-label">
                            <i class="fas fa-phone me-1"></i>Contact Number <span class="text-danger">*</span>
                        </label>
                        <input type="tel" class="form-control" id="claimerContact" name="claimer_contact" required
                            pattern="[0-9+\-\s\(\)]+" placeholder="e.g., +63 912 345 6789">
                        <div class="invalid-feedback">
                            Please provide a valid contact number.
                        </div>
                        <small class="form-text text-muted">Include country code if applicable</small>
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


{{-- Enhanced JavaScript with loading spinners and search functionality --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto-fill claimer info if same as student
        const sameAsStudentCheckbox = document.getElementById('sameAsStudent');
        const claimerFirstName = document.getElementById('claimerFirstName');
        const claimerLastName = document.getElementById('claimerLastName');
        const claimerContact = document.getElementById('claimerContact');

        function fillClaimerInfo() {
            if (sameAsStudentCheckbox.checked) {
                const studentName = document.getElementById('modalStudentName').textContent.trim();
                console.log('Student name:', studentName); // Debug log

                if (studentName) { // Check if studentName exists
                    const nameParts = studentName.split(' ');

                    if (nameParts.length === 1) {
                        // If only one word, assume it's first name
                        claimerFirstName.value = studentName;
                        claimerLastName.value = '';
                    } else {
                        // First name = everything except last word
                        claimerFirstName.value = nameParts.slice(0, -1).join(' ');
                        claimerLastName.value = nameParts[nameParts.length - 1];
                    }

                    // Lock fields so user can't accidentally edit
                    claimerFirstName.setAttribute('readonly', true);
                    claimerLastName.setAttribute('readonly', true);
                    claimerContact.setAttribute('readonly', true);
                }
            } else {
                // Reset fields when unchecked
                claimerFirstName.value = '';
                claimerLastName.value = '';
                claimerContact.value = '';

                claimerFirstName.removeAttribute('readonly');
                claimerLastName.removeAttribute('readonly');
                claimerContact.removeAttribute('readonly');
            }
        }

        sameAsStudentCheckbox.addEventListener('change', fillClaimerInfo);

        // Handle Claimed button clicks - populate modal (MOVED BEFORE RESET LISTENER)
        document.querySelectorAll('.complete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('Claimed button clicked'); // Debug log

                const requestId = this.getAttribute('data-request-id');
                const requestNo = this.getAttribute('data-request-no');
                const studentName = this.getAttribute('data-student-name');

                console.log('Request ID:', requestId, 'Request No:', requestNo, 'Student:', studentName); // Debug log

                // Populate modal content FIRST
                document.getElementById('modalRequestNo').textContent = requestNo;
                document.getElementById('modalStudentName').textContent = studentName;

                const form = document.getElementById('claimerForm');
                form.action = `{{ route('document-request3.complete', '') }}/${requestId}`;

                // Reset form state but keep the populated data
                form.reset();
                form.classList.remove('was-validated');

                // Reset checkbox state
                sameAsStudentCheckbox.checked = false;

                // Clear claimer fields
                claimerFirstName.value = '';
                claimerLastName.value = '';
                claimerContact.value = '';

                // Remove readonly attributes
                claimerFirstName.removeAttribute('readonly');
                claimerLastName.removeAttribute('readonly');
                claimerContact.removeAttribute('readonly');
            });
        });

        // Reset modal state when hidden (MOVED AFTER BUTTON LISTENERS)
        const claimerModal = document.getElementById('claimerModal');
        claimerModal.addEventListener('hidden.bs.modal', function() {
            console.log('Modal hidden'); // Debug log

            setLoadingState(false);
            const claimerForm = document.getElementById('claimerForm');
            claimerForm.classList.remove('was-validated');

            // Remove any error/success alerts
            const alerts = claimerForm.querySelectorAll('.alert-danger, .alert-success');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-info')) { // Don't remove the info alert with request details
                    alert.remove();
                }
            });

            // Reset claimer fields & checkbox (but don't clear the modal info)
            sameAsStudentCheckbox.checked = false;
            claimerFirstName.value = '';
            claimerLastName.value = '';
            claimerContact.value = '';

            claimerFirstName.removeAttribute('readonly');
            claimerLastName.removeAttribute('readonly');
            claimerContact.removeAttribute('readonly');

            // DON'T clear the modal info here - it should persist until new button is clicked
            // document.getElementById('modalRequestNo').textContent = '';
            // document.getElementById('modalStudentName').textContent = '';
        });

        // Reset modal state when shown (to ensure clean state)
        claimerModal.addEventListener('show.bs.modal', function() {
            console.log('Modal showing'); // Debug log

            // Reset form validation state
            const claimerForm = document.getElementById('claimerForm');
            claimerForm.classList.remove('was-validated');

            // Remove any lingering error/success alerts
            const alerts = claimerForm.querySelectorAll('.alert-danger, .alert-success');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-info')) {
                    alert.remove();
                }
            });

            setLoadingState(false);
        });

        // Initial page load spinner
        const spinner = document.getElementById("spinner");
        const table = document.getElementById("requestTable");

        spinner.style.display = "block";
        table.style.display = "none";

        setTimeout(() => {
            spinner.style.display = "none";
            table.style.display = "block";
        }, 600);

        // Search functionality (keeping existing search code)
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const searchInfo = document.getElementById('searchInfo');
        const searchResultText = document.getElementById('searchResultText');
        const searchQuery = document.getElementById('searchQuery');
        const noResults = document.getElementById('noResults');
        const tableRows = document.querySelectorAll('.table-row');
        const paginationContainer = document.getElementById('paginationContainer');

        let currentFilter = 'all';
        let totalRows = tableRows.length;

        // Search input event listener
        searchInput.addEventListener('input', function() {
            performSearch();
        });

        // Clear search button
        clearSearchBtn.addEventListener('click', function() {
            clearSearch();
        });

        // Filter dropdown options
        document.querySelectorAll('.filter-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                currentFilter = this.getAttribute('data-filter');
                document.getElementById('filterDropdown').textContent = this.textContent;
                performSearch();
            });
        });

        // Perform search function
        function performSearch() {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                let shouldShow = false;

                if (query === '') {
                    shouldShow = true;
                } else {
                    switch (currentFilter) {
                        case 'all':
                            shouldShow = searchAllColumns(row, query);
                            break;
                        case 'req-no':
                            shouldShow = row.getAttribute('data-req-no').includes(query);
                            break;
                        case 'student':
                            shouldShow = row.getAttribute('data-student').includes(query);
                            break;
                        case 'document':
                            shouldShow = row.getAttribute('data-document').includes(query);
                            break;
                        case 'school':
                            shouldShow = row.getAttribute('data-school').includes(query);
                            break;
                        case 'status':
                            shouldShow = row.getAttribute('data-status').includes(query);
                            break;
                        default:
                            shouldShow = searchAllColumns(row, query);
                    }
                }

                if (shouldShow) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateSearchInfo(query, visibleCount);

            if (query !== '') {
                paginationContainer.style.display = 'none';
            } else {
                paginationContainer.style.display = 'block';
            }
        }

        function searchAllColumns(row, query) {
            const searchableAttributes = [
                'data-req-no', 'data-student', 'data-document', 'data-school',
                'data-via', 'data-release-mode', 'data-remarks', 'data-status'
            ];

            return searchableAttributes.some(attr =>
                row.getAttribute(attr).includes(query)
            );
        }

        function updateSearchInfo(query, visibleCount) {
            if (query === '') {
                searchInfo.style.display = 'none';
                noResults.style.display = 'none';
            } else {
                searchInfo.style.display = 'block';
                searchQuery.textContent = `"${query}"`;

                if (visibleCount === 0) {
                    searchResultText.textContent = 'No records found for';
                    noResults.style.display = 'block';
                } else {
                    searchResultText.textContent = `Found ${visibleCount} of ${totalRows} records for`;
                    noResults.style.display = 'none';
                }
            }
        }

        window.clearSearch = function() {
            searchInput.value = '';
            currentFilter = 'all';
            document.getElementById('filterDropdown').textContent = 'Filter';
            performSearch();
            searchInput.focus();
        }

        // IMPROVED CLAIMER FORM SUBMISSION WITH BETTER ERROR HANDLING
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

            // Show loading state
            setLoadingState(true);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;

            if (!csrfToken) {
                console.error('CSRF token not found');
                showError('Security token not found. Please refresh the page and try again.');
                setLoadingState(false);
                return;
            }

            // Prepare form data
            const formData = new FormData(claimerForm);
            const actionUrl = claimerForm.action;

            console.log('Submitting to:', actionUrl); // Debug log

            // Submit using fetch with improved error handling
            fetch(actionUrl, {
                    method: 'POST', // Use POST instead of PUT for better compatibility
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: (() => {
                        // Add _method field for Laravel method spoofing
                        formData.append('_method', 'PUT');
                        return formData;
                    })()
                })
                .then(async response => {
                    console.log('Response status:', response.status); // Debug log
                    console.log('Response headers:', response.headers); // Debug log

                    if (!response.ok) {
                        // Try to get error message from response
                        let errorMessage = 'An error occurred while processing the request.';

                        try {
                            const errorData = await response.json();
                            console.log('Error data:', errorData); // Debug log

                            if (errorData.message) {
                                errorMessage = errorData.message;
                            } else if (errorData.errors) {
                                // Handle validation errors
                                const errors = Object.values(errorData.errors).flat();
                                errorMessage = errors.join(', ');
                            }
                        } catch (parseError) {
                            console.error('Error parsing response:', parseError);
                            // Try to get text response
                            try {
                                const errorText = await response.text();
                                console.log('Error text:', errorText); // Debug log
                                if (errorText.includes('419')) {
                                    errorMessage = 'Session expired. Please refresh the page and try again.';
                                } else if (errorText.includes('404')) {
                                    errorMessage = 'Request not found. Please refresh the page and try again.';
                                } else if (errorText.includes('500')) {
                                    errorMessage = 'Server error. Please try again later.';
                                }
                            } catch (textError) {
                                console.error('Error getting text response:', textError);
                            }
                        }

                        throw new Error(errorMessage);
                    }

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        console.log('Success data:', result); // Debug log

                        // Show success message if provided
                        if (result.message) {
                            showSuccess(result.message);
                        }

                        // Redirect or reload after short delay
                        setTimeout(() => {
                            if (result.redirect) {
                                window.location.href = result.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 1000);
                    } else {
                        // If not JSON, assume success and reload
                        console.log('Non-JSON response, assuming success'); // Debug log
                        showSuccess('Document marked as claimed successfully!');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error); // Debug log
                    showError(error.message);
                    setLoadingState(false);
                });
        });

        // Helper function to set loading state
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

        // Helper function to show error messages
        function showError(message) {
            // Create or update error alert
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

        // Helper function to show success messages
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

        // Handle Delete button clicks (keeping existing functionality)
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
            const allButtons = document.querySelectorAll('.delete-btn');
            allButtons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = 'Delete';
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
            if (e.key === 'Escape' && searchInput.value !== '') {
                clearSearch();
            }
        });

        // Phone number formatting
        const contactInput = document.getElementById('claimerContact');
        contactInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s\(\)]/g, '');
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
    /* Additional styling for better loading states */
    .btn:disabled {
        cursor: not-allowed;
    }

    .spinner-border-sm {
        width: 0.875rem;
        height: 0.875rem;
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

    /* Search container styling */
    .search-container {
        flex-wrap: nowrap;
    }

    @media (max-width: 768px) {
        .search-container {
            width: 100%;
            flex-wrap: wrap;
        }

        .search-container .input-group {
            width: 100% !important;
            margin-bottom: 0.5rem;
        }
    }

    /* Highlight search results */
    .table-row.highlight {
        background-color: #fff3cd !important;
        transition: background-color 0.3s ease;
    }

    /* Search input focus styling */
    #searchInput:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* Filter dropdown styling */
    .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
    }

    .filter-option:hover {
        background-color: #f8f9fa;
    }

    .filter-option.active {
        background-color: #1dd3b0;
        color: white;
    }

    /* Modal styling enhancements */
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

    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    /* Required field indicator */
    .text-danger {
        font-weight: bold;
    }

    /* Alert styling in modal */
    .alert-info {
        background-color: #e3f2fd;
        border-color: #1976d2;
        color: #1565c0;
    }

    /* Form validation styling */
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

    /* Loading state for modal */
    .modal-content.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    /* Smooth transitions for modal form */
    .modal-body input,
    .modal-body select,
    .modal-body textarea {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
</style>

@endsection