@extends('layout.blankpage')

@section('content')

    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h1 class="mt-4">
                <span class="badge page-title-badge">Declined Requests</span>
            </h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
                <li class="breadcrumb-item active">Declined Requests</li>
            </ol>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <h1 class="mt-md-4">
                <span class="badge count-badge">Total Declined: {{ $DocRequests->total() }}</span>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            @if (session('Status'))
                <div class="alert alert-success">
                    {{ session('Status') }}
                </div>
            @endif

            @if (session('Danger'))
                <div class="alert alert-danger">
                    {{ session('Danger') }}
                </div>
            @endif

            <x-tabs page='Declined' />

            <div class="card shadow-lg border-0 rounded-lg mt-3">
                {{-- Card Header with Search/Filter Controls --}}
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">Declined Document Requests</h5>

                    {{-- Search/Filter Form --}}
                    <form method="GET" action="{{ url()->current() }}" id="searchForm" class="w-100 w-md-auto">
                        <div class="search-controls">
                            {{-- Search Input --}}
                            <div class="input-group search-input-group">
                                <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                                    placeholder="Search..." value="{{ request('search') }}">
                                <button class="btn btn-outline-light btn-sm px-2" type="button" id="clearSearch"
                                    title="Clear">
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

                <div class="card-body bg-light">
                    {{-- Search Info Banner - Copied from pending.blade.php --}}
                    @if (request('search') || request('filter') != 'all' || request('sort') != 'default')
                        <div class="alert alert-info mb-3 py-2">
                            <small>
                                <i class="fas fa-search me-1"></i>
                                @if (request('search'))
                                    Showing results for: <strong>"{{ request('search') }}"</strong>
                                @else
                                    Showing all records
                                @endif
                                @if (request('filter') != 'all')
                                    filtered by <strong>{{ ucfirst(request('filter')) }}</strong>
                                @endif
                                @if (request('sort') != 'default')
                                    - Sorted by <strong>Request No.
                                        ({{ request('sort') == 'asc' ? 'A-Z' : 'Z-A' }})</strong>
                                @endif
                                {{-- Assuming the route is 'declined.index' or similar for clearing --}}
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
                            </small>
                        </div>
                    @endif

                    <div id="spinner" class="text-center my-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    {{-- Removed old searchInfo div --}}

                    <div class="table-responsive" id="requestTable">
                        @if ($DocRequests->isEmpty())
                            <div class="alert alert-warning text-center my-3">
                                @if (request('search'))
                                    No declined document requests found matching your search criteria.
                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-warning ms-2">Clear
                                        Search</a>
                                @else
                                    No declined document requests found.
                                @endif
                            </div>
                        @else
                            <table class="table table-bordered table-hover align-middle" id="requestsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sortable-header">
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                                        <th title="School/Entity">School</th>
                                        <th title="Requested Via">Via</th>
                                        <th title="Release Mode">Rel Mode</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th title="Request Date">Req Date</th>
                                        <th class="action-column">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @foreach ($DocRequests as $item)
                                        <tr>
                                            <td class="fw-semibold">{{ $item->req_no }}</td>
                                            <td>{{ $item->studentInformation->full_name }}</td>
                                            <td>{{ $item->documents->DocType }}</td>
                                            <td>{{ strtoupper($item->request_schl_entity) }}</td>
                                            <td>{{ $item->request_mode }}</td>
                                            <td>{{ $item->release_mode }}</td>
                                            <td>{{ $item->remarks }}</td>
                                            <td><span class="badge bg-danger text-white status-badge">{{ $item->status }}</span></td>
                                            <td>{{ $item->request_date }}</td>
                                            <td class="action-column">
                                                <div class="btn-group-vertical btn-group-sm d-md-inline" role="group">
                                                    <form action="{{ route('tables.destroy', $item->id) }}" method="POST"
                                                        class="d-inline decline-form" data-swal-loading="true"
                                                        data-swal-delete="true">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="decline_reason" class="decline-reason" value="">
                                                        <button type="submit" class="btn btn-sm btn-danger mb-1 decline-btn">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('document-request.complete', $item->id) }}"
                                                        method="POST" class="d-inline accept-form" data-swal-loading="true"
                                                        data-swal-title="Reaccepting Declined Request"
                                                        data-swal-text="This may take a few seconds...">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success mb-1 accept-btn"
                                                            data-original-text="Accept">
                                                            <i class="fas fa-check me-1"></i>Accept
                                                        </button>
                                                    </form>

                                                    @if ($item->supporting_document)
                                                        <button type="button" class="btn btn-sm btn-primary mb-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#documentModal{{ $item->id }}">
                                                            <i class="fas fa-file-alt me-1"></i>View Doc
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="d-flex flex-column justify-content-center align-items-center mt-3"
                        id="paginationContainer">
                        {{-- Pagination links must include existing query parameters --}}
                        {{ $DocRequests->appends(request()->query())->links() }}
                        <small class="text-muted">
                            Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of
                            {{ $DocRequests->total() }}
                        </small>
                    </div>

                    {{-- Modals (Receipt & Document) --}}
                    @foreach ($DocRequests as $item)
                        @if ($item->receipt)
                            <div class="modal fade" id="receiptModal{{ $item->id }}" tabindex="-1"
                                aria-labelledby="receiptModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content border-0 shadow-sm">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title mx-auto" id="receiptModalLabel{{ $item->id }}">
                                                Receipt #{{ $item->receipt->receipt_no }}
                                            </h5>
                                            <button type="button"
                                                class="btn-close btn-close-white position-absolute end-0 me-3"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body bg-white text-dark px-4 py-3"
                                            style="font-family: 'Courier New', Courier, monospace;">
                                            <div class="text-center mb-3">
                                                <img src="{{ asset('images/UBLOGO.png') }}" alt="UB Logo"
                                                    class="mb-2" style="max-height: 80px;">
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
                                            <button type="button" class="btn btn-secondary w-100"
                                                data-bs-dismiss="modal">Close Receipt</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($item->image || $item->supporting_document)
                            <div class="modal fade" id="documentModal{{ $item->id }}" tabindex="-1"
                                aria-labelledby="documentModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content border-0 shadow-sm">
                                        <div class="modal-header text-white justify-content-between align-items-center"
                                            style="background-color: #1f2937;">
                                            <h5 class="modal-title" id="documentModalLabel{{ $item->id }}"
                                                style="color: #1dd3b0;">
                                                <i class="fas fa-file-alt me-2"></i>
                                                Supporting Document Comparison - Request No. {{ $item->req_no }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body bg-light">
                                            <div class="row g-3">

                                                <div class="col-md-6">
                                                    <div
                                                        class="border rounded bg-white shadow-sm h-100 d-flex flex-column">
                                                        <div class="p-2 text-center border-bottom"
                                                            style="background:#f8fafc;">
                                                            <strong class="text-muted"><i class="fas fa-history me-1"></i>
                                                                Old Supporting Document File</strong>
                                                        </div>
                                                        <div
                                                            class="flex-fill d-flex align-items-center justify-content-center p-2">
                                                            @php
                                                                $oldPath = $item->image;
                                                                $oldExt = $oldPath
                                                                    ? strtolower(pathinfo($oldPath, PATHINFO_EXTENSION))
                                                                    : null;
                                                            @endphp

                                                            @if ($oldPath)
                                                                {{-- Assuming file-preview partial exists and works --}}
                                                                @include('layout.partials.file-preview', [
                                                                    'filePath' => $oldPath,
                                                                    'ext' => $oldExt,
                                                                    'id' => 'old_' . $item->id,
                                                                ])
                                                            @else
                                                                <p class="text-muted">No old file available</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div
                                                        class="border rounded bg-white shadow-sm h-100 d-flex flex-column">
                                                        <div class="p-2 text-center border-bottom"
                                                            style="background:#f8fafc;">
                                                            <strong class="text-muted"><i
                                                                    class="fas fa-file-upload me-1"></i> New Supporting
                                                                Document File</strong>
                                                        </div>
                                                        <div
                                                            class="flex-fill d-flex align-items-center justify-content-center p-2">
                                                            @php
                                                                $newPath = $item->supporting_document;
                                                                $newExt = $newPath
                                                                    ? strtolower(pathinfo($newPath, PATHINFO_EXTENSION))
                                                                    : null;
                                                            @endphp

                                                            @if ($newPath)
                                                                {{-- Assuming file-preview partial exists and works --}}
                                                                @include('layout.partials.file-preview', [
                                                                    'filePath' => $newPath,
                                                                    'ext' => $newExt,
                                                                    'id' => 'new_' . $item->id,
                                                                ])
                                                            @else
                                                                <div class="text-center text-muted">
                                                                    <i class="fas fa-file text-secondary"
                                                                        style="font-size:3rem;"></i>
                                                                    <p class="mt-2">No new file uploaded</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer" style="background-color: #1f2937;">
                                            <button type="button" class="btn btn-outline-light btn-sm"
                                                data-bs-dismiss="modal" style="border-color: #1dd3b0; color: #1dd3b0;">
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

    {{-- Reason Modal (For Delete/Decline) - KEPT ORIGINAL LOGIC --}}
    <div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-start" style="background-color: #1f2937;">
                    <h5 class="modal-title" style="color: #1dd3b0;">Decline Reason</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" id="reasonInput" rows="3" placeholder="Enter reason for declining"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-white" style="background-color: #1f2937;"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white" id="proceedToConfirmBtn"
                        style="background-color: #1dd3b0;">Proceed</button>
                </div>
            </div>
        </div>
    </div>


    {{-- JavaScript - Updated for server-side search/sort --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let targetForm;

            const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearch');
            const filterSelect = document.getElementById('filterSelect');
            const sortSelect = document.getElementById('sortSelect');

            // Step 1: Click decline → open reason modal (Original Logic)
            document.querySelectorAll('.decline-btn').forEach(function(btn) {
                // Only attach event listener if the form is the DELETE form (the old logic used this for 'Delete')
                if (btn.closest('form').querySelector('input[name="_method"][value="DELETE"]')) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault(); // Prevent immediate form submission
                        targetForm = btn.closest('form');
                        document.getElementById('reasonInput').value = ''; // clear previous
                        reasonModal.show();
                    });
                }
            });

            // Step 2: After entering reason → show SweetAlert confirmation (Original Logic)
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

                // Note: The original decline logic was a bit confusing as it used 'decline-reason' for a 'DELETE' form.
                // We set the reason here, assuming the backend can read it for logging/auditing the deletion.
                const reasonInputHidden = targetForm.querySelector('.decline-reason');
                if (reasonInputHidden) {
                    reasonInputHidden.value = reason;
                } else {
                    // Fallback if the hidden input isn't found (though it was added above)
                    const newInput = document.createElement('input');
                    newInput.type = 'hidden';
                    newInput.name = 'decline_reason'; // Reusing old logic name
                    newInput.value = reason;
                    targetForm.appendChild(newInput);
                }

                reasonModal.hide();

                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    html: `You are about to delete this request with reason:<br><strong>${reason}</strong><br>You won't be able to revert this!`,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Changed to match Delete color
                    cancelButtonColor: '#1f2937',
                    confirmButtonText: 'Confirm Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable all buttons in the row and submit
                        const declineBtn = targetForm.querySelector(".decline-btn");
                        declineBtn.disabled = true;
                        declineBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Deleting...
                    `;
                        const row = targetForm.closest('tr');
                        if (row) {
                            row.querySelectorAll('button').forEach(b => {
                                if (b !== declineBtn) {
                                    b.disabled = true;
                                    b.style.opacity = '0.5';
                                }
                            });
                        }

                        targetForm.submit();
                    }
                });
            });

            // --- New Search/Filter/Sort Logic from pending.blade.php ---

            // Auto-submit form on filter/sort change
            filterSelect?.addEventListener('change', function() {
                searchForm.submit();
            });

            sortSelect?.addEventListener('change', function() {
                searchForm.submit();
            });

            // Clear search button (redirects to clear all params)
            clearSearchBtn?.addEventListener('click', function() {
                window.location.href = '{{ url()->current() }}';
            });

            // Auto-search with debounce (simplified to manual search on form submit for consistency)
            // Kept the keydown events for user experience
            let searchTimeout = null;
            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                // Submits if 3+ characters are typed or if the input is cleared
                searchTimeout = setTimeout(() => {
                    if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                        searchForm.submit();
                    }
                }, 500);
            });

            // Show loading spinner on form submit
            searchForm?.addEventListener('submit', function() {
                document.getElementById('spinner').style.display = 'block';
                document.getElementById('requestTable').style.opacity = '0.5';
            });

            // Initial page load spinner (kept from original declined.blade.php)
            const spinner = document.getElementById("spinner");
            const table = document.getElementById("requestTable");

            spinner.style.display = "block";
            table.style.display = "none";

            setTimeout(() => {
                spinner.style.display = "none";
                table.style.display = "block";
            }, 600);

            // --- Kept Accept Form Logic with Spinner ---
            const acceptForms = document.querySelectorAll('.accept-form');
            acceptForms.forEach(form => {
                let manualSubmit = false;

                form.addEventListener('submit', function(e) {
                    if (!manualSubmit) {
                        e.preventDefault();

                        const acceptBtn = form.querySelector('.accept-btn');
                        const originalText = acceptBtn.getAttribute('data-original-text') ||
                            'Accept';

                        acceptBtn.disabled = true;
                        acceptBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Processing...
                    `;

                        const row = form.closest('tr');
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


            // Add keyboard shortcuts (copied from original declined.blade.php)
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + F to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
                // Escape to clear search (redirects to clear all params)
                if (e.key === 'Escape' && searchInput.value !== '') {
                    window.location.href = '{{ url()->current() }}';
                }
            });

            // Re-enable buttons on page show (back button)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    document.querySelectorAll('.accept-btn').forEach(btn => {
                        btn.disabled = false;
                        btn.innerHTML = btn.getAttribute('data-original-text') || 'Accept';
                    });
                    document.querySelectorAll('.decline-btn').forEach(btn => {
                        btn.disabled = false;
                        btn.innerHTML = 'Delete';
                    });
                }
            });
        });
    </script>

    {{-- Kept original styling, adapting it slightly for the new controls --}}
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
            background-color: var(--primary-color);
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
        #searchInput:focus,
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

        /* ===== MODAL STYLES ===== */
        .modal-dialog {
            max-width: 600px;
        }

        .modal-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
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
        }

        /* ===== PAGINATION ===== */
        .pagination {
            margin-bottom: 0;
        }

        /* ===== SMOOTH TRANSITIONS ===== */
        .btn,
        .form-control,
        .form-select {
            transition: all 0.2s ease-in-out;
        }

        /* ===== UTILITY CLASSES ===== */
        .fw-semibold {
            font-weight: 600;
        }
    </style>

@endsection
