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
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;" id="totalRecords">Total Records: {{ $auditTrail->total() }}</span>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">

        @if(session('Status'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('Status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('Danger'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('Danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3">
                        <!-- Search Input with Clear Button -->
                        <div class="col-md-5">
                            <label for="search" class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i>Search
                            </label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="Search audit logs..."
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Filter Type -->
                        <div class="col-md-4">
                            <label for="filter" class="form-label fw-bold">
                                <i class="fas fa-filter me-1"></i>Search In
                            </label>
                            <select name="filter" id="filter" class="form-select">
                                <option value="all" {{ request('filter', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
                                <option value="type" {{ request('filter') == 'type' ? 'selected' : '' }}>Action Type</option>
                                <option value="user" {{ request('filter') == 'user' ? 'selected' : '' }}>Changed By</option>
                                <option value="table" {{ request('filter') == 'table' ? 'selected' : '' }}>Table Name</option>
                                <option value="date" {{ request('filter') == 'date' ? 'selected' : '' }}>Date</option>
                            </select>
                        </div>

                        <!-- Action Type Filter -->
                        <div class="col-md-3">
                            <label for="action_type" class="form-label fw-bold">
                                <i class="fas fa-tag me-1"></i>Action Type
                            </label>
                            <select name="action_type" id="action_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="CREATE" {{ request('action_type') == 'CREATE' ? 'selected' : '' }}>Create</option>
                                <option value="UPDATE" {{ request('action_type') == 'UPDATE' ? 'selected' : '' }}>Update</option>
                                <option value="DELETE" {{ request('action_type') == 'DELETE' ? 'selected' : '' }}>Delete</option>
                                <option value="LOGIN" {{ request('action_type') == 'LOGIN' ? 'selected' : '' }}>Login</option>
                                <option value="BACKUP" {{ request('action_type') == 'BACKUP' ? 'selected' : '' }}>Back Up</option>
                                <option value="RESTORE" {{ request('action_type') == 'RESTORE' ? 'selected' : '' }}>Restore</option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-md-12">
                            <a href="#" id="resetFilters" class="btn text-white" style="background-color: #1f2937;">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" style="display: none;">
            <div class="alert alert-info alert-dismissible fade show mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Active Filters:</strong>
                <span id="filterBadges"></span>
                <a href="#" id="clearAllFilters" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
                <button type="button" class="btn-close" id="closeActiveFilters"></button>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" style="display: none;" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading audit records...</p>
        </div>

        <div class="card shadow-lg border-0 rounded-lg" id="auditTableCard">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #1f2937;">
                <h5 class="mb-0">System Audit Log</h5>
                <span class="badge bg-light text-dark" id="recordsInfo">
                    @if($auditTrail->count() > 0)
                    Showing {{ $auditTrail->firstItem() }} - {{ $auditTrail->lastItem() }} of {{ $auditTrail->total() }}
                    @else
                    No records
                    @endif
                </span>
            </div>

            <div class="card-body bg-light" id="auditTableBody">
                @include('common.audit_table', ['auditTrail' => $auditTrail])
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

    <!-- Restore Form -->
    <div class="col-md-6 mb-3">
        <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="backup_file" class="form-label fw-bold">Select Backup File</label>
                <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".zip" required>
            </div>
            <button type="submit" class="btn btn-lg w-100"
                style="background-color: #dc3545; border-color: #dc3545; color: white;">
                <i class="fas fa-upload me-2"></i> Restore Database
            </button>
        </form>
    </div>
</div>

<!-- AJAX Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let searchTimeout;
        const searchInput = $('#search');
        const filterSelect = $('#filter');
        const actionTypeSelect = $('#action_type');
        const clearSearchBtn = $('#clearSearch');
        const loadingIndicator = $('#loadingIndicator');
        const auditTableCard = $('#auditTableCard');

        // Show/hide clear button based on input value
        function toggleClearButton() {
            if (searchInput.val().length > 0) {
                clearSearchBtn.show();
            } else {
                clearSearchBtn.hide();
            }
        }

        // Update active filters display
        function updateActiveFilters() {
            const search = searchInput.val();
            const filter = filterSelect.val();
            const actionType = actionTypeSelect.val();
            
            let badges = '';
            let hasFilters = false;

            if (search) {
                badges += `<span class="badge bg-primary me-1">Search: "${search}"</span>`;
                hasFilters = true;
            }
            if (filter && filter !== 'all') {
                badges += `<span class="badge bg-success me-1">Search In: ${filter.charAt(0).toUpperCase() + filter.slice(1)}</span>`;
                hasFilters = true;
            }
            if (actionType) {
                badges += `<span class="badge bg-warning text-dark me-1">Type: ${actionType}</span>`;
                hasFilters = true;
            }

            if (hasFilters) {
                $('#filterBadges').html(badges);
                $('#activeFilters').show();
            } else {
                $('#activeFilters').hide();
            }
        }

        // Clear all filters
        function clearAllFilters() {
            searchInput.val('');
            filterSelect.val('all');
            actionTypeSelect.val('');
            toggleClearButton();
            loadAuditTrail();
        }

        // Load audit trail data via AJAX
        function loadAuditTrail(page = 1) {
            const search = searchInput.val();
            const filter = filterSelect.val();
            const actionType = actionTypeSelect.val();

            // Show loading, hide table
            loadingIndicator.show();
            auditTableCard.css('opacity', '0.5');

            $.ajax({
                url: '{{ route("audit.index") }}',
                method: 'GET',
                data: {
                    search: search,
                    filter: filter,
                    action_type: actionType,
                    page: page,
                    ajax: 1
                },
                success: function(response) {
                    // Update table body
                    $('#auditTableBody').html(response.html);
                    
                    // Update total records badge
                    $('#totalRecords').text('Total Records: ' + response.total);
                    
                    // Update records info
                    if (response.count > 0) {
                        $('#recordsInfo').text(`Showing ${response.from} - ${response.to} of ${response.total}`);
                    } else {
                        $('#recordsInfo').text('No records');
                    }
                    
                    // Update active filters
                    updateActiveFilters();
                    
                    // Hide loading, show table
                    loadingIndicator.hide();
                    auditTableCard.css('opacity', '1');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    loadingIndicator.hide();
                    auditTableCard.css('opacity', '1');
                    alert('An error occurred while loading the audit trail. Please try again.');
                }
            });
        }

        // Search input with debounce
        searchInput.on('input', function() {
            toggleClearButton();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadAuditTrail();
            }, 500);
        });

        // Clear search button
        clearSearchBtn.on('click', function() {
            searchInput.val('');
            toggleClearButton();
            loadAuditTrail();
        });

        // Filter dropdowns - instant change
        filterSelect.on('change', function() {
            loadAuditTrail();
        });

        actionTypeSelect.on('change', function() {
            loadAuditTrail();
        });

        // Close active filters alert
        $('#closeActiveFilters').on('click', function() {
            $('#activeFilters').hide();
        });

        // Clear All button - AJAX version
        $(document).on('click', '#clearAllFilters', function(e) {
            e.preventDefault();
            clearAllFilters();
        });

        // Reset button - AJAX version
        $(document).on('click', '#resetFilters', function(e) {
            e.preventDefault();
            clearAllFilters();
        });

        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            loadAuditTrail(page);
            
            // Scroll to top of table
            $('html, body').animate({
                scrollTop: auditTableCard.offset().top - 100
            }, 300);
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl+F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
            // ESC to clear search
            if (e.key === 'Escape' && searchInput.val() !== '') {
                e.preventDefault();
                clearAllFilters();
                searchInput.focus();
            }
        });

        // Initial state
        toggleClearButton();
        updateActiveFilters();
    });
</script>

<style>
    .form-label {
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-weight: 500;
    }

    #clearSearch {
        border-left: 0;
    }

    #clearSearch:hover {
        background-color: #e9ecef;
    }

    .input-group .form-control:focus+#clearSearch {
        border-color: #86b7fe;
    }
</style>

@endsection