
@extends('layout.blankpage')

@section ('content')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-docs {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .page-header-docs h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-docs .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-docs .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-docs .breadcrumb-item.active {
        color: #d1d5db;
    }

    .docs-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .docs-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .docs-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .docs-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .docs-card-header .header-icon {
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

    .docs-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .btn-add-doc {
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

    .btn-add-doc:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .docs-card-body {
        padding: 1.5rem;
    }

    .table-docs {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .table-docs thead th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border: none;
    }

    .table-docs tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-docs tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }

    .table-docs tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
    }

    .table-docs .btn {
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .table-docs .btn:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .table-docs .btn-success {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
    }

    .table-docs .btn-danger:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
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

        .page-header-docs {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-docs h1 {
            font-size: 1.35rem;
        }

        .docs-card {
            border-radius: 12px;
        }

        .docs-card-header {
            padding: 0.875rem 1.25rem;
        }

        .docs-card-body {
            padding: 0.75rem;
        }

        .docs-card-body .table-responsive {
            margin: 0 -0.75rem;
            padding: 0 0.75rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-docs {
            font-size: 0.8rem;
            min-width: 500px;
        }

        .table-docs thead th {
            font-size: 0.7rem;
            padding: 0.6rem 0.6rem;
            white-space: nowrap;
        }

        .table-docs tbody td {
            padding: 0.55rem 0.6rem;
        }

        .table-docs .btn {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }

        .btn-add-doc {
            font-size: 0.8rem;
            padding: 0.45rem 1rem;
        }
    }

    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-docs {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-docs h1 {
            font-size: 1.15rem;
        }

        .page-header-docs .breadcrumb {
            font-size: 0.75rem;
        }

        .docs-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .docs-card-header .header-left {
            justify-content: center;
        }

        .docs-card-header h5 {
            font-size: 0.875rem;
        }

        .btn-add-doc {
            text-align: center;
            display: block;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .docs-card-body {
            padding: 0.5rem;
        }

        .docs-card-body .table-responsive {
            margin: 0 -0.5rem;
            padding: 0 0.5rem;
        }

        .table-docs {
            font-size: 0.75rem;
            min-width: 420px;
        }

        .table-docs thead th {
            font-size: 0.65rem;
            padding: 0.5rem 0.5rem;
        }

        .table-docs tbody td {
            padding: 0.5rem 0.5rem;
        }

        .table-docs .btn {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }

        .table-docs td .d-flex {
            flex-direction: column;
            gap: 0.3rem;
        }

        .table-docs td .d-flex .me-2 {
            margin-right: 0 !important;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-docs">
        <h1>Document Types</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Document Types</li>
        </ol>
    </div>

    <!-- Document Types Table -->
    <div class="docs-card">
        <div class="docs-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-file-alt"></i></span>
                <h5>Document Types</h5>
            </div>
            <a href="{{route('doc.add')}}" class="btn-add-doc">
                <i class="fas fa-plus me-1"></i> Add Document
            </a>
        </div>

        <div class="docs-card-body">
            <div class="table-responsive">
                <table class="table table-hover table-docs">
                    <thead>
                        <tr>
                            <th>Document ID</th>
                            <th>Document Name</th>
                            <th>Document Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Doc as $item)
                        <tr>
                            <td><strong>{{ $loop->iteration + $Doc->firstItem() - 1 }}</strong></td>
                            <td>{{ $item->DocType }}</td>
                            <td>{{ $item->DocPrice }}</td>
                            <td class="d-flex justify-content-start">
                                <a href="{{ route('doc.edit', ['id' => $item->id]) }}" class="btn btn-success me-2">Edit</a>

                                <form action="{{ route('doc.destroy', $item->id) }}" method="POST" style="display:inline;" data-swal-loading="true" data-swal-delete="true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $Doc->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
