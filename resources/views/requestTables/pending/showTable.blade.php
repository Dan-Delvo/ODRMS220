@extends('layout.blankpage')

@section ('content')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-show {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .page-header-show h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-show .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-show .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-show .breadcrumb-item.active {
        color: #d1d5db;
    }

    .show-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .show-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .show-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .show-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .show-card-header .header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .show-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .btn-back {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-back:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .show-card-body {
        padding: 0;
    }

    .table-details {
        width: 100%;
        border-collapse: collapse;
    }

    .table-details tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
    }

    .table-details tr:last-child {
        border-bottom: none;
    }

    .table-details tr:hover {
        background-color: rgba(29, 211, 176, 0.03);
    }

    .table-details th {
        background: var(--primary-dark);
        color: white;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1.25rem;
        white-space: nowrap;
        width: 220px;
        vertical-align: top;
    }

    .table-details td {
        padding: 0.75rem 1.25rem;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: top;
    }

    @media (max-width: 991px) {
        .container-fluid.px-4 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }

    @media (max-width: 767px) {
        .container-fluid.px-4 {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 1rem !important;
        }

        .page-header-show {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-show h1 {
            font-size: 1.35rem;
        }

        .show-card {
            border-radius: 12px;
        }

        .show-card-header {
            padding: 0.875rem 1.25rem;
        }

        .table-details th {
            width: 160px;
            padding: 0.65rem 1rem;
            font-size: 0.75rem;
        }

        .table-details td {
            padding: 0.65rem 1rem;
            font-size: 0.82rem;
        }
    }

    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-show {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-show h1 {
            font-size: 1.15rem;
        }

        .page-header-show .breadcrumb {
            font-size: 0.75rem;
        }

        .show-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .show-card-header .header-left {
            justify-content: center;
        }

        .show-card-header h5 {
            font-size: 0.875rem;
        }

        .btn-back {
            text-align: center;
            display: block;
        }

        /* Stack table rows on small mobile */
        .table-details,
        .table-details tbody,
        .table-details tr,
        .table-details th,
        .table-details td {
            display: block;
            width: 100%;
        }

        .table-details tr {
            padding: 0.5rem 0;
        }

        .table-details th {
            border-radius: 0;
            padding: 0.5rem 1rem;
            font-size: 0.72rem;
        }

        .table-details td {
            padding: 0.5rem 1rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-show">
        <h1>Request Details</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('pending') }}">Pending Requests</a></li>
            <li class="breadcrumb-item active">View Request</li>
        </ol>
    </div>

    <!-- Show Card -->
    <div class="show-card">
        <div class="show-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-eye"></i></span>
                <h5>Requester's Information</h5>
            </div>
            <a href="{{ url('pending') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="show-card-body">
            <table class="table-details">
                <tr>
                    <th>ID</th>
                    <td>{{ $table->id }}</td>
                </tr>
                <tr>
                    <th>Claimer</th>
                    <td>{{ $table->claimer->full_name }}</td>
                </tr>
                <tr>
                    <th>Student Information ID</th>
                    <td>{{ $table->student_information_id }}</td>
                </tr>
                <tr>
                    <th>Document ID</th>
                    <td>{{ $table->document_id }}</td>
                </tr>
                <tr>
                    <th>Requesting School Entity</th>
                    <td>{{ $table->request_schl_entity }}</td>
                </tr>
                <tr>
                    <th>Requested SF10</th>
                    <td>{{ $table->requested_sf10 }}</td>
                </tr>
                <tr>
                    <th>Release Mode</th>
                    <td>{{ $table->release_mode }}</td>
                </tr>
                <tr>
                    <th>Remarks</th>
                    <td>{{ $table->remarks }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $table->status }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection
