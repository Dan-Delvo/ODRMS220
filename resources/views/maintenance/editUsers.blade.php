@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">Edit User</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('users') }}">Users List</a></li>
            <li class="breadcrumb-item active">Edit User</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header bg-dark text-white">
                <h4>Edit User
                    <a href="{{ route('user') }}" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('user.update', $user->user_account_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- User Account ID -->
                    <div class="mb-3">
                        <label for="user_account_id" class="form-label">Account ID</label>
                        <input type="text" name="user_account_id" id="user_account_id" class="form-control" value="{{ $user->user_account_id }}" readonly>
                    </div>

                    <!-- Student ID -->
                    <div class="mb-3">
                        <label for="std_students_id" class="form-label">Student ID</label>
                        <input type="text" name="std_students_id" id="std_students_id" class="form-control" value="{{ $user->std_students_id }}">
                        @error('std_students_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $role->id == $user->roles->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email_address" class="form-label">Email Address</label>
                        <input type="email" name="email_address" id="email_address" class="form-control" value="{{ $user->email_address }}">
                        @error('email_address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ $user->username }}">
                        @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Save Button -->
                    <div>
                        <button type="submit" class="btn btn-primary float-end">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
