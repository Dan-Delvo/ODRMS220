@extends('layout.blankpage')

@section('content')

<div class="row mb-3">
    <div class="col-12">
        <h1 class="mt-4"><span class="badge text-bg-success">Completed Requests</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Completed Requests</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">

        @if (session('Status'))
            <div class="alert alert-success">{{ session('Status') }}</div>
        @endif

        @if (session('Danger'))
            <div class="alert alert-danger">{{ session('Danger') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="mb-2 mb-md-0">Completed Requests</h5>
                <span class="badge bg-success fs-6">Total Completed: {{ $totalCount }}</span>
            </div>

            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-nowrap">
                            <tr>
                                <th>ID</th>
                                <th>Claimer</th>
                                <th>Student</th>
                                <th>Document</th>
                                <th>School</th>
                                <th>Requested Via</th>
                                <th>Release Mode</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($DocRequests as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->claimer->full_name }}</td>
                                    <td>{{ $item->studentInformation->full_name }}</td>
                                    <td>{{ $item->documents->DocType }}</td>
                                    <td>{{ $item->request_schl_entity }}</td>
                                    <td>{{ $item->request_mode }}</td>
                                    <td>{{ $item->release_mode }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td><span class="badge bg-success text-white">{{ $item->status }}</span></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            @if (!empty($PermissionEdit))
                                                <a href="{{ route('tables.edit', $item->id) }}" class="btn btn-success btn-sm">Edit</a>
                                            @endif

                                            @if (!empty($deleteCompleted))
                                                <form action="{{ route('tables.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            @endif

                                            <a href="{{ route('tables.show', $item->id) }}" class="btn btn-info btn-sm">Info</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $DocRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
