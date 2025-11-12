<div>
    {{-- Main Card --}}
    <div class="card shadow-lg border-0 rounded-lg mt-3 archived-card">
        {{-- Card Header with Search/Filter Controls --}}
        <div class="card-header card-header-custom">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-archive"></i>
                <h5 class="mb-0 text-light">Archived Document Requests</h5>
            </div>

            {{-- Search/Filter Form --}}
            <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap align-items-center">
                {{-- Search Input --}}
                <div class="search-wrapper">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text"
                            class="form-control form-control-sm border-start-0 ps-0"
                            placeholder="Search requests..."
                            wire:model.live.debounce.300ms="search"
                            autocomplete="off">
                        @if($search)
                        <button class="btn btn-sm btn-outline-secondary"
                            type="button"
                            wire:click="$set('search', '')"
                            title="Clear search">
                            <i class="fas fa-times"></i>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Filter Dropdown --}}
                <div class="filter-wrapper">
                    <select wire:model.live="filter" class="form-select form-select-sm">
                        <option value="all">📋 All Fields</option>
                        <option value="school">🏫 School</option>
                        <option value="reqno">🔢 Req No.</option>
                        <option value="status">📊 Status</option>
                    </select>
                </div>

                {{-- Sort Dropdown --}}
                <div class="sort-wrapper">
                    <select wire:model.live="sort" class="form-select form-select-sm">
                        <option value="default">🕐 Latest</option>
                        <option value="asc">⬆️ A-Z</option>
                        <option value="desc">⬇️ Z-A</option>
                    </select>
                </div>

                {{-- Reset Button --}}
                <button type="button"
                    class="btn btn-sm btn-outline-light reset-btn"
                    wire:click="resetFilters"
                    title="Reset all filters">
                    <i class="fas fa-redo-alt"></i>
                </button>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body bg-light p-3">
            {{-- Search Info Banner --}}
            @if($search || $filter !== 'all' || $sort !== 'default')
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-3 py-2 search-info-banner" role="alert">
                <i class="fas fa-filter me-2"></i>
                <div class="flex-grow-1">
                    <small>
                        <strong>Active Filters:</strong>
                        @if($search)
                        <span class="badge bg-primary ms-1">Search: "{{ $search }}"</span>
                        @endif
                        @if($filter !== 'all')
                        <span class="badge bg-info ms-1">Field: {{ ucfirst($filter) }}</span>
                        @endif
                        @if($sort !== 'default')
                        <span class="badge bg-secondary ms-1">Sort: {{ $sort === 'asc' ? 'A-Z' : 'Z-A' }}</span>
                        @endif
                    </small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-info" wire:click="resetFilters">
                    <i class="fas fa-times me-1"></i> Clear All
                </button>
            </div>
            @endif

            {{-- Loading Spinner --}}
            <div wire:loading class="text-center my-4 loading-overlay">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Loading data...</p>
            </div>

            {{-- Table Container --}}
            <div class="table-responsive rounded shadow-sm" wire:loading.class="opacity-50">
                @if ($requests->isEmpty())
                <div class="empty-state text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Records Found</h5>
                    <p class="text-muted">
                        @if ($search)
                        No archived requests match your search criteria.<br>
                        Try adjusting your filters or search terms.
                        @else
                        There are no archived document requests to display.
                        @endif
                    </p>
                </div>
                @else
                <table class="table table-hover align-middle mb-0 modern-table">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 100px;">
                                <i class="fas fa-hashtag me-1"></i>Req No
                            </th>
                            <th class="text-center" style="width: 160px;">
                                <i class="fas fa-user-graduate me-1"></i>Student
                            </th>
                            <th class="text-center" style="width: 150px;">
                                <i class="fas fa-file-alt me-1"></i>Document
                            </th>
                            <th>
                                <i class="fas fa-school me-1"></i>School Name
                            </th>
                            <th class="text-center" style="width: 120px;">
                                <i class="fas fa-calendar me-1"></i>Request Date
                            </th>
                            <th class="text-center" style="width: 150px;">
                                <i class="fas fa-user me-1"></i>Claimer
                            </th>
                            <th class="text-center" style="width: 100px;">
                                <i class="fas fa-info-circle me-1"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                        <tr class="table-row-hover">
                            <td class="text-center">
                                <span class="badge bg-dark fw-bold">{{ $req->req_no }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user-graduate text-success me-2"></i>
                                    <span class="student-name">
                                        {{ strtoupper(optional($req->studentInformation)->full_name ?? 'N/A') }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-file-alt text-info me-2"></i>
                                    <span class="document-type" title="{{ optional($req->documents)->DocType }}">
                                        {{ optional($req->documents)->DocType ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    <span class="text-truncate" title="{{ $req->request_schl_entity }}">
                                        {{ strtoupper($req->request_schl_entity) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center text-muted">
                                <i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($req->request_date)->format('M d, Y') }}
                            </td>
                            <td class="text-center">
                                @if($req->claimer->full_name == 'Blank Blank')
                                <span class="badge bg-secondary">
                                    <i class="fas fa-user-slash me-1"></i>N/A
                                </span>
                                @else
                                <span class="claimer-name">
                                    <i class="fas fa-user-circle me-1"></i>{{ $req->claimer->full_name }}
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge status-badge-custom">
                                    {{ $req->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Modern Pagination --}}
            @if (!$requests->isEmpty())
            <div class="pagination-wrapper mt-4">
                <div class="row align-items-center g-2">
                    <div class="col-12 col-md-4 text-center text-md-start">
                        <small class="text-muted pagination-info">
                            <i class="fas fa-list-ol me-1"></i>
                            Showing <strong>{{ $requests->firstItem() }}</strong> to <strong>{{ $requests->lastItem() }}</strong> of <strong>{{ $requests->total() }}</strong> entries
                        </small>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="d-flex justify-content-center justify-content-md-end">
                            {{ $requests->links('vendor.livewire.custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
