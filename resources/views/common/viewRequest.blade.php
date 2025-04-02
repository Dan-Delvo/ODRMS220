@extends('layout.studentpage')

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <h1 class="text-primary">All Requests: {{ $totalCount }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h4 class="mb-0 text-dark">All Requests</h4>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th>id</th>
                                    <th>Claimer</th>
                                    <th>Student</th>
                                    <th>Document</th>
                                    <th>School</th>
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
                                    <td>{{ $item->release_mode }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td>{{ $item->status }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $DocRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
