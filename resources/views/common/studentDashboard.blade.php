@extends('layout.studentpage')

@section('content')

<style>
    :root {
        --sidebar-width: 240px;
        --sidebar-collapsed-width: 80px;
    }

    body, html {
        background-color: #0f172a;
        color: #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    footer,
    .footer {
        display: none !important;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.4s ease;
        padding: 6rem 2rem 2rem;
    }

    body.sidebar-shrink .main-content {
        margin-left: var(--sidebar-collapsed-width);
    }

    .id-header h5 {
        font-size: 1.8rem;
        font-weight: 600;
        color: #1dd3b0;
        margin-bottom: 2rem;
    }


    .id-card {
        background-color: #1e293b;
        padding: 20px 25px;
        margin-bottom: 20px;
        border-radius: 12px;
        border: 2px solid #334155;
    }

    .id-card h5 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1dd3b0;
        margin-bottom: 20px;
        border-bottom: 2px solid #334155;
        padding-bottom: 10px;
    }

    .id-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .id-card li {
        font-size: 1rem;
        line-height: 1.8;
        padding: 8px 0;
        border-bottom: 1px solid #475569;
        display: flex;
        justify-content: space-between;
    }

    .id-card li:last-child {
        border-bottom: none;
    }

    .id-card li strong {
        color: #f1f5f9;
        font-weight: 600;
        width: 50%;
    }

    .card-container {
        background-color: #1e293b;
        border: 2px solid #334155;
        border-radius: 1rem;
        padding: 2rem;
        margin-top: 3rem;
    }

    .card-header-custom {
        background-color: transparent;
        padding-bottom: 1rem;
        border-bottom: 1px solid #334155;
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

    .badge {
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 8px;
    }

    .badge.bg-success {
        background-color: #16a34a;
        color: #fff;
    }

    .badge.bg-warning {
        background-color: #1dd3b0;
        color: #1f2937;
    }

    .badge.bg-secondary {
        background-color: #64748b;
        color: #f8fafc;
    }

    .pagination > .page-item > .page-link {
        background-color: #334155;
        color: #f8fafc;
        border: none;
    }

    .pagination > .page-item.active > .page-link {
        background-color: #1dd3b0;
        color: #1e293b;
        font-weight: bold;
    }

    .pagination > .page-item > .page-link:hover {
        background-color: #64748b;
    }

    .custom-table th,
    .custom-table td {
        border: 1px solid #64748b !important;
    }

    .custom-table {
        border: 1px solid #64748b;
        border-radius: 8px;
        overflow: hidden;
    }

    .text-accent {
        color: #1dd3b0 !important;
    }

        @media (max-width: 768px) {
        .main-content {
            padding-top: 7rem;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1rem;
            margin-left: 0 !important;
        }

        .id-header h5 {
            font-size: 1.3rem;
            text-align: center;
        }

        .id-header {
            margin-top: 1.5rem;
            padding-top: 1rem;
        }

        .id-card {
            margin-left: 0 !important;
        }

        .card-container {
            padding: 1.2rem;
            margin-top: 2rem;
        }

        .id-card li {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .id-card li strong {
            width: 100%;
        }
    }
</style>

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
                                        @if($item->claimer->full_name  == 'Blank Blank')
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
                                            @if($item->status == 'Completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($item->status == 'Ongoing')
                                                <span class="badge bg-warning">Ongoing</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
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
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-shrink');
            });
        }
    });
</script>

@endsection
