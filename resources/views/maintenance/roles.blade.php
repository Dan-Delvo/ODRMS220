
@extends('layout.blankpage')

@section ('content')

@include('layout.partials.message')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-roles {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header-roles h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-roles .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-roles .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-roles .breadcrumb-item.active {
        color: #d1d5db;
    }

    .total-counter {
        background: rgba(29, 211, 176, 0.15);
        border: 1px solid rgba(29, 211, 176, 0.3);
        border-radius: 12px;
        padding: 0.5rem 1.25rem;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .total-counter span {
        color: #1dd3b0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .roles-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .roles-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .roles-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .roles-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .roles-card-header .header-icon {
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

    .roles-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .btn-add-role {
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

    .btn-add-role:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .roles-card-body {
        padding: 1.5rem;
    }

    .table-roles {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .table-roles thead th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border: none;
    }

    .table-roles tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-roles tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }

    .table-roles tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
    }

    .table-roles .btn {
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .table-roles .btn:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .table-roles .btn-success {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
    }

    .table-roles .btn-danger:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
    }

    @media (max-width: 767px) {
        .page-header-roles {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-roles h1 {
            font-size: 1.35rem;
        }

        .total-counter {
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
        }

        .total-counter span {
            font-size: 1.1rem;
        }

        .roles-card {
            border-radius: 12px;
        }

        .roles-card-header {
            padding: 0.875rem 1.25rem;
        }

        .roles-card-body {
            padding: 1rem;
        }

        .table-roles {
            font-size: 0.8rem;
        }

        .table-roles thead th {
            font-size: 0.725rem;
            padding: 0.6rem 0.75rem;
        }

        .table-roles tbody td {
            padding: 0.6rem 0.75rem;
        }

        .table-roles .btn {
            font-size: 0.725rem;
            padding: 0.35rem 0.75rem;
        }

        .btn-add-role {
            font-size: 0.8rem;
            padding: 0.45rem 1rem;
        }
    }

    @media (max-width: 575px) {
        .page-header-roles h1 {
            font-size: 1.15rem;
        }

        .roles-card-header h5 {
            font-size: 0.875rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-roles">
        <div>
            <h1>Roles</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Roles List</li>
            </ol>
        </div>
        <div class="total-counter">
            Total: <span>{{ $roles->count() }}</span>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="roles-card">
        <div class="roles-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-user-shield"></i></span>

            </div>
            <a href="{{ route('role.add') }}" class="btn-add-role">
                <i class="fas fa-plus me-1"></i> Add Role
            </a>
        </div>

        <div class="roles-card-body">
            <div class="table-responsive">
                <table class="table table-hover table-roles">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $item)
                        <tr>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>
                                <a href="{{ route('role.edit', ['id' => $item->id]) }}" class="btn btn-success">Edit</a>
                                @if(!in_array($item->id, [1, 2, 4]))
                                <form action="{{ route('role.delete', ['id' => $item->id]) }}" method="POST" class="d-inline"
                                    data-swal-loading="true"
                                    data-swal-delete="true"
                                    data-swal-delete-title="Delete Role?"
                                    data-swal-delete-text="The user accounts connected to this role will also be deleted. This action cannot be undone!">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                @elseif(in_array($item->id, [1, 2, 4]))
                                <button type="button" class="btn btn-danger" disabled>Delete</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
