@extends('layout.studentpage')

@section('content')
<div class="container mt-5">
    <!-- Student Information Section -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0">Student Dashboard</h4>
                </div>
                <div class="card-body bg-light">
                    <h5 class="card-title text-center fw-bold text-dark">{{ $studInfo->FirstName }} {{ $studInfo->LastName }}</h5>
                    <p class="text-center text-muted">{{ $studInfo->Grade_level }} - {{ $studInfo->Std_status }}</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-light"><strong>Student ID:</strong> {{ $studInfo->id }}</li>
                        <li class="list-group-item bg-light"><strong>LRN:</strong> {{ $studInfo->LRN }}</li>
                        <li class="list-group-item bg-light"><strong>Grade Level:</strong> {{ $studInfo->Grade_level }}</li>
                        <li class="list-group-item bg-light"><strong>Status:</strong> {{ $studInfo->Std_status }}</li>
                        <li class="list-group-item bg-light"><strong>Last School Year Attended:</strong> {{ $studInfo->Last_sy_attended }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Requests Table Section -->
    <div class="row mt-5">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white text-center">
                    <h5 class="mb-0">Your Document Requests</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Claimer</th>
                                    <th>Student</th>
                                    <th>Document</th>
                                    <th>School</th>
                                    <th>Release Mode</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="table-light">
                                @foreach ($DocRequests as $item)
                                    <tr class="shadow-sm">
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->claimer->full_name }}</td>
                                        <td>{{ $item->studentInformation->full_name }}</td>
                                        <td>{{ $item->documents->DocType }}</td>
                                        <td>{{ $item->request_schl_entity }}</td>
                                        <td>{{ $item->release_mode }}</td>
                                        <td>{{ $item->remarks }}</td>
                                        <td>
                                            @if($item->status == 'Completed')
                                                <span class="badge bg-success px-3 py-2 rounded-pill">Completed</span>
                                            @elseif($item->status == 'Ongoing')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Ongoing</span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2 rounded-pill">Pending</span>
                                            @endif
                                        </td>
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
