@extends('layout.blankpage')

@section('content')

@include('layout.partials.message')

<!-- Consolidated Notification System -->
<div class="row mb-4">
    <div class="col-md-12">
        {{-- Alerts already handled --}}
    </div>
</div>

<!-- Title and Breadcrumb -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Users</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Users List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Users Total: {{ $user->total() }}</span>
        </h1>
    </div>
</div>

<!-- Users Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0" style="color: #e2e8f0;">
                    Users
                </h4>
                <div class="d-flex align-items-center gap-2">
                    <!-- Sorting Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="sortDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort me-1"></i>Sort
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item sort-option" href="#" data-sort="default">Default Order</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort="asc">
                                    <i class="fas fa-sort-numeric-down me-2"></i> Account ID (Ascending)
                                </a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort="desc">
                                    <i class="fas fa-sort-numeric-up me-2"></i> Account ID (Descending)
                                </a></li>
                        </ul>
                    </div>

                    <!-- Add User Button -->
                    <a href="{{ route('userStud.add') }}" class="btn text-black fw-semibold"
                        style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                        Add User
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Account id</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user as $item)
                            <tr>
                                <td>{{ $item->user_account_id }}</td>

                                @if(!$item->studentInformation)
                                <td class="text-danger">No Student Info</td>
                                @else
                                <td>{{ $item->studentInformation->full_name }}</td>
                                @endif

                                <td>{{ $item->roles->name }}</td>
                                <td>{{ $item->email_address }}</td>
                                <td>{{ $item->username }}</td>

                                <td class="d-flex justify-content-start">
                                    @if(!empty($PermissionEdit))
                                    <a href="{{ route('user.edit', ['id' => $item->user_account_id]) }}" class="btn btn-success me-2">Edit</a>
                                    @endif

                                    @if(!empty($PermissionDelete))
                                    <form action="{{ route('user.delete', $item->user_account_id) }}" method="POST" class="d-inline" data-swal-loading="true" data-swal-delete="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-delete me-2">Delete</button>
                                    </form>
                                    @endif

                                    @if(!empty($PermissionInfo))
                                    <a href="{{ route('user.show', ['id' => $item->user_account_id]) }}" class="btn btn-info">Info</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
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
        const table = document.getElementById("usersTable").getElementsByTagName("tbody")[0];
        const rows = Array.from(table.querySelectorAll("tr"));
        const originalOrder = [...rows]; // keep default order

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

                table.innerHTML = "";
                sortedRows.forEach(r => table.appendChild(r));
            });
        });
    });
</script>

<!-- Delete Confirmation -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-delete").forEach(button => {
            button.addEventListener("click", function() {
                let form = this.closest("form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "The user accounts connected to this role will also be deleted",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1dd3b0",
                    cancelButtonColor: "#1f2937",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Final Confirmation",
                            text: "This action cannot be undone!",
                            icon: "error",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#1f2937",
                            confirmButtonText: "Yes, I understand",
                            cancelButtonText: "Cancel"
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                });
            });
        });
    });
</script>

@endsection