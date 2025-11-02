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

                        {{-- Filter Dropdown --}}
                        <select name="filter" id="filterSelect" class="form-select form-select-sm" style="width: auto;">
                            <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Fields</option>
                            <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student Name</option>
                            <option value="document" {{ request('filter') == 'document' ? 'selected' : '' }}>Document Type</option>
                            <option value="school" {{ request('filter') == 'school' ? 'selected' : '' }}>School/Entity</option>
                            <option value="reqno" {{ request('filter') == 'reqno' ? 'selected' : '' }}>Request No.</option>
                            <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>

                        {{-- Sort Dropdown --}}
                        <select name="sort" id="sortSelect" class="form-select form-select-sm" style="width: auto;">
                            <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default Order</option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Req No. (A-Z)</option>
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Req No. (Z-A)</option>
                        </select>

                        {{-- Reset Button (replaces Search button) --}}
                        <button type="button" id="resetBtn" class="btn btn-light btn-sm">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
            {{-- END: Updated Card Header with Search/Filter Controls --}}

            <div class="card-body bg-light">
                {{-- Search Info Banner - Copied from pending.blade.php --}}
                @if(request('search') || request('filter') != 'all' || request('sort') != 'default')
                <div class="alert alert-info mb-3 py-2" id="searchInfoAlert" style="display: none;">
                    <small>
                        <i class="fas fa-search me-1"></i>
                        <span id="alertContent"></span>
                        <button type="button" id="clearAllBtn" class="btn btn-sm btn-outline-info ms-2">Clear All</button>
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
        const resetBtn = document.getElementById('resetBtn');
        const clearAllBtn = document.getElementById('clearAllBtn');
        const spinner = document.getElementById("spinner");
        const table = document.getElementById("requestTable");
        const paginationContainer = document.getElementById("paginationContainer");
        const searchInfoAlert = document.getElementById('searchInfoAlert');

        // --- Hide X button and alert by default ---
        if (clearSearchBtn) {
            clearSearchBtn.style.visibility = 'hidden';
            clearSearchBtn.style.opacity = '0';
            clearSearchBtn.style.transition = 'opacity 0.2s ease, visibility 0.2s ease';
        }

        // --- AJAX SEARCH FUNCTION ---
        function performAjaxSearch() {
            const url = searchForm.getAttribute('action') || window.location.href;
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData).toString();

            spinner.style.display = "block";
            table.style.opacity = "0.5";
            if (paginationContainer) paginationContainer.style.opacity = "0.5";

            fetch(`${url}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                
                // Update table
                const newTable = doc.getElementById('requestTable');
                if (newTable) {
                    table.innerHTML = newTable.innerHTML;
                }

                // Update pagination
                const newPagination = doc.getElementById('paginationContainer');
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }

                // Update or hide search info alert
                const hasSearch = searchInput.value.trim().length > 0;
                const hasFilter = filterSelect && filterSelect.value !== 'all';
                const hasSort = sortSelect && sortSelect.value !== 'default';

                if (searchInfoAlert) {
                    if (hasSearch || hasFilter || hasSort) {
                        // Update alert content
                        let alertHTML = '<small><i class="fas fa-search me-1"></i>';
                        
                        if (hasSearch) {
                            alertHTML += `Showing results for: <strong>"${searchInput.value}"</strong>`;
                        } else {
                            alertHTML += 'Showing all records';
                        }
                        
                        if (hasFilter) {
                            const filterText = filterSelect.options[filterSelect.selectedIndex].text;
                            alertHTML += ` filtered by <strong>${filterText}</strong>`;
                        }
                        
                        if (hasSort) {
                            const sortText = sortSelect.value === 'asc' ? 'A-Z' : 'Z-A';
                            alertHTML += ` - Sorted by <strong>Request No. (${sortText})</strong>`;
                        }
                        
                        alertHTML += ' <button type="button" id="clearAllBtn" class="btn btn-sm btn-outline-info ms-2">Clear All</button></small>';
                        
                        searchInfoAlert.innerHTML = alertHTML;
                        searchInfoAlert.style.display = 'block';
                        
                        // Re-attach clear all button event
                        attachClearAllEvent();
                    } else {
                        searchInfoAlert.style.display = 'none';
                    }
                }

                // Update URL without reload
                const newUrl = params ? `${url}?${params}` : url;
                window.history.pushState({}, '', newUrl);

                spinner.style.display = "none";
                table.style.opacity = "1";
                if (paginationContainer) paginationContainer.style.opacity = "1";
            })
            .catch(err => {
                console.error('AJAX Error:', err);
                spinner.style.display = "none";
                table.style.opacity = "1";
                if (paginationContainer) paginationContainer.style.opacity = "1";
            });
        }

        // --- CLEAR ALL BUTTON EVENT ---
        function attachClearAllEvent() {
            const newClearAllBtn = document.getElementById('clearAllBtn');
            if (newClearAllBtn) {
                newClearAllBtn.addEventListener('click', resetAllFilters);
            }
        }

        function resetAllFilters() {
            searchInput.value = '';
            if (filterSelect) filterSelect.value = 'all';
            if (sortSelect) sortSelect.value = 'default';
            clearSearchBtn.style.visibility = 'hidden';
            clearSearchBtn.style.opacity = '0';
            performAjaxSearch();
        }

        // Attach initial clear all event
        clearAllBtn?.addEventListener('click', resetAllFilters);

        // --- RESET BUTTON ---
        resetBtn?.addEventListener('click', resetAllFilters);

        // --- SEARCH INPUT ---
        let searchTimeout = null;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);

            // Toggle X button visibility
            if (searchInput.value.trim().length > 0) {
                clearSearchBtn.style.visibility = 'visible';
                clearSearchBtn.style.opacity = '1';
            } else {
                clearSearchBtn.style.visibility = 'hidden';
                clearSearchBtn.style.opacity = '0';
            }

            searchTimeout = setTimeout(() => {
                performAjaxSearch();
            }, 400);
        });

        // --- CLEAR (X) BUTTON ---
        clearSearchBtn?.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.visibility = 'hidden';
            clearSearchBtn.style.opacity = '0';
            performAjaxSearch();
        });

        // --- FILTER & SORT CHANGE ---
        filterSelect?.addEventListener('change', performAjaxSearch);
        sortSelect?.addEventListener('change', performAjaxSearch);

        // Prevent default submit reload
        searchForm?.addEventListener('submit', function(e) {
            e.preventDefault();
        });

        // --- KEYBOARD SHORTCUTS ---
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
            if (e.key === 'Escape' && searchInput.value !== '') {
                searchInput.value = '';
                clearSearchBtn.style.visibility = 'hidden';
                clearSearchBtn.style.opacity = '0';
                performAjaxSearch();
            }
        });

        // --- INITIAL PAGE LOAD SPINNER ---
        spinner.style.display = "block";
        table.style.display = "none";
        setTimeout(() => {
            spinner.style.display = "none";
            table.style.display = "block";
        }, 600);

        // --- DECLINE BUTTON LOGIC ---
        document.querySelectorAll('.decline-btn').forEach(function(btn) {
            if (btn.closest('form').querySelector('input[name="_method"][value="DELETE"]')) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    targetForm = btn.closest('form');
                    document.getElementById('reasonInput').value = '';
                    reasonModal.show();
                });
            }
        });

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

            const reasonInputHidden = targetForm.querySelector('.decline-reason');
            if (reasonInputHidden) {
                reasonInputHidden.value = reason;
            } else {
                const newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'decline_reason';
                newInput.value = reason;
                targetForm.appendChild(newInput);
            }

            reasonModal.hide();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                html: `You are about to delete this request with reason:<br><strong>${reason}</strong><br>You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#1f2937',
                confirmButtonText: 'Confirm Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
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

        // --- ACCEPT FORM LOGIC ---
        const acceptForms = document.querySelectorAll('.accept-form');
        acceptForms.forEach(form => {
            let manualSubmit = false;
            form.addEventListener('submit', function(e) {
                if (!manualSubmit) {
                    e.preventDefault();
                    const acceptBtn = form.querySelector('.accept-btn');
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
                    manualSubmit = true;
                    setTimeout(() => form.submit(), 100);
                }
            });
        });

        // --- Re-enable buttons when navigating back ---
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

    /* Search container styling - Adopted from pending.blade.php's structure */
    #tableControls {
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        #tableControls {
            width: 100%;
            flex-wrap: wrap;
        }

        #tableControls .input-group,
        #tableControls select,
        #tableControls button[type="submit"] {
            width: 100% !important;
            margin-bottom: 0.5rem;
        }
    }

    /* Search input focus styling */
    #searchInput:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* Filter/Sort select focus styling */
    .form-select:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
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

    /* Explicit styling for table layout to prevent horizontal issues */
    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: max-content;
        min-width: 100%;
        table-layout: auto;
    }

    .table th,
    .table td {
        white-space: nowrap;
    }

    /* Make Remarks column allow wrapping */
    .table th:nth-child(7),
    .table td:nth-child(7) {
        white-space: normal;
        min-width: 100px;
    }
</style>

@endsection