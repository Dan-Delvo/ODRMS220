@extends('layout.blankpage')

@section('content')

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

    .btn-back-show {
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

    .btn-back-show:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .show-card-body {
        padding: 1.5rem;
    }

    .table-details {
        font-size: 0.875rem;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
    }

    .table-details th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border: none;
        width: 220px;
        vertical-align: middle;
    }

    .table-details td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
    }

    .table-details tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-details tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
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

        .show-card-body {
            padding: 1rem;
        }

        .table-details {
            font-size: 0.8rem;
        }

        .table-details th {
            font-size: 0.725rem;
            padding: 0.6rem 0.75rem;
            width: 160px;
        }

        .table-details td {
            padding: 0.6rem 0.75rem;
            word-break: break-word;
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

        .btn-back-show {
            text-align: center;
            display: block;
        }

        .show-card-body {
            padding: 0.5rem;
        }

        /* Stack table rows vertically on small screens */
        .table-details,
        .table-details thead,
        .table-details tbody,
        .table-details th,
        .table-details td,
        .table-details tr {
            display: block;
            width: 100%;
        }

        .table-details tr {
            margin-bottom: 0.5rem;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .table-details th {
            width: 100%;
            text-align: left;
            padding: 0.5rem 0.75rem;
            font-size: 0.7rem;
        }

        .table-details td {
            width: 100%;
            text-align: left;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            background: white;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-show">
        <h1>User Details</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">User Details</li>
        </ol>
    </div>

    <!-- Details Card -->
    <div class="show-card">
        <div class="show-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-info-circle"></i></span>
                <h5>User Information</h5>
            </div>
            <a href="{{ route('user') }}" class="btn-back-show">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="show-card-body">
            <div class="table-responsive">
                <table class="table table-details">
                    <tbody>
                        <tr>
                            <th>User Account ID</th>
                            <td>{{ $user->user_account_id }}</td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $user->std_students_id }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ $user->roles->name }}</td>
                        </tr>
                        <tr>
                            <th>Email Address</th>
                            <td>{{ $user->email_address }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th>Account Created At</th>
                            <td>{{ $user->account_created }}</td>
                        </tr>
                        <tr>
                            <th>Last Edited At</th>
                            <td>{{ $user->account_edited }}</td>
                        </tr>
                        <tr>
                            <th>Deleted At</th>
                            <td>{{ $user->deleted_at ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Encrypted Password</th>
                            <td>{{ $user->password }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
