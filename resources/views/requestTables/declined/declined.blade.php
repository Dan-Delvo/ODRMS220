@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Declined Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Declined Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Declined: {{ $totalCount }}</span>
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
                <h5 class="mb-2 mb-md-0">Declined Document Requests</h5>

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
                            <li><a class="dropdown-item filter-option" href="#" data-filter="student">Student Name</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="document">Document Type</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="school">School/Entity</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="reqno">Request No.</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="status">Status</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Sort by Request Number">
                            <i class="fas fa-sort me-1"></i>Sort
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item sort-option" href="#" data-sort="default">Default Order</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort="req-asc">
                                <i class="fas fa-sort-numeric-down me-2"></i>Req No. (A-Z)
                            </a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort="req-desc">
                                <i class="fas fa-sort-numeric-up me-2"></i>Req No. (Z-A)
                            </a></li>
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
                        No declined document requests found.
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
                                <td><span class="badge bg-danger text-white px-2 py-1">{{ $item->status }}</span></td>
                                <td>{{ $item->request_date }}</td>
                                <td>{{ $item->approve_date }}</td>
                                <td>{{ $item->forRelease_date }}</td>
                                <td>{{ $item->claimed_date }}</td>
                                <td class="text-nowrap">
                                    <!-- Accept and Decline buttons moved here -->
                                <form action="{{ route('tables.destroy', $item->id) }}" method="POST" class="d-inline decline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1 decline-btn">Delete</button>
                                </form>

                                <form action="{{ route('document-request.complete', $item->id) }}" method="POST" class="d-inline accept-form">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success mb-1 accept-btn" data-original-text="Accept">Accept</button>
                                </form>

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

                <!-- Supporting Document Modal -->
                @if($item->supporting_document)
                <div class="modal fade" id="documentModal{{ $item->id }}" tabindex="-1" aria-labelledby="documentModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-sm">
                            <div class="modal-header text-white justify-content-between align-items-center" style="background-color: #1f2937;">
                                <h5 class="modal-title" id="documentModalLabel{{ $item->id }}" style="color: #1dd3b0;">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Supporting Document - Request No. {{ $item->req_no }}
                                </h5>

                                <!-- Removed buttons from here - they are now in the action column -->
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-0 text-center bg-light">
                                <div class="position-relative">
                                    @php
                                    $fileExtension = strtolower(pathinfo($item->supporting_document, PATHINFO_EXTENSION));
                                    // Since database already stores full path, use it directly
                                    $documentPath = $item->supporting_document;
                                    @endphp

                                    @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <!-- Display image if it's an image file -->
                                    <img src="{{ asset($documentPath) }}"
                                        alt="Supporting Document for {{ $item->req_no }}"
                                        class="img-fluid w-100"
                                        style="max-height: 70vh; object-fit: contain;"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image-placeholder.png') }}'; this.alt='Document not available';">

                                    @elseif($fileExtension === 'pdf')
                                    <!-- Display PDF preview -->
                                    <div class="p-4">
                                        <div class="text-center mb-3">
                                            <i class="fas fa-file-pdf text-danger" style="font-size: 4rem;"></i>
                                            <h5 class="mt-2">PDF Document</h5>
                                            <p class="text-muted">{{ basename($item->supporting_document) }}</p>
                                        </div>
                                        <iframe src="{{ asset($documentPath) }}"
                                            width="100%"
                                            height="400px"
                                            style="border: 1px solid #ddd;">
                                            <p>Your browser does not support PDFs.
                                                <a href="{{ asset($documentPath) }}" target="_blank">Download the PDF</a>
                                            </p>
                                        </iframe>
                                    </div>

                                    @else
                                    <!-- Display file icon for other file types -->
                                    <div class="p-5 text-center">
                                        @switch($fileExtension)
                                        @case('doc')
                                        @case('docx')
                                        <i class="fas fa-file-word text-primary" style="font-size: 4rem;"></i>
                                        @break
                                        @case('xls')
                                        @case('xlsx')
                                        <i class="fas fa-file-excel text-success" style="font-size: 4rem;"></i>
                                        @break
                                        @case('ppt')
                                        @case('pptx')
                                        <i class="fas fa-file-powerpoint text-warning" style="font-size: 4rem;"></i>
                                        @break
                                        @case('txt')
                                        <i class="fas fa-file-alt text-secondary" style="font-size: 4rem;"></i>
                                        @break
                                        @default
                                        <i class="fas fa-file text-muted" style="font-size: 4rem;"></i>
                                        @endswitch

                                        <h5 class="mt-3">{{ strtoupper($fileExtension) }} Document</h5>
                                        <p class="text-muted">{{ basename($item->supporting_document) }}</p>
                                        <p class="small text-info">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Click "Download" or "Open in New Tab" to view this document
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Loading overlay -->
                                    <div class="position-absolute top-50 start-50 translate-middle" id="documentLoader{{ $item->id }}" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading document...</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document details -->
                                <div class="p-3 bg-white border-top">
                                    <div class="row text-start">
                                        <div class="col-md-4">
                                            <small class="text-muted">Student:</small><br>
                                            <strong>{{ $item->studentInformation->full_name }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Document Type:</small><br>
                                            <strong>{{ $item->documents->DocType }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">File Type:</small><br>
                                            <strong>{{ strtoupper($fileExtension) }}</strong>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">File Name:</small><br>
                                            <strong>{{ basename($item->supporting_document) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer" style="background-color: #1f2937;">
                                <a href="{{ asset($documentPath) }}"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    Open in New Tab
                                </a>
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal" style="border-color: #1dd3b0; color: #1dd3b0;">
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

<!-- Confirmation Modal -->




{{-- Enhanced JavaScript with loading spinners and search functionality --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let targetForm;
        let currentSort = 'default';

        const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
        const tableBody = document.getElementById('tableBody');
        const originalOrder = Array.from(document.querySelectorAll('.table-row'));


        // Step 1: Click decline → open reason modal
        document.querySelectorAll('.decline-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                targetForm = btn.closest('form');
                document.getElementById('reasonInput').value = ''; // clear previous
                reasonModal.show();
            });
        });



        // Step 2: After entering reason → show SweetAlert confirmation
        document.getElementById('proceedToConfirmBtn').addEventListener('click', function() {
            const reason = document.getElementById('reasonInput').value.trim();

            if (!reason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please enter a reason!',
                    confirmButtonColor: '#1dd3b0'
                });
                return;
            }

            targetForm.querySelector('.decline-reason').value = reason;

            reasonModal.hide();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                html: `You are about to decline with reason:<br><strong>${reason}</strong><br>You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonColor: '#1dd3b0',
                cancelButtonColor: '#1f2937',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    targetForm.submit();
                }
            });
        });
    });

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

        // Document modal loading functionality
        // Handle Decline/Delete button clicks with loading spinner (no modal)
        document.querySelectorAll(".decline-form").forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const declineBtn = form.querySelector(".decline-btn");

                // Disable button + show spinner
                declineBtn.disabled = true;
                declineBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Deleting...
                `;

                // Disable other buttons in the same row
                const row = form.closest("tr");
                const allButtons = row.querySelectorAll("button, a.btn");
                allButtons.forEach(btn => {
                    if (btn !== declineBtn) {
                        btn.disabled = true;
                        btn.style.opacity = "0.5";
                    }
                });

                // Submit form after a short delay
                setTimeout(() => {
                    form.submit();
                }, 200);
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

        document.querySelectorAll('.sort-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                currentSort = this.getAttribute('data-sort');
                document.getElementById('sortDropdown').innerHTML = `<i class="fas fa-sort me-1"></i>${this.textContent}`;
                performSort();
                performSearch(); // Re-apply search after sorting
            });
        });


        function performSort() {
        const rows = Array.from(tableBody.querySelectorAll('.table-row'));

        let sortedRows;

        switch (currentSort) {
            case 'req-asc':
                sortedRows = rows.sort((a, b) => {
                    const reqA = a.getAttribute('data-req-no').toLowerCase();
                    const reqB = b.getAttribute('data-req-no').toLowerCase();
                    return reqA.localeCompare(reqB);
                });
                break;

            case 'req-desc':
                sortedRows = rows.sort((a, b) => {
                    const reqA = a.getAttribute('data-req-no').toLowerCase();
                    const reqB = b.getAttribute('data-req-no').toLowerCase();
                    return reqB.localeCompare(reqA);
                });
                break;

            case 'default':
            default:
                // Restore original order
                sortedRows = originalOrder.slice();
                break;
        }

        // Clear table body and re-append sorted rows
        tableBody.innerHTML = '';
        sortedRows.forEach(row => tableBody.appendChild(row));
}
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
                    switch (currentFilter) {
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
            currentSort = 'default'; // Add this line
            document.getElementById('filterDropdown').textContent = 'Filter';
            document.getElementById('sortDropdown').innerHTML = '<i class="fas fa-sort me-1"></i>Sort'; // Add this line
            performSort(); // Add this line to reset to original order
            performSearch();
            searchInput.focus();
        }

        // Handle Accept button clicks with loading spinner
        const acceptForms = document.querySelectorAll('.accept-form');

        acceptForms.forEach(form => {
            let manualSubmit = false;

            form.addEventListener('submit', function(e) {
                if (!manualSubmit) {
                    e.preventDefault();

                    const acceptBtn = form.querySelector('.accept-btn');
                    const originalText = acceptBtn.getAttribute('data-original-text') || 'Accept';

                    acceptBtn.disabled = true;
                    acceptBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Processing...
                    `;

                    const row = form.closest('tr') || form.closest('.modal-header');
                    if (row) {
                        const allButtons = row.querySelectorAll('button, a.btn');
                        allButtons.forEach(btn => {
                            if (btn !== acceptBtn) {
                                btn.disabled = true;
                                btn.style.opacity = '0.5';
                            }
                        });
                    }

                    // allow next submission
                    manualSubmit = true;
                    setTimeout(() => {
                        form.submit();
                    }, 100);
                }
            });
        });

        // Handle Decline button clicks with loading spinner
        const declineForms = document.querySelectorAll(".decline-form");
        declineForms.forEach(form => {
            form.addEventListener("submit", function(e) {
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
    .accept-btn,
    .decline-btn {
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

    /* Action column styling for better button layout */
    .table td.text-nowrap {
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Button spacing in action column */
    .table .btn-sm {
        margin: 1px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Ensure action column buttons stack properly on mobile */
    @media (max-width: 576px) {
        .table td.text-nowrap {
            white-space: normal;
        }

        .table .btn-sm {
            display: block;
            width: 100%;
            margin-bottom: 2px;
        }

        .table .d-inline {
            display: block !important;
            width: 100%;
        }
    }
</style>

@endsection
