@extends('layout.blankpage')

@section ('content')
@include ('layout.partials.message')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-add {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .page-header-add h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-add .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-add .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-add .breadcrumb-item.active {
        color: #d1d5db;
    }

    .add-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .add-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .add-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .add-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .add-card-header .header-icon {
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

    .add-card-header h5 {
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

    .add-card-body {
        padding: 1.5rem;
    }

    .add-card-body .form-label {
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
    }

    .add-card-body .form-control,
    .add-card-body .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .add-card-body .form-control:focus,
    .add-card-body .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .btn-submit {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
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

        .page-header-add {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-add h1 {
            font-size: 1.35rem;
        }

        .add-card {
            border-radius: 12px;
        }

        .add-card-header {
            padding: 0.875rem 1.25rem;
        }

        .add-card-body {
            padding: 1rem;
        }

        .add-card-body .row .col-lg-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .btn-submit {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-add {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-add h1 {
            font-size: 1.15rem;
        }

        .page-header-add .breadcrumb {
            font-size: 0.75rem;
        }

        .add-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .add-card-header .header-left {
            justify-content: center;
        }

        .add-card-header h5 {
            font-size: 0.875rem;
        }

        .btn-back {
            text-align: center;
            display: block;
        }

        .add-card-body {
            padding: 0.875rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-add">
        <h1>Create Pending Request</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('pending') }}">Pending Requests</a></li>
            <li class="breadcrumb-item active">Create Request</li>
        </ol>
    </div>

    <!-- Add Card -->
    <div class="add-card">
        <div class="add-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-plus"></i></span>
                <h5>New Pending Request</h5>
            </div>
            <a href="{{ url('pending') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="add-card-body">
            <form action="{{ route('pending.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="mb-3 col-lg-6">
                        <label class="form-label">ID</label>
                        <input type="text" name="id" class="form-control">
                        @error('id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Claimer ID</label>
                        <input type="text" name="claimer_id" class="form-control">
                        @error('claimer_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Student Information ID</label>
                        <input type="text" name="student_information_id" class="form-control">
                        @error('student_information_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Document ID</label>
                        <input type="text" name="document_id" class="form-control">
                        @error('document_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Requesting School Entity</label>
                        <input type="text" name="request_schl_entity" class="form-control">
                        @error('request_schl_entity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Requested SF10</label>
                        <input type="text" name="requested_sf10" class="form-control">
                        @error('requested_sf10') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Release Mode</label>
                        <input type="text" name="release_mode" class="form-control">
                        @error('release_mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Status</label>
                        <input type="text" name="status" class="form-control">
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                    @error('remarks') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-submit float-end">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
