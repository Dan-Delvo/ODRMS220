@extends('layout.blankpage')

@section('content')

<h1 class="mt-4">
    <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Student Details</span>
</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student') }}" class="text-dark">Students List</a></li>
    <li class="breadcrumb-item active">Student Details</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">
                    Student Information
                </h4>
                <a href="{{ route('student') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Back
                </a>
            </div>
            <div class="card-body bg-light">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $student->id }}</td>
                        </tr>
                        <tr>
                            <th>First Name</th>
                            <td>{{ $student->FirstName }}</td>
                        </tr>
                        <tr>
                            <th>Last Name</th>
                            <td>{{ $student->LastName }}</td>
                        </tr>
                        <tr>
                            <th>LRN</th>
                            <td>{{ $student->LRN }}</td>
                        </tr>
                        <tr>
                            <th>Grade Level</th>
                            <td>{{ $student->Grade_level }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $student->Std_status }}</td>
                        </tr>
                        <tr>
                            <th>Last SY Attended</th>
                            <td>{{ $student->Last_sy_attended }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
