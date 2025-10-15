@extends('layout.blankpage')

@section('content')
@include('layout.partials.message')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Processing Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Processing Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Processing: {{ $totalCount }}</span>
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
                <h5 class="mb-2 mb-md-0">Processing Document Requests</h5>

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
                    <!-- NEW Sort Dropdown -->
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
                            <span id="sortInfo" class="ms-2 text-muted"></span>
                        </small>
                    </div>
                </div>

                <div class="table-responsive" id="requestTable">
                    @if($DocRequests->isEmpty())
                    <div class="alert alert-warning text-center my-3">
                        No Processing document requests found.
                    </div>
                    @else
                    <table class="table table-sm table-bordered table-hover align-middle text-nowrap" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th title="Request Number" class="sortable-header" data-column="req-no">
                                    Req # <i class="fas fa-sort sort-icon ms-1" id="req-no-icon"></i>
                                </th>
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
                                data-req-no-raw="{{ $item->req_no }}"
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
                                <td><span class="badge bg-warning text-muted px-2 py-1">{{ $item->status }}</span></td>
                                <td>{{ $item->request_date }}</td>
                                <td>{{ $item->approve_date }}</td>
                                <td>{{ $item->forRelease_date }}</td>
                                <td>{{ $item->claimed_date }}</td>
                                <td class="text-nowrap">
                                    <div class="d-flex flex-wrap flex-md-nowrap gap-2 justify-content-center">
                                        <!-- Update the Complete button form in the Actions column -->
                                        @if(!empty($approveOngoing))
                                        <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-swal-loading="true" data-swal-delete="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>
                                        </form>

                                        <form action="{{ route('document-request2.complete', $item->id) }}" method="POST" class="d-inline complete-form"
                                            data-swal-loading="true"
                                            data-swal-title="Completing Request"
                                            data-swal-text="This may take a few seconds...">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm complete-btn" data-original-text="Complete">Complete</button>
                                        </form>

                                        @if($item->documents->DocType == 'Good Moral')
                                        <form action="{{ route('doc.print', $item->id) }}" method="POST" class="d-inline print-form">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-sm print-btn" data-original-text="Print">Print</button>
                                        </form>
                                        @endif
                                        @endif

                                        @if (!empty($PermissionEdit))
                                        <a href="{{ route('ongoing.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        @endif

                                        @if (!empty($deleteCompleted))
                                        <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST" class="d-inline delete2-form" data-swal-loading="true" data-swal-delete="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete2-btn">Delete</button>
                                        </form>
                                        @endif

                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $item->id }}">
                                            Receipt
                                        </button>
                                    </div>
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
        let currentSortOrder = 'default'; // default, req-asc, req-desc
        let originalRowOrder = []; // Store original order

        const tableBody = document.getElementById('tableBody');
        const tableRows = document.querySelectorAll('.table-row');

        // Store original order
        originalRowOrder = Array.from(tableRows);

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
            const currentRows = Array.from(document.querySelectorAll('.table-row'));

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
                        case 'claimer':
                            shouldShow = row.getAttribute('data-claimer').includes(query);
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
                        case 'receipt':
                            shouldShow = row.getAttribute('data-receipt').includes(query);
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
                'data-id', 'data-claimer', 'data-student', 'data-document', 'data-school',
                'data-via', 'data-release-mode', 'data-remarks', 'data-status', 'data-receipt'
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

        // Handle Complete button clicks with loading spinner
        const completeForms = document.querySelectorAll(".complete-form");
        completeForms.forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const completeBtn = form.querySelector(".complete-btn");
                const originalText = completeBtn.getAttribute("data-original-text");

                // Disable button and show spinner
                completeBtn.disabled = true;
                completeBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Processing...
                `;

                // Disable other buttons in the same row
                const row = form.closest('tr');
                const allButtons = row.querySelectorAll('button, a.btn');
                allButtons.forEach(btn => {
                    if (btn !== completeBtn) {
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

        // Handle Delete button clicks with loading spinner
        const deleteForms = document.querySelectorAll(".delete-form, .delete2-form");
        deleteForms.forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const deleteBtn = form.querySelector(".delete-btn, .delete2-btn");

                // Show confirmation dialog
                if (confirm("Are you sure you want to delete this request?")) {
                    // Disable button and show spinner
                    deleteBtn.disabled = true;
                    deleteBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Deleting...
                    `;

                    // Disable other buttons in the same row
                    const row = form.closest('tr');
                    const allButtons = row.querySelectorAll('button, a.btn');
                    allButtons.forEach(btn => {
                        if (btn !== deleteBtn) {
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

        const sortInfo = document.getElementById('sortInfo');


        // Sort functionality
        function sortTable(order) {
            const rows = Array.from(document.querySelectorAll('.table-row'));

            let sortedRows;

            switch (order) {
                case 'req-asc':
                    sortedRows = rows.sort((a, b) => {
                        const aVal = a.getAttribute('data-req-no-raw');
                        const bVal = b.getAttribute('data-req-no-raw');
                        return aVal.localeCompare(bVal, undefined, {
                            numeric: true,
                            sensitivity: 'base'
                        });
                    });
                    break;
                case 'req-desc':
                    sortedRows = rows.sort((a, b) => {
                        const aVal = a.getAttribute('data-req-no-raw');
                        const bVal = b.getAttribute('data-req-no-raw');
                        return bVal.localeCompare(aVal, undefined, {
                            numeric: true,
                            sensitivity: 'base'
                        });
                    });
                    break;
                default: // 'default'
                    sortedRows = [...originalRowOrder];
                    break;
            }

            // Clear and repopulate table body
            tableBody.innerHTML = '';
            sortedRows.forEach(row => {
                tableBody.appendChild(row);
            });

            // Update sort icon
            updateSortIcon(order);

            // Update sort info
            updateSortInfo(order);

            currentSortOrder = order;
        }

        function updateSortIcon(order) {
            const icon = document.getElementById('req-no-icon');

            switch (order) {
                case 'req-asc':
                    icon.className = 'fas fa-sort-up sort-icon ms-1';
                    break;
                case 'req-desc':
                    icon.className = 'fas fa-sort-down sort-icon ms-1';
                    break;
                default:
                    icon.className = 'fas fa-sort sort-icon ms-1';
                    break;
            }
        }

        function updateSortInfo(order) {
            let sortText = '';

            switch (order) {
                case 'req-asc':
                    sortText = '(sorted by Req No. A-Z)';
                    break;
                case 'req-desc':
                    sortText = '(sorted by Req No. Z-A)';
                    break;
                default:
                    sortText = '';
                    break;
            }

            sortInfo.textContent = sortText;
        }

        // Sort dropdown options
        document.querySelectorAll('.sort-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const sortOrder = this.getAttribute('data-sort');
                document.getElementById('sortDropdown').innerHTML = `<i class="fas fa-sort me-1"></i>${this.textContent.trim()}`;

                sortTable(sortOrder);

                // Re-apply search if there's a search query
                const currentQuery = searchInput.value.toLowerCase().trim();
                if (currentQuery !== '') {
                    performSearch();
                }
            });
        });

        // Table header click sorting (alternative method)
        document.querySelector('.sortable-header[data-column="req-no"]').addEventListener('click', function() {
            let newOrder;

            if (currentSortOrder === 'default' || currentSortOrder === 'req-desc') {
                newOrder = 'req-asc';
            } else {
                newOrder = 'req-desc';
            }

            sortTable(newOrder);

            // Update dropdown to reflect current sort
            const sortDropdown = document.getElementById('sortDropdown');
            const sortText = newOrder === 'req-asc' ? 'Req No. (A-Z)' : 'Req No. (Z-A)';
            sortDropdown.innerHTML = `<i class="fas fa-sort me-1"></i>${sortText}`;

            // Re-apply search if there's a search query
            const currentQuery = searchInput.value.toLowerCase().trim();
            if (currentQuery !== '') {
                performSearch();
            }
        });

        // Handle Print button clicks with loading spinner
        const printForms = document.querySelectorAll(".print-form");
        printForms.forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const printBtn = form.querySelector(".print-btn");
                const originalText = printBtn.getAttribute("data-original-text");

                // Disable button and show spinner
                printBtn.disabled = true;
                printBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Printing...
                `;

                // Disable other buttons in the same row
                const row = form.closest('tr');
                const allButtons = row.querySelectorAll('button, a.btn');
                allButtons.forEach(btn => {
                    if (btn !== printBtn) {
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

        // Handle potential form submission errors (optional)
        window.addEventListener('pageshow', function(event) {
            // Re-enable buttons if user navigates back
            const allButtons = document.querySelectorAll('.complete-btn, .delete-btn, .delete2-btn, .print-btn');
            allButtons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';

                if (btn.classList.contains('complete-btn')) {
                    btn.innerHTML = btn.getAttribute('data-original-text') || 'Complete';
                } else if (btn.classList.contains('delete-btn') || btn.classList.contains('delete2-btn')) {
                    btn.innerHTML = 'Delete';
                } else if (btn.classList.contains('print-btn')) {
                    btn.innerHTML = btn.getAttribute('data-original-text') || 'Print';
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
    .complete-btn,
    .delete-btn,
    .delete2-btn,
    .print-btn {
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


    /* Sortable header styling */
    .sortable-header {
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    .sortable-header:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sort-icon {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .sortable-header:hover .sort-icon {
        opacity: 1;
    }

    /* Sort dropdown styling */
    .dropdown-menu .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .dropdown-menu .dropdown-item i {
        width: 16px;
        text-align: center;
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
</style>

@endsection