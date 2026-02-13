@extends('layout.blankpage')

@section ('content')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-create {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .page-header-create h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-create .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-create .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }

    .page-header-create .breadcrumb-item.active {
        color: #d1d5db;
    }

    .create-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .create-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .create-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .create-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .create-card-header .header-icon {
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

    .create-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .btn-back {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        color: white;
    }

    .create-card-body {
        padding: 1.5rem;
    }

    .create-card-body .form-label,
    .create-card-body label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .create-card-body .form-control,
    .create-card-body .form-select {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .create-card-body .form-control:focus,
    .create-card-body .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    .btn-save {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    @media (max-width: 767px) {
        .page-header-create {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-create h1 {
            font-size: 1.35rem;
        }

        .create-card {
            border-radius: 12px;
        }

        .create-card-header {
            padding: 0.875rem 1rem;
        }

        .create-card-body {
            padding: 1rem;
        }
    }

    @media (max-width: 575px) {
        .page-header-create h1 {
            font-size: 1.15rem;
        }

        .create-card-header h5 {
            font-size: 0.875rem;
        }

        .btn-back {
            font-size: 0.8rem;
            padding: 0.4rem 1rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-create">
        <h1><i class="fas fa-cogs me-2"></i>Ongoing Requests</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('ongoing') }}">Ongoing Requests</a></li>
            <li class="breadcrumb-item active">Create Request</li>
        </ol>
    </div>

    <!-- Create Card -->
    <div class="create-card">
        <div class="create-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-plus"></i></span>
                <h5>New Ongoing Request</h5>
            </div>
            <a href="{{ url('ongoing') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="create-card-body">
            <form action="{{ route('ongoing.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">ID</label>
                        <input type="text" name="id" class="form-control" />
                        @error('id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Claimer ID</label>
                        <input type="text" name="claimer_id" class="form-control" />
                        @error('claimer_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Student Information ID</label>
                        <input type="text" name="student_information_id" class="form-control" />
                        @error('student_information_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Document ID</label>
                        <input type="text" name="document_id" class="form-control" />
                        @error('document_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Requesting School / Entity</label>
                        <input type="text" name="request_schl_entity" class="form-control" />
                        @error('request_schl_entity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Requested SF10</label>
                        <input type="text" name="requested_sf10" class="form-control" />
                        @error('requested_sf10') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Release Mode</label>
                        <input type="text" name="release_mode" class="form-control" />
                        @error('release_mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" />
                        @error('remarks') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Status</label>
                        <input type="text" name="status" class="form-control" />
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
