showUsers.blade.php
@extends('layout.blankpage')

@section('content')

<h1 class="mt-4">
    <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">User Details</span>
</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    <li class="breadcrumb-item active">User Details</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 70px;">
                <h4 class="mb-0">
                    User Information
                </h4>
                <a href="{{ route('user') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Back
                </a>
            </div>
            <div class="card-body bg-light">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>User Account ID</th>
                            <td>{{ $user->user_account_id }}</td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $user->std_students_id }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ $user->roles->name }}</td>
                        </tr>
                        <tr>
                            <th>Email Address</th>
                            <td>{{ $user->email_address }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th>Account Created At</th>
                            <td>{{ $user->account_created }}</td>
                        </tr>
                        <tr>
                            <th>Last Edited At</th>
                            <td>{{ $user->account_edited }}</td>
                        </tr>
                        <tr>
                            <th>Deleted At</th>
                            <td>{{ $user->deleted_at ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Encrypted Password</th>
                            <td>{{ $user->password }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
