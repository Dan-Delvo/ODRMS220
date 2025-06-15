@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Pending Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Pending Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Pending: {{ $totalCount }}</span>
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
                <h5 class="mb-2 mb-md-0">Pending Document Requests</h5>

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
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="student">Student Name</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="document">Document Type</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="school">School/Entity</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="reqno">Request No.</a></li>
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
                            No pending document requests found.
                        </div>
                    @else
                    <table class="table table-sm table-bordered table-hover align-middle text-nowrap" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th title="Request Number">Req #</th>
                                <th>Student</th>
                                <th>Doc</th>
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
                                <td><span class="badge bg-warning text-dark px-2 py-1">{{ $item->status }}</span></td>
                                <td>{{ $item->request_date }}</td>
                                <td>{{ $item->approve_date }}</td>
                                <td>{{ $item->forRelease_date }}</td>
                                <td>{{ $item->claimed_date }}</td>
                                <td class="text-nowrap">
                                    @if(!empty($approvePending))
                                    <form action="{{ route('pending.destroy', $item->id) }}" method="POST" class="d-inline decline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mb-1 decline-btn">Decline</button>
                                    </form>

                                    <form action="{{ route('document-request.complete', $item->id) }}" method="POST" class="d-inline accept-form">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success mb-1 accept-btn" data-original-text="Accept">Accept</button>
                                    </form>
                                    @endif

                                    @if(!empty($PermissionEdit))
                                    <a href="{{ route('pending.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                    @endif

                                    <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $item->id }}">
                                        Receipt
                                    </button>

                                    <!-- Image Button -->
                                    @if($item->image)
                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#imageModal{{ $item->id }}" title="View Image">
                                        <i class="fas fa-image"></i> Image
                                    </button>
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

                <!-- Receipt Modals -->
                @foreach ($DocRequests as $item)
                    @if ($item->receipt)
                    <div class="modal fade" id="receiptModal{{ $item->id }}" tabindex="-1" aria-labelledby="receiptModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-md">
                            <div class="modal-content border-0 shadow-sm">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title mx-auto" id="receiptModalLabel{{ $item->id }}">
                                        Receipt #{{ $item->receipt->receipt_no }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body bg-white text-dark px-4 py-3" style="font-family: 'Courier New', Courier, monospace;">
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('images/UBLOGO.png') }}" alt="UB Logo" class="mb-2" style="max-height: 80px;">
                                        <h5 class="fw-bold mb-1">Upper Bicutan National High School</h5>
                                        <div class="text-muted small">Official Receipt</div>
                                    </div>

                                    <hr>

                                    <div class="mb-2 d-flex justify-content-between">
                                        <strong>Document:</strong>
                                        <span>{{ $item->documents->DocType }}</span>
                                    </div>

                                    <div class="mb-2 d-flex justify-content-between">
                                        <strong>Amount Paid:</strong>
                                        <span>₱{{ number_format($item->receipt->doc_amount, 2) }}</span>
                                    </div>

                                    <div class="mb-2 d-flex justify-content-between">
                                        <strong>Student ID:</strong>
                                        <span>{{ $item->receipt->name_request }}</span>
                                    </div>

                                    <div class="mb-2 d-flex justify-content-between">
                                        <strong>Date:</strong>
                                        <span>{{ \Carbon\Carbon::parse($item->receipt->time_request)->format('F d, Y - h:i A') }}</span>
                                    </div>

                                    <hr>

                                    <div class="text-center mt-3">
                                        <div class="text-muted small">Thank you for your request!</div>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light border-top-0">
                                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close Receipt</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Image Modal -->
                    @if($item->image)
                    <div class="modal fade" id="imageModal{{ $item->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-sm">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="imageModalLabel{{ $item->id }}">
                                        <i class="fas fa-image me-2"></i>
                                        Request Image - {{ $item->req_no }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-0 text-center bg-light">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $item->image) }}"
                                             alt="Request Image for {{ $item->req_no }}"
                                             class="img-fluid w-100"
                                             style="max-height: 70vh; object-fit: contain;"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ asset('images/no-image-placeholder.png') }}'; this.alt='Image not available';">

                                        <!-- Loading overlay -->
                                        <div class="position-absolute top-50 start-50 translate-middle" id="imageLoader{{ $item->id }}" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading image...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image details -->
                                    <div class="p-3 bg-white border-top">
                                        <div class="row text-start">
                                            <div class="col-md-6">
                                                <small class="text-muted">Student:</small><br>
                                                <strong>{{ $item->studentInformation->full_name }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Document Type:</small><br>
                                                <strong>{{ $item->documents->DocType }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light">
                                    <a href="{{ asset('storage/' . $item->image) }}"
                                       target="_blank"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Open in New Tab
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
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

        // Image modal loading functionality
        document.querySelectorAll('[id^="imageModal"]').forEach(modal => {
            modal.addEventListener('show.bs.modal', function() {
                const modalId = this.id.replace('imageModal', '');
                const loader = document.getElementById('imageLoader' + modalId);
                const img = this.querySelector('img');

                if (loader && img) {
                    loader.style.display = 'block';

                    // Hide loader when image loads
                    img.addEventListener('load', function() {
                        loader.style.display = 'none';
                    });

                    // Hide loader on error too
                    img.addEventListener('error', function() {
                        loader.style.display = 'none';
                    });
                }
            });
        });

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
                    // Search based on current filter
                    switch(currentFilter) {
                        case 'all':
                            shouldShow = searchAllColumns(row, query);
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
                        case 'reqno':
                            shouldShow = row.getAttribute('data-req-no').includes(query);
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

            // Update search info and results
            updateSearchInfo(query, visibleCount);

            // Hide pagination when searching
            if (query !== '') {
                paginationContainer.style.display = 'none';
            } else {
                paginationContainer.style.display = 'block';
            }
        }

        // Search all columns function
        function searchAllColumns(row, query) {
            const searchableAttributes = [
                'data-req-no', 'data-student', 'data-document', 'data-school',
                'data-via', 'data-release-mode', 'data-remarks', 'data-status'
            ];

            return searchableAttributes.some(attr =>
                row.getAttribute(attr).includes(query)
            );
        }

        // Update search info
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

        // Clear search function
        window.clearSearch = function() {
            searchInput.value = '';
            currentFilter = 'all';
            document.getElementById('filterDropdown').textContent = 'Filter';
            performSearch();
            searchInput.focus();
        }

        // Handle Accept button clicks with loading spinner
        const acceptForms = document.querySelectorAll(".accept-form");
        acceptForms.forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const acceptBtn = form.querySelector(".accept-btn");
                const originalText = acceptBtn.getAttribute("data-original-text");

                // Disable button and show spinner
                acceptBtn.disabled = true;
                acceptBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Processing...
                `;

                // Optional: Disable other buttons in the same row to prevent multiple actions
                const row = form.closest('tr');
                const allButtons = row.querySelectorAll('button, a.btn');
                allButtons.forEach(btn => {
                    if (btn !== acceptBtn) {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                    }
                });

                // Submit form after a brief delay
                setTimeout(() => {
                    form.submit();
                }, 200);
            });
        });

        // Handle Decline button clicks with loading spinner
        const declineForms = document.querySelectorAll(".decline-form");
        declineForms.forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const declineBtn = form.querySelector(".decline-btn");

                // Show confirmation dialog
                if (confirm("Are you sure you want to decline this request?")) {
                    // Disable button and show spinner
                    declineBtn.disabled = true;
                    declineBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Declining...
                    `;

                    // Optional: Disable other buttons in the same row
                    const row = form.closest('tr');
                    const allButtons = row.querySelectorAll('button, a.btn');
                    allButtons.forEach(btn => {
                        if (btn !== declineBtn) {
                            btn.disabled = true;
                            btn.style.opacity = '0.5';
                        }
                    });

                    // Submit form after a brief delay
                    setTimeout(() => {
                        form.submit();
                    }, 200);
                }
            });
        });

        // Handle potential form submission errors (optional)
        window.addEventListener('pageshow', function(event) {
            // Re-enable buttons if user navigates back
            const allButtons = document.querySelectorAll('.accept-btn, .decline-btn');
            allButtons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';

                if (btn.classList.contains('accept-btn')) {
                    btn.innerHTML = btn.getAttribute('data-original-text') || 'Accept';
                } else if (btn.classList.contains('decline-btn')) {
                    btn.innerHTML = 'Decline';
                }
            });
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
            // Escape to clear search
            if (e.key === 'Escape' && searchInput.value !== '') {
                clearSearch();
            }
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
    .accept-btn, .decline-btn {
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

    /* Image modal specific styles */
    .modal-lg {
        max-width: 900px;
    }

    .modal-body img {
        border-radius: 0.375rem;
    }

    /* Image loading state */
    .position-relative .spinner-border {
        z-index: 10;
    }

    /* Responsive image modal */
    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95%;
            margin: 1.75rem auto;
        }

        .modal-body img {
            max-height: 50vh !important;
        }
    }

    /* Image button styling */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>

@endsection
