@extends('layout.studentpage')

@section('content')

<style>
    :root {
        --sidebar-width: 270px;
        --sidebar-collapsed-width: 85px;
    }

    body, html {
        background-color: #0f172a;
        color: #e2e8f0;
    }

    footer,
    .footer {
        display: none !important;
    }

    #main-content {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.4s ease;
        padding: 6rem 2rem 2rem;
    }

    body.sidebar-shrink #main-content {
        margin-left: var(--sidebar-collapsed-width);
    }

    .page-title {
        color: #1dd3b0;
        font-weight: 600;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .custom-table th {
        background-color: #334155;
        color: #f1f5f9;
        white-space: nowrap;
    }

    .custom-table td {
        background-color: #1e293b;
        color: #e2e8f0;
        vertical-align: middle;
        white-space: nowrap;
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
        width: 100%;
    }

    .text-warning {
        color: #1dd3b0 !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #main-content {
            margin-left: 0 !important;
            padding: 6.5rem 1rem 2rem;
        }

        .card-container {
            padding: 1rem;
        }

        .page-title {
            font-size: 1.4rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-header-custom h4 {
            font-size: 1.1rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .custom-table th,
        .custom-table td {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 480px) {
        .custom-table th, .custom-table td {
            font-size: 0.8rem;
            padding: 0.5rem;
        }
    }
</style>

<div id="main-content">
    <div class="container-fluid py-5">
        <h1 class="page-title">All Requests: {{ $totalCount }}</h1>

        <div class="col-12"> <!-- 🔄 Changed from centered row to full-width column -->
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

<script>
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
