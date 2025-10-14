@extends('layout.blankpage')

@section ('content')

@include('layout.partials.message')
<!-- Consolidated Notification System -->
<div class="row mb-4">
    <div class="col-md-12">
        {{-- Enhanced notification system --}}
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning!</strong> {{ session('warning') }}
                @if(session('warning_details'))
                    <br><small>{{ session('warning_details') }}</small>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                @if(session('success_details'))
                    <br><small>{{ session('success_details') }}</small>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                @if(session('error_details'))
                    <br><small>{{ session('error_details') }}</small>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Legacy notification system for backward compatibility --}}
        @if(session('Status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('Status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('Danger'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('Danger') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Success message from controller update method --}}
        @if(session('Success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('Success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
</div>

<!-- Title and Breadcrumb -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Users</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Users List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Users Total: {{ $user->total() }}</span></h1>
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
                                    placeholder="Search by name, email, username, or account ID..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label fw-bold">Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="user_account_id" {{ request('sort_by') == 'user_account_id' ? 'selected' : '' }}>Account ID</option>
                                <option value="username" {{ request('sort_by') == 'username' ? 'selected' : '' }}>Username</option>
                                <option value="email_address" {{ request('sort_by') == 'email_address' ? 'selected' : '' }}>Email</option>
                                <option value="role_id" {{ request('sort_by') == 'role_id' ? 'selected' : '' }}>Role</option>
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

<!-- Users Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0" style="color: #e2e8f0;">
                    Users
                </h4>
                <a href="{{ route('userStud.add') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Add User
                </a>
            </div>

            <div class="card-body">
                @if($user->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
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
                                    <form action="{{ route('user.delete', $item->user_account_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                    {{ $user->appends(request()->query())->links() }}
                    <small class="text-muted mt-2">
                        Showing {{ $user->firstItem() }} - {{ $user->lastItem() }} of {{ $user->total() }}
                    </small>
                </div>
                @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No users found matching your search criteria.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto-submit on select change -->
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

        // Submit on Enter key in search field
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });

        // Delete confirmation script
        document.querySelectorAll(".btn-delete").forEach(button => {
            button.addEventListener("click", function(e) {
                let form = this.closest("form");

                // First confirmation
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
                        // Second confirmation
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
