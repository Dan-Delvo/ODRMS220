@extends('layout.blankpage')

@section ('content')

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
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Users Total: {{ $user->count() }}</span></h1>
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
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Account id</th>
                                <th>Student id</th>
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
                                <td>{{ $item->std_students_id }}</td>
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
                                        <button type="submit" class="btn btn-danger me-2">Delete</button>
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
                <div class="d-flex justify-content-center mt-3">
                    {{ $user->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add this script to ensure Bootstrap alerts work properly --}}
@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000); // 5 seconds
        });
    });
</script>
@endpush

@endsection
