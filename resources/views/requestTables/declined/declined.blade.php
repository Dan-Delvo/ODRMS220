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
                <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap" id="tableControls">
                    {{-- Search Input --}}
                    <div class="input-group" style="width: 300px;">
                        <input type="text"
                            name="search"
                            id="searchInput"
                            class="form-control form-control-sm"
                            placeholder="Search requests..."
                            value="{{ request('search') }}"
                            autocomplete="off">
                        <button class="btn btn-outline-light btn-sm"
                            type="button"
                            id="clearSearch"
                            title="Clear search"
                            style="display: none;">
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

                    {{-- Reset Button --}}
                    <button type="button" class="btn btn-light btn-sm" id="resetBtn">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
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
                    @if ($DocRequests->isEmpty())
                    <div class="alert alert-warning text-center my-3">
                        @if (request('search'))
                        No declined document requests found matching your search criteria.
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

                {{-- Pagination --}}
                <div id="paginationContainer">
                    @if (!$DocRequests->isEmpty())
                    <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                        {{ $DocRequests->appends(request()->query())->links() }}
                        <small class="text-muted">
                            Showing {{ $DocRequests->firstItem() }} - {{ $DocRequests->lastItem() }} of
                            {{ $DocRequests->total() }}
                        </small>
                    </div>
                    @endif
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
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const filterSelect = document.getElementById('filterSelect');
        const sortSelect = document.getElementById('sortSelect');
        const resetBtn = document.getElementById('resetBtn');
        const spinner = document.getElementById("spinner");
        const table = document.getElementById("requestTable");
        const paginationContainer = document.getElementById("paginationContainer");
        const searchInfoAlert = document.getElementById('searchInfoAlert');

        // --- Hide X button by default ---
        toggleClearButton();

        // --- Toggle Clear Button Visibility ---
        function toggleClearButton() {
            if (clearSearchBtn) {
                clearSearchBtn.style.display = searchInput.value.trim().length > 0 ? 'inline-block' : 'none';
            }
        }

        // --- AJAX SEARCH FUNCTION - FIXED (NO FORM) ---
        function performAjaxSearch() {
            const search = searchInput.value.trim();
            const filter = filterSelect.value;
            const sort = sortSelect.value;

            spinner.style.display = "block";
            table.style.opacity = "0.5";
            if (paginationContainer) paginationContainer.style.opacity = "0.5";

            const url = `{{ route('declined-documents.index') }}?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
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

                    // Update pagination - FIXED
                    const newPagination = doc.getElementById('paginationContainer');
                    if (paginationContainer && newPagination) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    } else if (paginationContainer && !newPagination) {
                        paginationContainer.innerHTML = ''; // Clear pagination if no results
                    }

                    // Update search info alert
                    const hasSearch = searchInput.value.trim().length > 0;
                    const hasFilter = filterSelect && filterSelect.value !== 'all';
                    const hasSort = sortSelect && sortSelect.value !== 'default';

                    if (searchInfoAlert) {
                        if (hasSearch || hasFilter || hasSort) {
                            let alertHTML = '<small><i class="fas fa-search me-1"></i>';

                            if (hasSearch) {
                                alertHTML += `Showing results for: <strong>"${searchInput.value}"</strong>`;
                            }

                            if (hasFilter) {
                                const filterText = filterSelect.options[filterSelect.selectedIndex].text;
                                alertHTML += ` in <strong>${filterText}</strong>`;
                            }

                            if (hasSort) {
                                const sortText = sortSelect.value === 'asc' ? 'A-Z' : 'Z-A';
                                alertHTML += ` - Sorted by <strong>Request No. (${sortText})</strong>`;
                            }

                            alertHTML += ' <button type="button" id="clearAllBtn" class="btn btn-sm btn-outline-info ms-2">Clear All</button></small>';

                            searchInfoAlert.innerHTML = alertHTML;
                            searchInfoAlert.style.display = 'block';

                            attachClearAllEvent();
                        } else {
                            searchInfoAlert.style.display = 'none';
                        }
                    }

                    spinner.style.display = "none";
                    table.style.opacity = "1";
                    if (paginationContainer) paginationContainer.style.opacity = "1";

                    // Reattach event listeners
                    reattachActionButtons();
                    attachPaginationListeners();
                })
                .catch(err => {
                    console.error('AJAX Error:', err);
                    spinner.style.display = "none";
                    table.style.opacity = "1";
                    if (paginationContainer) paginationContainer.style.opacity = "1";
                });
        }

        // --- AJAX PAGINATION HANDLER - FIXED ---
        function attachPaginationListeners() {
            document.querySelectorAll('#paginationContainer .pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.href;

                    spinner.style.display = "block";
                    table.style.opacity = "0.5";
                    if (paginationContainer) paginationContainer.style.opacity = "0.5";

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
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

                            // Update pagination - FIXED
                            const newPagination = doc.getElementById('paginationContainer');
                            if (paginationContainer && newPagination) {
                                paginationContainer.innerHTML = newPagination.innerHTML;
                            }

                            spinner.style.display = "none";
                            table.style.opacity = "1";
                            if (paginationContainer) paginationContainer.style.opacity = "1";

                            // Scroll to top of card
                            document.querySelector('.card').scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });

                            // Reattach event listeners
                            reattachActionButtons();
                            attachPaginationListeners();
                        })
                        .catch(err => {
                            console.error('AJAX Pagination Error:', err);
                            spinner.style.display = "none";
                            table.style.opacity = "1";
                            if (paginationContainer) paginationContainer.style.opacity = "1";
                        });
                });
            });
        }

        // Initial attachment
        attachPaginationListeners();

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
            toggleClearButton();
            performAjaxSearch();
        }

        // --- RESET BUTTON ---
        resetBtn?.addEventListener('click', resetAllFilters);

        // --- SEARCH INPUT ---
        let searchTimeout = null;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            toggleClearButton();

            searchTimeout = setTimeout(() => {
                performAjaxSearch();
            }, 400);
        });

        // --- CLEAR (X) BUTTON ---
        clearSearchBtn?.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearButton();
            performAjaxSearch();
        });

        // --- FILTER & SORT CHANGE ---
        filterSelect?.addEventListener('change', performAjaxSearch);
        sortSelect?.addEventListener('change', performAjaxSearch);

        // --- KEYBOARD SHORTCUTS ---
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
            if (e.key === 'Escape' && searchInput.value !== '') {
                searchInput.value = '';
                toggleClearButton();
                performAjaxSearch();
            }
        });

        // --- REATTACH ACTION BUTTONS ---
        function reattachActionButtons() {
            // Decline button logic
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

            // Accept form logic
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
        }

        // Initial attachment
        reattachActionButtons();

        // --- DECLINE BUTTON LOGIC ---
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

        // --- Re-enable buttons when navigating back ---
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.querySelectorAll('.accept-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = btn.getAttribute('data-original-text') || '<i class="fas fa-check me-1"></i>Accept';
                });
                document.querySelectorAll('.decline-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
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