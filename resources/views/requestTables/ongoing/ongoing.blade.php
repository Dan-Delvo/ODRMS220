@extends('layout.blankpage')

@section('content')

<div class="row mb-3">
    <div class="col-12 col-md-6">
        <h1 class="mt-4"><span class="badge text-bg-primary">Ongoing Requests</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Ongoing Requests</li>
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
                <h5 class="mb-2 mb-md-0">Ongoing Document Requests</h5>
                <span class="badge bg-primary fs-6">Total Ongoing: {{ $totalCount }}</span>
            </div>


            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-center">
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($DocRequests as $item)
                                <tr>
                                    <td class="text-center">{{ $item->id }}</td>
                                    <td>{{ $item->claimer->full_name }}</td>
                                    <td>{{ $item->studentInformation->full_name }}</td>
                                    <td>{{ $item->documents->DocType }}</td>
                                    <td>{{ $item->request_schl_entity }}</td>
                                    <td>{{ $item->request_mode }}</td>
                                    <td>{{ $item->release_mode }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td><span class="badge bg-primary text-white">{{ $item->status }}</span></td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-1">
                                            @if(!empty($approveOngoing))
                                                <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>

                                                <form action="{{ route('document-request2.complete', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                                </form>
                                            @endif

                                            @if(!empty($PermissionEdit))
                                                <a href="{{ route('ongoing.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @endif

                                            <a href="{{ route('ongoing.show', $item->id) }}" class="btn btn-sm btn-info">Info</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No ongoing requests found.</td>
                                </tr>
                            @endforelse
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
