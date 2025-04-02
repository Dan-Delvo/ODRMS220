@extends('layout.blankpage')

@section('content')

<h1 class="mt-4">Student Details</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student') }}">Students List</a></li>
    <li class="breadcrumb-item active">Student Details</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header bg-dark text-white">
                <h4>Student Information
                    <a href="{{ route('student') }}" class="btn btn-danger float-end">Back</a>
                </h4>
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
