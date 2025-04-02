@extends('layout.blankpage')

@section ('content')

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">All Requests</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="#" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">All Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">Requests Total: {{ $totalCount }}</h1>
    </div>
</div>

<!-- Date Range Filters for Reports -->
<div class="row mb-4">
    <div class="col-md-12">
        <form action="{{ route('generateReports') }}" method="GET" class="d-flex align-items-center">
            <label for="start_date" class="me-2 text-dark">Start Date:</label>
            <input type="date" id="start_date" name="start_date" class="form-control me-3" required>
            <label for="end_date" class="me-2 text-dark">End Date:</label>
            <input type="date" id="end_date" name="end_date" class="form-control me-3" required>
            <button type="submit" name="action" value="pdf" class="btn btn-primary me-2">Generate PDF</button>
            <button type="submit" name="action" value="excel" class="btn btn-success">Generate Excel</button>
        </form>
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

<!-- Completed Requests Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-black text-white">
                <h4>Requests</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
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
                                <td>{{ $item->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $DocRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
