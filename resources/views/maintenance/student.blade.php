@extends('layout.blankpage')

@section ('content')

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">Students Information</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="#" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Students Information List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">Students Total: {{ $user->count() }}</h1>
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
            <div class="card-header bg-black text-white">
                <h4>Students Information
                    <a href="#" class="btn btn-light float-end text-dark">Add Student</a>
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Student ID</th>
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
                                <td>{{ $item->id }}</td>
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
                                    <form action="{{ route('student.delete', $item->id) }}" method="POST" class="mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger me-2">Delete</button>
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
                <div class="d-flex justify-content-center mt-3">
                    {{ $user->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
