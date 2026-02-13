@if($auditTrail->count() > 0)
<style>
    .table-audit {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
    }

    .table-audit thead th {
        background: #1f2937;
        color: white;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border: none;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-audit thead th:first-child {
        border-radius: 0;
    }

    .table-audit thead th:last-child {
        border-radius: 0;
    }

    .table-audit tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-audit tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.04);
    }

    .table-audit tbody td {
        padding: 0.65rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #374151;
        white-space: nowrap;
    }

    .table-audit .badge {
        border-radius: 50px;
        padding: 0.3rem 0.65rem;
        font-weight: 600;
        font-size: 0.72rem;
        letter-spacing: 0.02em;
    }

    .btn-view-old {
        background: transparent;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.3rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s;
    }

    .btn-view-old:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #374151;
    }

    .btn-view-new {
        background: transparent;
        border: 1px solid rgba(29, 211, 176, 0.4);
        border-radius: 8px;
        padding: 0.3rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 500;
        color: #17a98b;
        transition: all 0.2s;
    }

    .btn-view-new:hover {
        background: rgba(29, 211, 176, 0.06);
        border-color: #1dd3b0;
    }

    .btn-details {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 8px;
        padding: 0.3rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-details:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(29, 211, 176, 0.35);
        color: white;
    }

    /* ── Pagination ── */
    .audit-pagination {
        padding: 1rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .audit-pagination .pagination {
        margin: 0;
    }

    .audit-pagination .pagination .page-link {
        border-radius: 8px;
        margin: 0 0.15rem;
        border: 1px solid #e2e8f0;
        color: #374151;
        font-size: 0.82rem;
        padding: 0.35rem 0.7rem;
    }

    .audit-pagination .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border-color: transparent;
        color: white;
    }

    .audit-pagination .pagination .page-link:hover {
        background: rgba(29, 211, 176, 0.1);
        border-color: #1dd3b0;
        color: #17a98b;
    }

    /* ── Modals ── */
    .modal-audit .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-audit .modal-header {
        background: #1f2937;
        padding: 1rem 1.5rem;
        border: none;
    }

    .modal-audit .modal-title {
        color: #1dd3b0;
        font-weight: 600;
        font-size: 1rem;
    }

    .modal-audit .modal-body {
        padding: 1.5rem;
    }

    .modal-detail-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        text-align: center;
        padding: 1.25rem 0.75rem;
        height: 100%;
    }

    .modal-detail-card .detail-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .modal-detail-card .detail-icon.icon-type {
        background: rgba(99, 102, 241, 0.1);
        color: #6366f1;
    }

    .modal-detail-card .detail-icon.icon-user {
        background: rgba(29, 211, 176, 0.1);
        color: #17a98b;
    }

    .modal-detail-card .detail-icon.icon-table {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .modal-detail-card .detail-icon.icon-time {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .modal-detail-card h6 {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #9ca3af;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }

    .modal-detail-card p, .modal-detail-card .card-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .modal-data-section {
        background: white;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .modal-data-section .data-header {
        padding: 0.75rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: white;
    }

    .modal-data-section .data-header.old-data {
        background: #6b7280;
    }

    .modal-data-section .data-header.new-data {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
    }

    .modal-data-section .data-body {
        padding: 1rem;
    }

    .modal-data-section .data-body ul {
        font-size: 0.85rem;
    }

    .modal-data-section .data-body li {
        margin-bottom: 0.35rem;
        line-height: 1.5;
    }

    .empty-alert {
        border-radius: 12px;
        border: 1px solid rgba(29, 211, 176, 0.2);
        background: rgba(29, 211, 176, 0.05);
        color: #065f46;
        padding: 1.5rem;
        text-align: center;
        font-size: 0.875rem;
    }

    .empty-alert .btn {
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* ── Audit Table Responsive ── */
    @media (max-width: 767px) {
        .table-responsive {
            border-radius: 0;
        }

        .table-audit {
            min-width: 900px;
        }

        .table-audit thead th,
        .table-audit tbody td {
            padding: 0.5rem 0.75rem;
            font-size: 0.78rem;
        }

        .audit-pagination {
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 575px) {
        .table-audit {
            min-width: 800px;
        }

        .modal-audit .modal-body {
            padding: 1rem;
        }

        .modal-detail-card {
            padding: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .modal-detail-card .detail-icon {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
        }

        .audit-pagination .pagination .page-link {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<div class="table-responsive">
    <table class="table-audit">
        <thead>
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
                    <button class="btn-view-old" data-bs-toggle="modal" data-bs-target="#oldDataModal{{ $item->id }}">
                        <i class="fas fa-eye me-1"></i>View
                    </button>
                    @else
                    <span class="text-muted" style="font-size: 0.8rem;">N/A</span>
                    @endif
                </td>
                <td>
                    @if($item->new_data)
                    <button class="btn-view-new" data-bs-toggle="modal" data-bs-target="#newDataModal{{ $item->id }}">
                        <i class="fas fa-eye me-1"></i>View
                    </button>
                    @else
                    <span class="text-muted" style="font-size: 0.8rem;">N/A</span>
                    @endif
                </td>
                <td>
                    <button class="btn-details" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                        <i class="fas fa-info-circle me-1"></i>Details
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="audit-pagination">
    {{ $auditTrail->appends(request()->query())->links() }}
    <small class="text-muted">
        Showing {{ $auditTrail->firstItem() }} - {{ $auditTrail->lastItem() }} of {{ $auditTrail->total() }} records
    </small>
</div>
@else
<div style="padding: 1.5rem;">
    <div class="empty-alert">
        <i class="fas fa-info-circle me-2"></i>
        @if(request('search') || request('filter') != 'all' || request('action_type'))
        No audit trail records found matching your criteria.
        @else
        No audit trail records available.
        @endif
        @if(request('search') || request('filter') != 'all' || request('action_type'))
        <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-info ms-2" style="border-radius: 50px;">Clear Filters</a>
        @endif
    </div>
</div>
@endif

<!-- Data Modals -->
@foreach ($auditTrail as $item)
<!-- Old Data Modal -->
@if($item->old_data)
<div class="modal fade modal-audit" id="oldDataModal{{ $item->id }}" tabindex="-1" aria-labelledby="oldDataModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="oldDataModalLabel{{ $item->id }}">
                    <i class="fas fa-database me-2"></i>
                    Old Data - {{ $item->fromTableName }} (ID: {{ $item->id }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-type d-inline-flex"><i class="fas fa-tag"></i></div>
                            <h6>Action Type</h6>
                            <p>{{ $item->type }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-user d-inline-flex"><i class="fas fa-user"></i></div>
                            <h6>Changed By</h6>
                            <p>{{ $item->changedBy }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-data-section">
                    <div class="data-header old-data">
                        <i class="fas fa-arrow-left me-2"></i>Previous Data
                    </div>
                    <div class="data-body">
                        <ul class="mb-0">
                            @foreach(explode(',', $item->old_data) as $value)
                            <li>{{ trim($value) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- New Data Modal -->
@if($item->new_data)
<div class="modal fade modal-audit" id="newDataModal{{ $item->id }}" tabindex="-1" aria-labelledby="newDataModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newDataModalLabel{{ $item->id }}">
                    <i class="fas fa-database me-2"></i>
                    New Data - {{ $item->fromTableName }} (ID: {{ $item->id }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-type d-inline-flex"><i class="fas fa-tag"></i></div>
                            <h6>Action Type</h6>
                            <p>{{ $item->type }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-user d-inline-flex"><i class="fas fa-user"></i></div>
                            <h6>Changed By</h6>
                            <p>{{ $item->changedBy }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-data-section">
                    <div class="data-header new-data">
                        <i class="fas fa-arrow-right me-2"></i>Current Data
                    </div>
                    <div class="data-body">
                        @php
                        $data = json_decode($item->new_data, true);
                        if (!is_array($data)) {
                            $data = [];
                            $pairs = explode(',', $item->new_data);
                            foreach ($pairs as $pair) {
                                if (strpos($pair, ':') !== false) {
                                    [$key, $value] = explode(':', $pair, 2);
                                    $data[trim($key)] = trim($value);
                                }
                            }
                        }
                        @endphp

                        @if(!empty($data))
                        <ul class="list-unstyled mb-0">
                            @foreach($data as $key => $value)
                            <li class="mb-1">
                                <strong class="text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                <span class="text-secondary">{{ $value }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-muted mb-0">No data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Detail Modal -->
<div class="modal fade modal-audit" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $item->id }}">
                    <i class="fas fa-info-circle me-2"></i>
                    Audit Details - {{ $item->fromTableName }} (ID: {{ $item->id }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Summary Information -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-type d-inline-flex"><i class="fas fa-tag"></i></div>
                            <h6>Action Type</h6>
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
                    <div class="col-md-3 col-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-user d-inline-flex"><i class="fas fa-user"></i></div>
                            <h6>Changed By</h6>
                            <p>{{ $item->changedBy }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-table d-inline-flex"><i class="fas fa-table"></i></div>
                            <h6>Table</h6>
                            <p>{{ $item->fromTableName }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="modal-detail-card">
                            <div class="detail-icon icon-time d-inline-flex"><i class="fas fa-clock"></i></div>
                            <h6>Date & Time</h6>
                            <p style="font-size: 0.8rem;">{{ $item->time ? $item->time->format('M d, Y') : 'N/A' }}<br>{{ $item->time ? $item->time->format('h:i A') : '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="modal-data-section mb-3">
                    <div class="data-header new-data">
                        <i class="fas fa-file-alt me-2"></i>Description
                    </div>
                    <div class="data-body">
                        <p class="mb-0">{{ $item->description }}</p>
                    </div>
                </div>

                <!-- Data Comparison -->
                <div class="row g-3">
                    {{-- Previous Data --}}
                    @if($item->old_data)
                    <div class="col-md-6">
                        <div class="modal-data-section h-100">
                            <div class="data-header old-data">
                                <i class="fas fa-arrow-left me-2"></i>Previous Data
                            </div>
                            <div class="data-body">
                                @php
                                $oldData = json_decode($item->old_data, true);
                                if (!is_array($oldData)) {
                                    $oldData = [];
                                    $pairs = explode(',', $item->old_data);
                                    foreach ($pairs as $pair) {
                                        if (strpos($pair, ':') !== false) {
                                            [$key, $value] = explode(':', $pair, 2);
                                            $oldData[trim($key)] = trim($value);
                                        }
                                    }
                                }
                                @endphp

                                @if(!empty($oldData))
                                <ul class="list-unstyled mb-0">
                                    @foreach($oldData as $key => $value)
                                    <li class="mb-1">
                                        <strong class="text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                        <span class="text-secondary">{{ $value }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted mb-0">No previous data available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Current Data --}}
                    @if($item->new_data)
                    <div class="col-md-{{ $item->old_data ? '6' : '12' }}">
                        <div class="modal-data-section h-100">
                            <div class="data-header new-data">
                                <i class="fas fa-arrow-right me-2"></i>Current Data
                            </div>
                            <div class="data-body">
                                @php
                                $newData = json_decode($item->new_data, true);
                                if (!is_array($newData)) {
                                    $newData = [];
                                    $pairs = explode(',', $item->new_data);
                                    foreach ($pairs as $pair) {
                                        if (strpos($pair, ':') !== false) {
                                            [$key, $value] = explode(':', $pair, 2);
                                            $newData[trim($key)] = trim($value);
                                        }
                                    }
                                }
                                @endphp

                                @if(!empty($newData))
                                <ul class="list-unstyled mb-0">
                                    @foreach($newData as $key => $value)
                                    <li class="mb-1">
                                        <strong class="text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                        <span class="text-secondary">{{ $value }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted mb-0">No current data available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- No Data --}}
                    @if(!$item->old_data && !$item->new_data)
                    <div class="col-12">
                        <div class="empty-alert">
                            <i class="fas fa-info-circle me-2"></i>
                            No data changes recorded for this audit entry.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
