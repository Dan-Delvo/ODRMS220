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
                <div class="bg-white p-3 border rounded shadow-sm">
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
                    <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
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
                    {{-- Previous Data --}}
                    @if($item->old_data)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-arrow-left me-2"></i>Previous Data
                                </h6>
                            </div>
                            <div class="card-body bg-white">
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
                                <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
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
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-arrow-right me-2"></i>Current Data
                                </h6>
                            </div>
                            <div class="card-body bg-white">
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
                                <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
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
                        <div class="alert alert-info text-center">
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
