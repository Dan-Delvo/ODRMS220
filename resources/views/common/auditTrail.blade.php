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
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Records: {{ $auditTrail->total() }}</span>
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
                <form action="{{ route('audit.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-5">
                            <label for="search" class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i>Search
                            </label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search audit logs..."
                                value="{{ request('search') }}">
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

                        <!-- Filter Buttons -->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                            <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request('search') || request('filter') != 'all' || request('action_type'))
        <div class="alert alert-info alert-dismissible fade show mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Active Filters:</strong>
            @if(request('search'))
                <span class="badge bg-primary me-1">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('filter') && request('filter') != 'all')
                <span class="badge bg-success me-1">Search In: {{ ucfirst(request('filter')) }}</span>
            @endif
            @if(request('action_type'))
                <span class="badge bg-warning text-dark me-1">Type: {{ request('action_type') }}</span>
            @endif
            <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear All</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #1f2937;">
                <h5 class="mb-0">System Audit Log</h5>
                <span class="badge bg-light text-dark">
                    @if($auditTrail->count() > 0)
                        Showing {{ $auditTrail->firstItem() }} - {{ $auditTrail->lastItem() }} of {{ $auditTrail->total() }}
                    @else
                        No records
                    @endif
                </span>
            </div>

            <div class="card-body bg-light">
                @if($auditTrail->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover align-middle text-nowrap" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th>Audit No.</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Changed By</th>
                                <th>Date/Time</th>
                                <th>Old Data</th>
                                <th>New Data</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auditTrail as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($auditTrail->currentPage() - 1) * $auditTrail->perPage() }}</td>
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
                                    @case('LOGIN')
                                    <span class="badge bg-info px-2 py-1">{{ $item->type }}</span>
                                    @break
                                    @case('BACKUP')
                                    <span class="badge bg-primary px-2 py-1">{{ $item->type }}</span>
                                    @break
                                    @case('RESTORE')
                                    <span class="badge bg-dark px-2 py-1">{{ $item->type }}</span>
                                    @break
                                    @default
                                    <span class="badge bg-secondary px-2 py-1">{{ $item->type }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $item->description }}</td>
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
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                        <i class="fas fa-info-circle me-1"></i>Details
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                    {{ $auditTrail->appends(request()->query())->links() }}
                    <small class="text-muted mt-2">
                        Showing {{ $auditTrail->firstItem() }} - {{ $auditTrail->lastItem() }} of {{ $auditTrail->total() }} records
                    </small>
                </div>
                @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    @if(request('search') || request('filter') != 'all' || request('action_type'))
                        No audit trail records found matching your criteria.
                    @else
                        No audit trail records available.
                    @endif
                    @if(request('search') || request('filter') != 'all' || request('action_type'))
                        <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-info ms-2">Clear Filters</a>
                    @endif
                </div>
                @endif

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
                                                @case('CREATE')
                                                <span class="badge bg-success px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('UPDATE')
                                                <span class="badge bg-warning text-dark px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('DELETE')
                                                <span class="badge bg-danger px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('LOGIN')
                                                <span class="badge bg-info px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('BACKUP')
                                                <span class="badge bg-primary px-3 py-2">{{ $item->type }}</span>
                                                @break
                                                @case('RESTORE')
                                                <span class="badge bg-dark px-3 py-2">{{ $item->type }}</span>
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
                                                <p class="card-text fs-6">{{ $item->changedBy }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-table text-warning mb-2" style="font-size: 1.5rem;"></i>
                                                <h6 class="card-title">Table</h6>
                                                <p class="card-text fs-6">{{ $item->fromTableName }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-clock text-success mb-2" style="font-size: 1.5rem;"></i>
                                                <h6 class="card-title">Date & Time</h6>
                                                <p class="card-text" style="font-size: 0.85rem;">{{ $item->time ? $item->time->format('M d, Y') : 'N/A' }}<br>{{ $item->time ? $item->time->format('h:i A') : '' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="text-secondary mb-2"><i class="fas fa-file-alt me-2"></i>Description:</h6>
                                                <p class="mb-0">{{ $item->description }}</p>
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

<!-- Auto-submit and keyboard shortcuts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterForm = document.getElementById('filterForm');
    const filterSelect = document.getElementById('filter');
    const actionTypeSelect = document.getElementById('action_type');
    const searchInput = document.getElementById('search');

    // Auto-submit when filter dropdowns change
    filterSelect.addEventListener('change', function() {
        filterForm.submit();
    });

    actionTypeSelect.addEventListener('change', function() {
        filterForm.submit();
    });

    // Submit on Enter key in search field
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterForm.submit();
        }
    });

    // Keyboard shortcut: Ctrl+F to focus search
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
        }
        // ESC to clear search and focus
        if (e.key === 'Escape' && searchInput.value !== '') {
            e.preventDefault();
            searchInput.value = '';
            searchInput.focus();
        }
    });
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
</style>

@endsection
