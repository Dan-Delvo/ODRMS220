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

<!-- Students Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">Students Information</h4>

                <!-- Sorting Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="sortDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false" title="Sort by Student ID">
                        <i class="fas fa-sort me-1"></i>Sort
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item sort-option" href="#" data-sort="default">Default Order</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item sort-option" href="#" data-sort="asc">
                                <i class="fas fa-sort-numeric-down me-2"></i> Student ID (Ascending)
                            </a></li>
                        <li><a class="dropdown-item sort-option" href="#" data-sort="desc">
                                <i class="fas fa-sort-numeric-up me-2"></i> Student ID (Descending)
                            </a></li>
                    </ul>
                </div>
            </div>

            <div class="card-body">
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
                                <td>{{ $item->Suffix}}</td>
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
                <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                    {{ $user->links() }}
                    <small class="text-muted">
                        Showing {{ $user->firstItem() }} - {{ $user->lastItem() }} of {{ $user->total() }}
                    </small>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- Sorting Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const table = document.getElementById("studentsTable").getElementsByTagName("tbody")[0];
        const rows = Array.from(table.querySelectorAll("tr"));
        const originalOrder = [...rows]; // save default order

        document.querySelectorAll(".sort-option").forEach(option => {
            option.addEventListener("click", function(e) {
                e.preventDefault();
                const sortType = this.getAttribute("data-sort");

                let sortedRows;
                if (sortType === "asc") {
                    sortedRows = [...rows].sort((a, b) => {
                        return parseInt(a.cells[0].textContent) - parseInt(b.cells[0].textContent);
                    });
                } else if (sortType === "desc") {
                    sortedRows = [...rows].sort((a, b) => {
                        return parseInt(b.cells[0].textContent) - parseInt(a.cells[0].textContent);
                    });
                } else {
                    sortedRows = [...originalOrder];
                }

                // Clear table and re-append rows
                table.innerHTML = "";
                sortedRows.forEach(r => table.appendChild(r));
            });
        });
    });
</script>

@endsection