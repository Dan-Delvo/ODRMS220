@extends('layout.blankpage')

@section('content')
@include('layout.partials.swal-loading')
@include('layout.partials.message')

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Students Information</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Students Information List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Students Total: {{ $user->total() }}</span></h1>
    </div>
</div>

<!-- Status Alerts -->
<div class="row mb-4">
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
    </div>
</div>

<!-- Search and Filter Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-6">
                            <label for="search" class="form-label fw-bold">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="Search by name, LRN, or grade level..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label fw-bold">Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Default Order</option>
                                <option value="LastName" {{ request('sort_by') == 'LastName' ? 'selected' : '' }}>Last Name</option>
                                <option value="FirstName" {{ request('sort_by') == 'FirstName' ? 'selected' : '' }}>First Name</option>
                                <option value="LRN" {{ request('sort_by') == 'LRN' ? 'selected' : '' }}>LRN</option>
                                <option value="Grade_level" {{ request('sort_by') == 'Grade_level' ? 'selected' : '' }}>Grade Level</option>
                                <option value="Last_sy_attended" {{ request('sort_by') == 'Last_sy_attended' ? 'selected' : '' }}>Last SY Attended</option>
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-3">
                            <label for="sort_order" class="form-label fw-bold">Sort Order</label>
                            <select name="sort_order" id="sort_order" class="form-select">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('sort_order', 'asc') == 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Students Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">Students Information</h4>
                <span class="badge bg-light text-dark">
                    Showing {{ $user->firstItem() ?? 0 }} - {{ $user->lastItem() ?? 0 }} of {{ $user->total() }}
                </span>
            </div>

            <div class="card-body">
                @if($user->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Suffix</th>
                                <th>LRN</th>
                                <th>Grade Level</th>
                                <th>Status</th>
                                <th>Last SY Attended</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user as $item)
                            <tr>
                                <td>{{ $item->LastName }}</td>
                                <td>{{ $item->FirstName }}</td>
                                <td>{{ $item->MiddleName }}</td>
                                <td>{{ $item->Suffix }}</td>
                                <td>{{ $item->LRN }}</td>
                                <td>{{ $item->Grade_level }}</td>
                                <td>{{ $item->Std_status}}</td>
                                <td>{{ $item->Last_sy_attended }}</td>
                                <td class="d-flex align-items-center">
                                    @if(!empty($PermissionEdit))
                                    <a href="{{ route('student.edit', ['id' => $item->id]) }}" class="btn btn-success me-2">Edit</a>
                                    @endif

                                    @if(!empty($PermissionDelete))
                                    <form action="{{ route('student.delete', $item->id) }}" method="POST" class="mb-0" data-swal-loading="true" data-swal-delete="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btb-delete me-2">Delete</button>
                                    </form>
                                    @endif

                                    @if(!empty($PermissionInfo))
                                    <a href="{{ route('student.show', ['id' => $item->id]) }}" class="btn btn-info">Info</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                    {{ $user->appends(request()->query())->links() }}
                    <small class="text-muted mt-2">
                        Showing {{ $user->firstItem() }} - {{ $user->lastItem() }} of {{ $user->total() }} results
                    </small>
                </div>
                @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No students found matching your search criteria.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto-submit on select change (Optional) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sortBy = document.getElementById('sort_by');
        const sortOrder = document.getElementById('sort_order');
        const filterForm = document.getElementById('filterForm');

        // Auto-submit when sort options change
        sortBy.addEventListener('change', function() {
            filterForm.submit();
        });

        sortOrder.addEventListener('change', function() {
            filterForm.submit();
        });

        // Optional: Submit on Enter key in search field
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
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
</style>

@endsection
