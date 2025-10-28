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
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Declined: {{ $DocRequests->total() }}</span>
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
    <a class="nav-link  text-dark" href="{{ route('claimed-documents.index') }}">Claimed</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="{{ route('declined-documents.index') }}">Declined</a>
  </li>
</ul>
        <div class="card shadow-lg border-0 rounded-lg mt-3">
            {{-- START: Updated Card Header with Search/Filter Controls --}}
            <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="background-color: #1f2937;">
                <h5 class="mb-2 mb-md-0">Declined Document Requests</h5>

                {{-- Search/Filter Form - Targeting the current route --}}
                <form method="GET" action="{{ url()->current() }}" id="searchForm">
                    <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
                        {{-- Search Input --}}
                        <div class="input-group" style="width: 300px;">
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   class="form-control form-control-sm"
                                   placeholder="Search requests..."
                                   value="{{ request('search') }}"
                                   style="border-radius: 0.375rem 0 0 0.375rem;">
                            <button class="btn btn-outline-light btn-sm"
                                    type="button"
                                    id="clearSearch"
                                    title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Filter Dropdown (Now a select element) --}}
                        <select name="filter" id="filterSelect" class="form-select form-select-sm" style="width: auto;">
                            <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Fields</option>
                            <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student Name</option>
                            <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document Type</option>
                            <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School/Entity</option>
                            <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Request No.</option>
                            <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>

                        {{-- Sort Dropdown (Now a select element) --}}
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
            {{-- END: Updated Card Header with Search/Filter Controls --}}

            <div class="card-body bg-light">
                {{-- Search Info Banner - Copied from pending.blade.php --}}
                @if(request('search') || request('filter') != 'all' || request('sort') != 'default')
                <div class="alert alert-info mb-3 py-2">
                    <small>
                        <i class="fas fa-search me-1"></i>
                        @if(request('search'))
                            Showing results for: <strong>"{{ request('search') }}"</strong>
                        @else
                            Showing all records
                        @endif
                        @if(request('filter') != 'all')
                            filtered by <strong>{{ ucfirst(request('filter')) }}</strong>
                        @endif
                        @if(request('sort') != 'default')
                            - Sorted by <strong>Request No. ({{ request('sort') == 'asc' ? 'A-Z' : 'Z-A' }})</strong>
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
                    @if($DocRequests->isEmpty())
                    <div class="alert alert-warning text-center my-3">
                        @if(request('search'))
                            No declined document requests found matching your search criteria.
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-warning ms-2">Clear Search</a>
                        @else
                            No declined document requests found.
                        @endif
                    </div>
                    @else
                    <table class="table table-sm table-bordered table-hover align-middle text-nowrap" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                {{-- Updated Req # to include server-side sort link --}}
                                <th title="Request Number">
                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => request('sort') == 'asc' ? 'desc' : 'asc'])) }}"
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
                                <th title="School/Entity">School</th>
                                <th title="Requested Via">Via</th>
                                <th title="Release Mode">Rel Mode</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th title="Request Date">Req Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach ($DocRequests as $item)
                            <tr class="table-row">
                                <td>{{ $item->req_no }}</td>
                                <td>{{ $item->studentInformation->full_name }}</td>
                                <td>{{ $item->documents->DocType }}</td>
                                <td>{{ strtoupper($item->request_schl_entity) }}</td>
                                <td>{{ $item->request_mode }}</td>
                                <td>{{ $item->release_mode }}</td>
                                <td>{{ $item->remarks }}</td>
                                <td><span class="badge bg-danger text-white px-2 py-1">{{ $item->status }}</span></td>
                                <td>{{ $item->request_date }}</td>
                                <td class="text-nowrap">
                                    {{-- KEPT ACTION BUTTONS (DELETE & ACCEPT/REACCEPT) --}}
                                    <form action="{{ route('tables.destroy', $item->id) }}" method="POST" class="d-inline decline-form" data-swal-loading="true" data-swal-delete="true">
                                        @csrf
                                        @method('DELETE')
                                        {{-- Add hidden input for reason, needed for the original JS logic --}}
                                        <input type="hidden" name="decline_reason" class="decline-reason" value="">
                                        <button type="submit" class="btn btn-sm btn-danger mb-1 decline-btn">Delete</button>
                                    </form>

                                    <form action="{{ route('document-request.complete', $item->id) }}" method="POST" class="d-inline accept-form"
                                        data-swal-loading="true"
                                        data-swal-title="Reaccepting Declined Request"
                                        data-swal-text="This may take a few seconds...">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success mb-1 accept-btn" data-original-text="Accept">Accept</button>
                                    </form>
                                    @if($item->supporting_document)
                                    <button type="button" class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#documentModal{{ $item->id }}">
                                        <i class="fas fa-file-alt me-1"></i>
                                        View Document
                                    </button>
                                    @endif

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- No Results Message - Removed old client-side version --}}

                    @endif
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center mt-3" id="paginationContainer">
                    {{-- Pagination links must include existing query parameters --}}
                    {{ $DocRequests->appends(request()->query())->links() }}
                    <small class="text-muted">
                        Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of {{ $DocRequests->total() }}
                    </small>
                </div>

                {{-- Modals (Receipt & Document) --}}
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

                @if($item->image || $item->supporting_document)
                <div class="modal fade" id="documentModal{{ $item->id }}" tabindex="-1" aria-labelledby="documentModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content border-0 shadow-sm">
                            <div class="modal-header text-white justify-content-between align-items-center" style="background-color: #1f2937;">
                                <h5 class="modal-title" id="documentModalLabel{{ $item->id }}" style="color: #1dd3b0;">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Supporting Document Comparison - Request No. {{ $item->req_no }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body bg-light">
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <div class="border rounded bg-white shadow-sm h-100 d-flex flex-column">
                                            <div class="p-2 text-center border-bottom" style="background:#f8fafc;">
                                                <strong class="text-muted"><i class="fas fa-history me-1"></i> Old Supporting Document File</strong>
                                            </div>
                                            <div class="flex-fill d-flex align-items-center justify-content-center p-2">
                                                @php
                                                $oldPath = $item->image;
                                                $oldExt = $oldPath ? strtolower(pathinfo($oldPath, PATHINFO_EXTENSION)) : null;
                                                @endphp

                                                @if($oldPath)
                                                {{-- Assuming file-preview partial exists and works --}}
                                                @include('layout.partials.file-preview', ['filePath' => $oldPath, 'ext' => $oldExt, 'id' => 'old_'.$item->id])
                                                @else
                                                <p class="text-muted">No old file available</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="border rounded bg-white shadow-sm h-100 d-flex flex-column">
                                            <div class="p-2 text-center border-bottom" style="background:#f8fafc;">
                                                <strong class="text-muted"><i class="fas fa-file-upload me-1"></i> New Supporting Document File</strong>
                                            </div>
                                            <div class="flex-fill d-flex align-items-center justify-content-center p-2">
                                                @php
                                                $newPath = $item->supporting_document;
                                                $newExt = $newPath ? strtolower(pathinfo($newPath, PATHINFO_EXTENSION)) : null;
                                                @endphp

                                                @if($newPath)
                                                {{-- Assuming file-preview partial exists and works --}}
                                                @include('layout.partials.file-preview', ['filePath' => $newPath, 'ext' => $newExt, 'id' => 'new_'.$item->id])
                                                @else
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-file text-secondary" style="font-size:3rem;"></i>
                                                    <p class="mt-2">No new file uploaded</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer" style="background-color: #1f2937;">
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
                <button type="button" class="btn text-white" style="background-color: #1f2937;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="proceedToConfirmBtn" style="background-color: #1dd3b0;">Proceed</button>
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
                    const originalText = acceptBtn.getAttribute('data-original-text') || 'Accept';

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

    .table thead th {
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

    .table thead th a {
        color: #1dd3b0;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .table thead th a:hover {
        color: white;
        text-shadow: 0 0 8px rgba(29, 211, 176, 0.5);
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-color: #e5e7eb;
    }

    .table tbody tr:hover {
        background-color: #f9fafb;
        box-shadow: inset 0 1px 3px rgba(29, 211, 176, 0.1);
    }

    .table tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        color: #374151;
    }

    .table th:nth-child(7),
    .table td:nth-child(7) {
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

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .accept-btn,
    .decline-btn {
        min-width: 70px;
    }

    /* ============= LOADING STATE ============= */
    #spinner {
        padding: 2rem;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        color: #1dd3b0;
    }

    /* ============= TABLE CONTAINER ============= */
    #requestTable {
        transition: opacity 0.3s ease;
        overflow-x: auto;
        border-radius: 0.375rem;
        background-color: white;
    }

    #requestTable::-webkit-scrollbar {
        height: 8px;
    }

    #requestTable::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    #requestTable::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    #requestTable::-webkit-scrollbar-thumb:hover {
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

    .alert-danger {
        background-color: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
    }

    .alert-success {
        background-color: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #15803d;
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
        color: #1dd3b0;
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

    /* ============= RESPONSIVE DESIGN ============= */
    @media (max-width: 1024px) {
        .table {
            font-size: 0.875rem;
        }

        .table th,
        .table td {
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

        .table th,
        .table td {
            padding: 0.625rem 0.5rem;
            font-size: 0.75rem;
        }

        .table th:nth-child(7),
        .table td:nth-child(7) {
            min-width: 80px;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        #requestTable {
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

        .table {
            width: max-content;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.375rem;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .table th:nth-child(7),
        .table td:nth-child(7) {
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

    /* Additional styling for better loading states */
    .btn {
        transition: all 0.2s ease-in-out;
    }

    .spinner-border-sm {
        width: 0.875rem;
        height: 0.875rem;
    }
</style>

@endsection
