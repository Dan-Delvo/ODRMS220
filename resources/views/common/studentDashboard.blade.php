@extends('layout.studentpage')

@section('content')

<div class="main-content">
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Student Info FIRST on mobile, LEFT on desktop -->
            <div class="col-md-3 order-md-1 mb-4">
                <div class="id-header mb-3 ms-md-3 text-md-start text-center">
                    <h5>Student Dashboard</h5>
                </div>
                <div class="id-card ms-md-3">
                    <h5>{{ $studInfo->FirstName }} {{ $studInfo->LastName }}</h5>
                    <ul>
                        <li><strong>Student ID:</strong> <span>{{ $studInfo->id }}</span></li>
                        <li><strong>LRN:</strong> <span>{{ $studInfo->LRN }}</span></li>
                        <li><strong>Grade Level:</strong> <span>{{ $studInfo->Grade_level }}</span></li>
                        <li><strong>Status:</strong> <span>{{ $studInfo->Std_status }}</span></li>
                        <li><strong>Last SY Attended:</strong> <span>{{ $studInfo->Last_sy_attended }}</span></li>
                    </ul>
                </div>
            </div>

            <!-- Document Table SECOND on mobile, RIGHT on desktop -->
            <div class="col-md-9 order-md-2">
                <div class="card-container shadow">
                    <div class="card-header-custom mb-3">
                        <h4 class="text-accent fw-semibold mb-0">Your Document Requests</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered custom-table">
                            <thead>
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
                            <tbody>
                                @foreach ($DocRequests as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    @if($item->claimer->full_name == 'Blank Blank')
                                    <td> </td>
                                    @else
                                    <td>{{ $item->claimer->full_name }}</td>
                                    @endif
                                    <td>{{ $item->studentInformation->full_name }}</td>
                                    <td>{{ $item->documents->DocType }}</td>
                                    <td>{{ $item->request_schl_entity }}</td>
                                    <td>{{ $item->release_mode }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td>
                                        @if($item->status == 'Claimed')
                                        <span class="badge bg-success">Claimed</span>
                                        @elseif($item->status == 'Processing')
                                        <span class="badge bg-warning">Processing</span>
                                        @elseif($item->status == 'Pending')
                                        <span class="badge bg-secondary">Pending</span>
                                        @elseif($item->status == 'For Release')
                                        <span class="badge bg-info">For Release</span>
                                        @else
                                        <span class="badge bg-danger">Declined</span>
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

<script>
    // Sidebar shrink toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-shrink');
            });
        }
    });
</script>

@endsection