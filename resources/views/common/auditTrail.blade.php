@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Audit Trail</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Audit Trail</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Records: {{ $auditTrail->count() }}</span>
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
                <h5 class="mb-2 mb-md-0">System Audit Log</h5>

                <!-- Search Bar -->
                <div class="search-container d-flex gap-2 mt-2 mt-md-0">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search audit logs..." style="border-radius: 0.375rem 0 0 0.375rem;">
                        <button class="btn btn-outline-light btn-sm" type="button" id="clearSearch" title="Clear search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item filter-option active" href="#" data-filter="all">All Records</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="type">Action Type</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="user">Changed By</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="table">Table Name</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="date">Date</a></li>
                        </ul>
                    </div>

                    <!-- NEW: Table Name Filter Buttons -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="tableFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-table me-1"></i>Table Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="tableFilterDropdown">
                            <li><a class="dropdown-item table-filter-option active" href="#" data-table="all">All Tables</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item table-filter-option" href="#" data-table="login">Login</a></li>
                            <li><a class="dropdown-item table-filter-option" href="#" data-table="doc_requests">Document Requests</a></li>
                            <li><a class="dropdown-item table-filter-option" href="#" data-table="permission_role">Permission Role</a></li>
                            <li><a class="dropdown-item table-filter-option" href="#" data-table="acc_users">Account Users</a></li>
                            <li><a class="dropdown-item table-filter-option" href="#" data-table="std_students">Students</a></li>
                            <li><a class="dropdown-item table-filter-option" href="#" data-table="doc_categories">Document Categories</a></li>
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

                <div class="table-responsive" id="auditTable">
                    @if($auditTrail->isEmpty())
                    <div class="alert alert-warning text-center my-3">
                        No audit trail records found.
                    </div>
                    @else
                    <table class="table table-sm table-bordered table-hover align-middle text-nowrap" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th title="Audit ID">Audit No.</th>
                                <th title="Action Type">Type</th>
                                <th title="Description">Description</th>
                                <th title="Changed By User">Changed By</th>
                                <th title="Date and Time">Date/Time</th>
                                <th title="Old Data">Old Data</th>
                                <th title="New Data">New Data</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach ($auditTrail as $item)
                            <tr class="audit-row"
                                data-id="{{ $item->id }}"
                                data-type="{{ strtolower($item->type) }}"
                                data-description="{{ strtolower($item->description) }}"
                                data-user="{{ strtolower($item->changedBy) }}"
                                data-date="{{ $item->time ? $item->time->format('Y-m-d') : '' }}"
                                data-old-data="{{ strtolower($item->old_data ?? '') }}"
                                data-new-data="{{ strtolower($item->new_data ?? '') }}">

                                <td>{{ $loop->iteration + $auditTrail->firstItem() - 1 }}</td>
                                <td>
                                    @switch($item->type)
                                    @case('CREATE')
                                    <span class="badge bg-success px-2 py-1">{{ $item->type }}</span>
                                    @break
                                    @case('UPDATE')
                                    <span class="badge bg-warning text-dark px-2 py-1">{{ $item->type }}</span>
                                    @break
                                    @case('DELETE')
                                    <span class="badge bg-danger px-2 py-1">{{ $item->type }}</span>
                                    @break
                                    @default
                                    <span class="badge bg-secondary px-2 py-1">{{ $item->type }}</span>
                                    @endswitch
                                </td>
                                <td> {{ $item->description }}</td>
                                <td>{{ $item->changedBy }}</td>
                                <td>{{ $item->time ? $item->time->format('M d, Y - h:i A') : 'N/A' }}</td>
                                <td>
                                    @if($item->old_data)
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#oldDataModal{{ $item->id }}">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->new_data)
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#newDataModal{{ $item->id }}">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-details" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                        <i class="fas fa-info-circle me-1"></i>Details
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                        {{ $auditTrail->links() }}
                        <small class="text-muted">
                            Showing {{ $auditTrail->firstItem() }} - {{ $auditTrail->lastItem() }} of {{ $auditTrail->total() }}
                        </small>
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" class="alert alert-warning text-center my-3" style="display: none;">
                        <i class="fas fa-search me-2"></i>
                        No records found matching your search criteria.
                        <button class="btn btn-sm btn-outline-warning ms-2" onclick="clearSearch()">Clear Search</button>
                    </div>
                    @endif
                </div>

                <!-- Data Modals -->
                @foreach ($auditTrail as $item)
                <!-- Old Data Modal -->
                @if($item->old_data)
                <div class="modal fade" id="oldDataModal{{ $item->id }}" tabindex="-1" aria-labelledby="oldDataModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-sm">
                            <div class="modal-header text-white" style="background-color: #1f2937;">
                                <h5 class="modal-title" id="oldDataModalLabel{{ $item->id }}" style="color: #1dd3b0;">
                                    <i class="fas fa-database me-2"></i>
                                    Old Data - {{ $item->fromTableName }} (ID: {{ $item->id }})
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Action Type:</small><br>
                                            <strong>{{ $item->type }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Changed By:</small><br>
                                            <strong>{{ $item->changedBy }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="text-secondary mb-3">Previous Data:</h6>
                                <div class="bg-white p-3 border rounded">
                                    <ul class="mb-0" style="font-size: 0.9rem;">
                                        @foreach(explode(',', $item->old_data) as $value)
                                        <li>{{ trim($value) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer" style="background-color: #f8f9fa;">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- New Data Modal -->
                @if($item->new_data)
                <div class="modal fade" id="newDataModal{{ $item->id }}" tabindex="-1" aria-labelledby="newDataModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-sm">
                            <div class="modal-header text-white" style="background-color: #1f2937;">
                                <h5 class="modal-title" id="newDataModalLabel{{ $item->id }}" style="color: #1dd3b0;">
                                    <i class="fas fa-database me-2"></i>
                                    New Data - {{ $item->fromTableName }} (ID: {{ $item->id }})
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Action Type:</small><br>
                                            <strong>{{ $item->type }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Changed By:</small><br>
                                            <strong>{{ $item->changedBy }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="text-info mb-3">Current Data:</h6>
                                <div class="bg-white p-3 border rounded">
                                    <ul class="mb-0" style="font-size: 0.9rem;">
                                        @foreach(explode(',', $item->new_data) as $value)
                                        <li>{{ trim($value) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer" style="background-color: #f8f9fa;">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Detail Modal -->
                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content border-0 shadow-sm">
                            <div class="modal-header text-white" style="background-color: #1f2937;">
                                <h5 class="modal-title" id="detailModalLabel{{ $item->id }}" style="color: #1dd3b0;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Audit Details - {{ $item->fromTableName }} (ID: {{ $item->id }})
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light">
                                <!-- Summary Information -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-tag text-primary mb-2" style="font-size: 1.5rem;"></i>
                                                <h6 class="card-title">Action Type</h6>
                                                @switch($item->type)
                                                @case('INSERT')
                                                <span class="badge bg-success px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('UPDATE')
                                                <span class="badge bg-warning text-dark px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('DELETE')
                                                <span class="badge bg-danger px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @default
                                                <span class="badge bg-secondary px-3 py-2">{{ $item->type }}</span>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-user text-info mb-2" style="font-size: 1.5rem;"></i>
                                                <h6 class="card-title">Changed By</h6>
                                                <p class="card-text fs-5">{{ $item->changedBy }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-table text-warning mb-2" style="font-size: 1.5rem;"></i>
                                                <h6 class="card-title">Description</h6>
                                                <p class="card-text fs-5">{{ $item->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-clock text-success mb-2" style="font-size: 1.5rem;"></i>
                                                <h6 class="card-title">Date & Time</h6>
                                                <p class="card-text fs-5">{{ $item->time ? $item->time->format('M d, Y') : 'N/A' }}<br>{{ $item->time ? $item->time->format('h:i A') : '' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Comparison -->
                                <div class="row">
                                    @if($item->old_data)
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="mb-0"><i class="fas fa-arrow-left me-2"></i>Previous Data</h6>
                                            </div>
                                            <div class="card-body bg-white">
                                                <ul class="mb-0" style="font-size: 0.9rem;">
                                                    @foreach(explode(',', $item->old_data) as $value)
                                                    <li>{{ trim($value) }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($item->new_data)
                                    <div class="col-md-{{ $item->old_data ? '6' : '12' }}">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0"><i class="fas fa-arrow-right me-2"></i>Current Data</h6>
                                            </div>
                                            <div class="card-body bg-white">
                                                <ul class="mb-0" style="font-size: 0.9rem;">
                                                    @foreach(explode(',', $item->new_data) as $value)
                                                    <li>{{ trim($value) }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!$item->old_data && !$item->new_data)
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No data changes recorded for this audit entry.
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer" style="background-color: #1f2937;">
                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>



<div class="row mb-3 mt-5">
    <div class="col-12">
        <h4 class="fw-bold text-dark mb-3">
            <i class="fas fa-database me-2" style="color:#1dd3b0;"></i> Backup & Restore
        </h4>
    </div>
</div>

<div class="row">
    <!-- Backup Button -->
    <div class="col-md-6 mb-3">
        <button class="btn btn-lg w-100"
            style="background-color: #1f2937; border-color: #1f2937; color: white;"
            onclick="window.location.href='{{ route('backup.download') }}'">
            <i class="fas fa-download me-2"></i> Backup Database
        </button>
    </div>
</div>


{{-- Enhanced JavaScript with loading spinners and search functionality --}}
<script>
    // Fixed JavaScript for search and filter functionality
    document.addEventListener("DOMContentLoaded", function() {
        // Initial page load spinner
        const spinner = document.getElementById("spinner");
        const table = document.getElementById("auditTable");

        if (spinner && table) {
            spinner.style.display = "block";
            table.style.display = "none";

            setTimeout(() => {
                spinner.style.display = "none";
                table.style.display = "block";
            }, 600);
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const searchInfo = document.getElementById('searchInfo');
        const searchResultText = document.getElementById('searchResultText');
        const searchQuery = document.getElementById('searchQuery');
        const noResults = document.getElementById('noResults');
        const tableRows = document.querySelectorAll('.audit-row');
        const filterDropdown = document.getElementById('filterDropdown');
        const tableFilterDropdown = document.getElementById('tableFilterDropdown');

        let currentFilter = 'all';
        let currentTableFilter = 'all';
        let totalRows = tableRows.length;

        // Check if required elements exist
        if (!searchInput || !tableRows.length) {
            console.warn('Search elements not found');
            return;
        }

        // Search input event listener with debouncing
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300); // 300ms debounce
        });

        // Clear search button
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                clearSearch();
            });
        }

        // Filter dropdown options
        const filterOptions = document.querySelectorAll('.filter-option');
        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                currentFilter = this.getAttribute('data-filter');

                // Update dropdown button text
                if (filterDropdown) {
                    filterDropdown.innerHTML = '<i class="fas fa-filter me-1"></i>' + this.textContent + ' <i class="fas fa-chevron-down ms-1"></i>';
                }

                // Update active state
                filterOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                performSearch();
            });
        });

        // Table filter dropdown options
        const tableFilterOptions = document.querySelectorAll('.table-filter-option');
        tableFilterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                currentTableFilter = this.getAttribute('data-table');

                // Update dropdown button text
                if (tableFilterDropdown) {
                    const buttonText = currentTableFilter === 'all' ? 'Table Filter' : this.textContent;
                    tableFilterDropdown.innerHTML = '<i class="fas fa-table me-1"></i>' + buttonText + ' <i class="fas fa-chevron-down ms-1"></i>';
                }

                // Update active state
                tableFilterOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                performSearch();
            });
        });

        // Perform search function
        function performSearch() {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                let shouldShow = false;

                // First apply table filter
                let passesTableFilter = true;
                if (currentTableFilter !== 'all') {
                    const tableData = row.getAttribute('data-table');
                    passesTableFilter = tableData && tableData === currentTableFilter.toLowerCase();
                }

                // Then apply search filter only if table filter passes
                if (passesTableFilter) {
                    if (query === '') {
                        shouldShow = true;
                    } else {
                        // Search based on current filter
                        switch (currentFilter) {
                            case 'all':
                                shouldShow = searchAllColumns(row, query);
                                break;
                            case 'type':
                                const typeData = row.getAttribute('data-type');
                                shouldShow = typeData && typeData.includes(query);
                                break;
                            case 'user':
                                const userData = row.getAttribute('data-user');
                                shouldShow = userData && userData.includes(query);
                                break;
                            case 'table':
                                const tableData = row.getAttribute('data-table');
                                shouldShow = tableData && tableData.includes(query);
                                break;
                            case 'date':
                                const dateData = row.getAttribute('data-date');
                                shouldShow = dateData && dateData.includes(query);
                                break;
                            default:
                                shouldShow = searchAllColumns(row, query);
                        }
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
        }

        // Search all columns function - improved
        function searchAllColumns(row, query) {
            // Search in visible text content of the row
            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(query)) {
                return true;
            }

            // Also search in data attributes
            const searchableAttributes = [
                'data-type', 'data-user', 'data-table', 'data-date',
                'data-old-data', 'data-new-data'
            ];

            return searchableAttributes.some(attr => {
                const value = row.getAttribute(attr);
                return value && value.includes(query);
            });
        }

        // Update search info
        function updateSearchInfo(query, visibleCount) {
            if (!searchInfo || !searchResultText) return;

            if (query === '') {
                searchInfo.style.display = 'none';
                if (noResults) noResults.style.display = 'none';
            } else {
                searchInfo.style.display = 'block';
                if (searchQuery) searchQuery.textContent = `"${query}"`;

                if (visibleCount === 0) {
                    searchResultText.textContent = 'No records found for';
                    if (noResults) noResults.style.display = 'block';
                } else {
                    searchResultText.textContent = `Found ${visibleCount} of ${totalRows} records for`;
                    if (noResults) noResults.style.display = 'none';
                }
            }
        }

        // Clear search function
        window.clearSearch = function() {
            searchInput.value = '';
            currentFilter = 'all';
            currentTableFilter = 'all';

            if (filterDropdown) {
                filterDropdown.innerHTML = '<i class="fas fa-filter me-1"></i>Filter <i class="fas fa-chevron-down ms-1"></i>';
            }
            if (tableFilterDropdown) {
                tableFilterDropdown.innerHTML = '<i class="fas fa-table me-1"></i>Table Filter <i class="fas fa-chevron-down ms-1"></i>';
            }

            // Reset active states
            const allFilterOptions = document.querySelectorAll('.filter-option, .table-filter-option');
            allFilterOptions.forEach(opt => opt.classList.remove('active'));

            // Set default active states
            const defaultFilterOption = document.querySelector('.filter-option[data-filter="all"]');
            const defaultTableOption = document.querySelector('.table-filter-option[data-table="all"]');
            if (defaultFilterOption) defaultFilterOption.classList.add('active');
            if (defaultTableOption) defaultTableOption.classList.add('active');

            performSearch();
            searchInput.focus();
        }

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

        // Modal loading states
        const modalButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        modalButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.classList.add('loading');
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 200);
            });
        });

        // Initialize search on page load if there's a value
        if (searchInput.value.trim() !== '') {
            performSearch();
        }
    });
</script>

@endsection
