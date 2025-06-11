@extends('layout.studentpage')

@section('content')

<style>
    body, html {
        background-color: #0f172a;
        color: #e2e8f0;
    }

    .page-title {
        color: #1dd3b0; /* teal */
        font-weight: 600;
        font-size: 1.8rem;
    }

    .custom-table th {
        background-color: #334155;
        color: #f1f5f9;
    }

    .custom-table td {
        background-color: #1e293b;
        color: #e2e8f0;
        vertical-align: middle;
    }

    .custom-table tr:hover td {
        background-color: #475569;
    }

    .card-container {
        background-color: #1e293b;
        border: 2px solid #334155;
        border-radius: 1rem;
        padding: 2rem;
    }

    .card-header-custom {
        background-color: transparent;
        padding-bottom: 1rem;
        border-bottom: 1px solid #334155;
    }

    .pagination > .page-item > .page-link {
        background-color: #334155;
        color: #f8fafc;
        border: none;
    }

    .pagination > .page-item.active > .page-link {
        background-color: #1dd3b0; /* changed from #facc15 */
        color: #1e293b;
        font-weight: bold;
    }

    .pagination > .page-item > .page-link:hover {
        background-color: #64748b;
    }

    .custom-table th,
    .custom-table td {
        border: 1px solid #64748b !important; /* Soft slate border */
    }

    .custom-table {
        border: 1px solid #64748b; /* Optional: border around full table */
        border-radius: 8px;
        overflow: hidden;
    }

    /* Replace Bootstrap .text-warning to teal */
    .text-warning {
        color: #1dd3b0 !important;
    }

</style>

<div class="container-fluid py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <h1 class="page-title text-center">📋 All Requests: {{ $totalCount }}</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-container shadow">
                <div class="card-header-custom mb-3">
                    <h4 class="text-warning fw-semibold mb-0">All Document Requests</h4>
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

@endsection
