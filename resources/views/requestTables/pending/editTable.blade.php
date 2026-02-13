@extends('layout.blankpage')

@section('content')
@include('layout.partials.swal-loading')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-edit {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .page-header-edit h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-edit .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-edit .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-edit .breadcrumb-item.active {
        color: #d1d5db;
    }

    .edit-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .edit-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .edit-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .edit-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .edit-card-header .header-icon {
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

    .edit-card-header h5 {
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

    .edit-card-body {
        padding: 1.5rem;
    }

    .edit-card-body .form-label {
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
    }

    .edit-card-body .form-control,
    .edit-card-body .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .edit-card-body .form-control:focus,
    .edit-card-body .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .edit-card-body .form-control[readonly] {
        background-color: #f9fafb;
        cursor: not-allowed;
        opacity: 0.75;
        border-style: dashed;
    }

    .locked-hint {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }

    .locked-hint i {
        color: #d1d5db;
    }

    .btn-save {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 2rem;
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

        .page-header-edit {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-edit h1 {
            font-size: 1.35rem;
        }

        .edit-card {
            border-radius: 12px;
        }

        .edit-card-header {
            padding: 0.875rem 1.25rem;
        }

        .edit-card-body {
            padding: 1rem;
        }

        .edit-card-body .row .col-lg-6,
        .edit-card-body .row .col-lg-8,
        .edit-card-body .row .col-lg-4,
        .edit-card-body .row .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .btn-save {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-edit {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-edit h1 {
            font-size: 1.15rem;
        }

        .page-header-edit .breadcrumb {
            font-size: 0.75rem;
        }

        .edit-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .edit-card-header .header-left {
            justify-content: center;
        }

        .edit-card-header h5 {
            font-size: 0.875rem;
        }

        .btn-back {
            text-align: center;
            display: block;
        }

        .edit-card-body {
            padding: 0.875rem;
        }

        .edit-card-body .form-label {
            font-size: 0.75rem;
        }

        .edit-card-body .form-control,
        .edit-card-body .form-select {
            font-size: 0.8rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-edit">
        <h1>Edit Pending Request</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pending.index') }}">Pending Requests</a></li>
            <li class="breadcrumb-item active">Edit Request</li>
        </ol>
    </div>

    <!-- Edit Card -->
    <div class="edit-card">
        <div class="edit-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-edit"></i></span>
                <h5>Edit Request</h5>
            </div>
            <a href="{{ route('pending.index') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="edit-card-body">
            <form action="{{ route('pending.update', $pending->id) }}" method="POST" data-swal-loading="true">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" value="{{ $pending->id }}">

                <div class="row">
                    <div class="mb-3 col-lg-6">
                        <label class="form-label d-flex align-items-center">
                            Claimer
                            <span id="claimerLockIcon" class="ms-2" style="display: none;">
                                <i class="fas fa-lock" style="color: #d1d5db; font-size: 0.75rem;"></i>
                            </span>
                        </label>
                        <input type="text" name="claimer_id" id="claimer_id" class="form-control"
                            value="{{ $pending->claimer->full_name }} " readonly>
                        <small id="claimerHelp" class="locked-hint" style="display: none;">
                            <i class="fas fa-lock me-1"></i> This field is locked while the request is pending.
                        </small>
                        @error('claimer_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Requested Document --}}
                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Requested Document</label>
                        <select class="form-control" name="document_id">
                            @foreach($DocType as $doc)
                            <option value="{{ $doc->id }}" {{ $doc->id == $pending->doc_categories_id ? 'selected' : '' }}>
                                {{ $doc->DocType }}
                            </option>
                            @endforeach
                        </select>
                        @error('document_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Requesting School --}}
                    <div class="mb-3 col-lg-8">
                        <label class="form-label">Requesting School</label>
                        <input type="text" name="request_schl_entity" class="form-control" value="{{ $pending->request_schl_entity }}">
                        @error('request_schl_entity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-lg-4">
                        <label class="form-label">
                            Request Mode <i class="fas fa-lock" style="color: #d1d5db; font-size: 0.7rem;"></i>
                        </label>
                        <input type="text" id="requestMode" name="request_mode" class="form-control" value="{{ $pending->request_mode }}" readonly>
                        <small id="requestHelp" class="locked-hint" style="display: none;">
                            <i class="fas fa-lock me-1"></i> This field is locked.
                        </small>
                        @error('request_mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">
                            Release Mode <i class="fas fa-lock" style="color: #d1d5db; font-size: 0.7rem;"></i>
                        </label>
                        <input type="text" id="releaseMode" name="release_mode" class="form-control" value="{{ $pending->release_mode }}" readonly>
                        <small id="releaseHelp" class="locked-hint" style="display: none;">
                            <i class="fas fa-lock me-1"></i> This field is locked.
                        </small>
                        @error('release_mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $pending->remarks }}">
                        @error('remarks') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Status Field --}}
                    <div class="mb-3 col-md-4">
                        <label class="form-label d-flex align-items-center">
                            Request Status
                            <span id="statusLockIcon" class="ms-2" style="display: none;">
                                <i class="fas fa-lock" style="color: #d1d5db; font-size: 0.75rem;"></i>
                            </span>
                        </label>
                        <input type="text" id="status" name="status" class="form-control" value="{{ $pending->status }}" readonly>
                        <small id="statusHelp" class="locked-hint" style="display: none;">
                            <i class="fas fa-lock me-1"></i> This field is locked.
                        </small>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Improved UX for locked fields --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusField = document.getElementById('status');
        const claimerField = document.getElementById('claimer_id');
        const requestMode = document.getElementById('requestMode');
        const releaseMode = document.getElementById('releaseMode');

        if (statusField && claimerField) {
            const statusValue = statusField.value.trim().toLowerCase();

            if (statusValue === 'pending') {
                // Disable visually + functionally
                [claimerField, statusField, requestMode, releaseMode].forEach(field => {
                    field.setAttribute('readonly', true);
                    field.style.backgroundColor = '#f9fafb';
                    field.style.cursor = 'not-allowed';
                    field.style.opacity = '0.75';
                    field.style.borderStyle = 'dashed';
                });

                // Show lock icons & help text
                document.getElementById('claimerLockIcon').style.display = 'inline';
                document.getElementById('statusLockIcon').style.display = 'inline';
                document.getElementById('claimerHelp').style.display = 'block';
                document.getElementById('statusHelp').style.display = 'block';
                document.getElementById('requestHelp').style.display = 'block';
                document.getElementById('releaseHelp').style.display = 'block';

                // SweetAlert — only once
                if (!window._claimerNoticeShown) {
                    window._claimerNoticeShown = true;
                    Swal.fire({
                        icon: 'info',
                        title: 'Locked Fields',
                        text: 'Claimer and Status fields are disabled while the request is pending.',
                        confirmButtonColor: '#1dd3b0'
                    });
                }
            }
        }
    });
</script>
@endsection
