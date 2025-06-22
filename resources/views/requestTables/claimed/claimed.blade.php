@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

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
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Claimed: {{ $totalCount }}</span>
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
                <h5 class="mb-2 mb-md-0">Claimed Document Requests</h5>

                <!-- Action Buttons and Search Bar -->
                <div class="d-flex flex-column flex-md-row gap-2 mt-2 mt-md-0">
                    <!-- Report Generation Button -->
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">
                        <i class="fas fa-chart-bar me-1"></i>Generate Report
                    </button>

                    <!-- Search Container -->
                    <div class="search-container d-flex gap-2">
                        <div class="input-group" style="width: 300px;">
                            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search claimed requests..." style="border-radius: 0.375rem 0 0 0.375rem;">
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
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="req-no">Request No.</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="student">Student Name</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="document">Document Type</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="school">School/Entity</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="claimer">Claimer</a></li>
                            </ul>
                        </div>
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
                            No claimed document requests found.
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
                                <th>Claimer</th>
                                <th>Contact</th>
                                <th>Remarks</th>
                                <th title="Request Date">Req Date</th>
                                <th title="Approved Date">App Date</th>
                                <th title="For Release Date">Rel Date</th>
                                <th title="Claimed Date">Claimed Date</th>
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
                                data-claimer="{{ strtolower(($item->claimer->Fname ?? '') . ' ' . ($item->claimer->Lname ?? '')) }}"
                                data-contact="{{ strtolower($item->claimer->contact_no ?? '') }}"
                                data-remarks="{{ strtolower($item->remarks) }}"
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
                                <td>{{ ($item->claimer->Fname ?? '') . ' ' . ($item->claimer->Lname ?? '') }}</td>
                                <td>{{ $item->claimer->contact_no ?? 'N/A' }}</td>
                                <td>{{ $item->remarks }}</td>
                                <td>{{ $item->request_date }}</td>
                                <td>{{ $item->approve_date }}</td>
                                <td>{{ $item->forRelease_date }}</td>
                                <td>
                                    <span class="badge bg-success text-white px-2 py-1">
                                        {{ \Carbon\Carbon::parse($item->claimed_date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="text-nowrap">

                                    @if(!empty($PermissionEdit))
                                    <a href="{{ route('claimed-documents.edit', $item->id) }}" class="btn btn-sm btn-info mb-1">Edit</a>
                                    @endif

                                    @if(!empty($deleteClaimed))
                                    <form action="{{ route('claimed-documents.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
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

                <div class="mt-3" id="paginationContainer">
                    {{ $DocRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revert Modal -->
<div class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="revertModalLabel">
                    <i class="fas fa-undo me-2"></i>Revert Document to For Release
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

<!-- Report Generation Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="reportModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>Generate Claimed Documents Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

{{-- Enhanced JavaScript with loading spinners and search functionality --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initial page load spinner
    const spinner = document.getElementById("spinner");
    const table = document.getElementById("requestTable");

    spinner.style.display = "block";
    table.style.display = "none";

    setTimeout(() => {
        spinner.style.display = "none";
        table.style.display = "block";
    }, 600);

    // Search functionality
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
                switch(currentFilter) {
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
                    case 'claimer':
                        shouldShow = row.getAttribute('data-claimer').includes(query);
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
            'data-via', 'data-release-mode', 'data-claimer', 'data-contact', 'data-remarks'
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

    // Handle Revert button clicks - populate modal
    document.querySelectorAll('.revert-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const requestNo = this.getAttribute('data-request-no');
            const studentName = this.getAttribute('data-student-name');

            document.getElementById('modalRevertRequestNo').textContent = requestNo;
            document.getElementById('modalRevertStudentName').textContent = studentName;

            const form = document.getElementById('revertForm');
            form.action = `{{ route('claimed-documents.revert', '') }}/${requestId}`;

            form.reset();
            form.classList.remove('was-validated');
        });
    });

    // REVERT FORM SUBMISSION WITH ERROR HANDLING
    const revertForm = document.getElementById('revertForm');
    const submitRevertBtn = document.getElementById('submitRevertBtn');

    revertForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!revertForm.checkValidity()) {
            e.stopPropagation();
            revertForm.classList.add('was-validated');
            return;
        }

        // Show loading state
        setRevertLoadingState(true);

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('input[name="_token"]')?.value;

        if (!csrfToken) {
            console.error('CSRF token not found');
            showRevertError('Security token not found. Please refresh the page and try again.');
            setRevertLoadingState(false);
            return;
        }

        // Prepare form data
        const formData = new FormData(revertForm);
        const actionUrl = revertForm.action;

        console.log('Submitting revert to:', actionUrl);

        // Submit using fetch with improved error handling
        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: (() => {
                formData.append('_method', 'PUT');
                return formData;
            })()
        })
        .then(async response => {
            console.log('Revert response status:', response.status);

            if (!response.ok) {
                let errorMessage = 'An error occurred while processing the revert request.';

                try {
                    const errorData = await response.json();
                    console.log('Revert error data:', errorData);

                    if (errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData.errors) {
                        const errors = Object.values(errorData.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                } catch (parseError) {
                    console.error('Error parsing revert response:', parseError);
                    try {
                        const errorText = await response.text();
                        console.log('Revert error text:', errorText);
                        if (errorText.includes('419')) {
                            errorMessage = 'Session expired. Please refresh the page and try again.';
                        } else if (errorText.includes('404')) {
                            errorMessage = 'Request not found. Please refresh the page and try again.';
                        } else if (errorText.includes('500')) {
                            errorMessage = 'Server error. Please try again later.';
                        }
                    } catch (textError) {
                        console.error('Error getting revert text response:', textError);
                    }
                }

                throw new Error(errorMessage);
            }

            // Handle success response
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const result = await response.json();
                console.log('Revert success data:', result);

                if (result.message) {
                    showRevertSuccess(result.message);
                }

                setTimeout(() => {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                console.log('Non-JSON revert response, assuming success');
                showRevertSuccess('Document reverted to For Release successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Revert fetch error:', error);
            showRevertError(error.message);
            setRevertLoadingState(false);
        });
    });

    // Helper function to set revert loading state
    function setRevertLoadingState(isLoading) {
        const formInputs = revertForm.querySelectorAll('input, select, textarea, button');

        if (isLoading) {
            submitRevertBtn.disabled = true;
            submitRevertBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Processing...
            `;

            formInputs.forEach(input => {
                if (input !== submitRevertBtn) {
                    input.disabled = true;
                }
            });
        } else {
            submitRevertBtn.disabled = false;
            submitRevertBtn.innerHTML = `
                <i class="fas fa-undo me-1"></i>Revert to For Release
            `;

            formInputs.forEach(input => {
                input.disabled = false;
            });
        }
    }

    // Helper function to show revert error messages
    function showRevertError(message) {
        let errorAlert = document.getElementById('modalRevertErrorAlert');
        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.id = 'modalRevertErrorAlert';
            errorAlert.className = 'alert alert-danger alert-dismissible fade show';
            errorAlert.innerHTML = `
                <strong>Error:</strong> <span id="revertErrorMessage"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            revertForm.querySelector('.modal-body').insertBefore(errorAlert, revertForm.querySelector('.modal-body').firstChild);
        }

        document.getElementById('revertErrorMessage').textContent = message;
        errorAlert.style.display = 'block';
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Helper function to show revert success messages
    function showRevertSuccess(message) {
        let successAlert = document.getElementById('modalRevertSuccessAlert');
        if (!successAlert) {
            successAlert = document.createElement('div');
            successAlert.id = 'modalRevertSuccessAlert';
            successAlert.className = 'alert alert-success fade show';
            successAlert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i><span id="revertSuccessMessage"></span>
            `;
            revertForm.querySelector('.modal-body').insertBefore(successAlert, revertForm.querySelector('.modal-body').firstChild);
        }

        document.getElementById('revertSuccessMessage').textContent = message;
        successAlert.style.display = 'block';
    }

    // Reset revert modal state when hidden
    const revertModal = document.getElementById('revertModal');
    revertModal.addEventListener('hidden.bs.modal', function() {
        setRevertLoadingState(false);
        revertForm.classList.remove('was-validated');

        // Remove any error/success alerts
        const alerts = revertForm.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    });

    // REPORT FORM HANDLING
    const reportForm = document.getElementById('reportForm');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    // Set default dates (current month)
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    startDateInput.value = firstDayOfMonth.toISOString().split('T')[0];
    endDateInput.value = lastDayOfMonth.toISOString().split('T')[0];

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
            return false;
        }
    });

    // Handle Delete button clicks
    const deleteForms = document.querySelectorAll(".delete-form");
    deleteForms.forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const deleteBtn = form.querySelector(".delete-btn");

            if (confirm("Are you sure you want to delete this claimed request? This action cannot be undone.")) {
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
        const allButtons = document.querySelectorAll('.delete-btn, .revert-btn');
        allButtons.forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = '1';
            if (btn.classList.contains('delete-btn')) {
                btn.innerHTML = 'Delete';
            } else if (btn.classList.contains('revert-btn')) {
                btn.innerHTML = 'Revert';
            }
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

    // Auto-resize textarea
    const revertReasonTextarea = document.getElementById('revertReason');
    revertReasonTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
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
    .delete-btn, .revert-btn {
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
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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
        background-color: #28a745;
        color: white;
    }

    /* Modal styling enhancements */
    .modal-dialog {
        max-width: 600px;
    }

    .modal-header {
        border-bottom: 2px solid rgba(255,255,255,0.1);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control:focus, .form-select:focus {
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

    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffc107;
        color: #856404;
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

    /* Textarea auto-resize */
    #revertReason {
        resize: vertical;
        min-height: 80px;
        max-height: 200px;
    }

    /* Badge styling for claimed date */
    .badge.bg-success {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    /* Action buttons spacing */
    .text-nowrap .btn {
        margin-right: 0.25rem;
    }

    .text-nowrap .btn:last-child {
        margin-right: 0;
    }

    /* Report modal styling */
    .modal-header.bg-info {
        background-color: #17a2b8 !important;
    }

    /* Date input styling */
    input[type="date"]:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    /* Smooth transitions for modal form */
    .modal-body input, .modal-body select, .modal-body textarea {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Header action buttons layout */
    .card-header .d-flex {
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            align-items: stretch;
        }

        .card-header .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

@endsection
