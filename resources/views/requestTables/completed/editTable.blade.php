@extends('layout.blankpage')

@section('content')

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
        color: var(--primary-green);
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
    .edit-card-body {
        padding: 1.5rem;
    }
    .edit-card-body .form-label,
    .edit-card-body label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    .edit-card-body .form-control,
    .edit-card-body .form-select {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .edit-card-body .form-control:focus,
    .edit-card-body .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }
    .section-divider {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--primary-dark);
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-green);
        margin-bottom: 1rem;
        margin-top: 0.5rem;
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
    .lock-help {
        font-size: 0.78rem;
        color: #9ca3af;
    }

    @media (max-width: 767px) {
        .page-header-edit { padding: 1.25rem; border-radius: 12px; }
        .page-header-edit h1 { font-size: 1.35rem; }
        .edit-card { border-radius: 12px; }
        .edit-card-header { padding: 0.875rem 1rem; }
        .edit-card-body { padding: 1rem; }
    }
    @media (max-width: 575px) {
        .page-header-edit h1 { font-size: 1.15rem; }
        .edit-card-header h5 { font-size: 0.875rem; }
        .btn-back { font-size: 0.8rem; padding: 0.4rem 1rem; }
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="page-header-edit">
        <h1><i class="fas fa-clipboard-check me-2"></i>For Release Requests</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tables.index') }}">For Release Requests</a></li>
            <li class="breadcrumb-item active">Edit Request</li>
        </ol>
    </div>

    <div class="edit-card">
        <div class="edit-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-edit"></i></span>
                <h5>Edit Request</h5>
            </div>
            <a href="{{ route('tables.index') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="edit-card-body">
                <form action="{{ route('tables.update', $table->id) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <input type="hidden" name="id" value="{{ $table->id }}">

                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label class="form-label d-flex align-items-center">
                                Claimer
                                <span id="claimerLockIcon" class="ms-2 text-muted" style="display: none;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </label>
                            <input type="text" name="claimer_id" id="claimer_id" class="form-control"
                                value="{{$table->claimer->full_name}}" />
                            <small id="claimerHelp" class="text-muted lock-help" style="display: none;">
                                <i class="fas fa-lock me-1"></i> This field is locked while the request is for release.
                            </small>
                            @error('claimer_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-lg-6">
                            <label for="inputDocumentId">Requested Document</label>
                            <select class="form-control" id="inputDocumentId" name="document_id">
                                @foreach($DocType as $doc)
                                <option value="{{ $doc->id }}" {{ $doc->id == $table->doc_categories_id ? 'selected' : '' }}>
                                    {{ $doc->DocType }}
                                </option>
                                @endforeach
                            </select>
                            @error('document_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row">

                        <div class="mb-3 col-lg-8">
                            <label>Requesting School</label>
                            <input type="text" name="request_schl_entity" class="form-control" value="{{$table->request_schl_entity}}" readonly />
                            @error('request_schl_entity') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-lg-4">
                            <label>Request Mode</label>
                            <input type="text" name="requested_sf10" class="form-control" value="{{$table->request_mode}}" readonly />
                            @error('requested_sf10') {{$message}} @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label>Release Mode</label>
                            <input type="text" name="release_mode" class="form-control" value="{{$table->release_mode}}" readonly />
                            @error('release_mode') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{$table->remarks}}" readonly />
                            @error('remarks') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label d-flex align-items-center">
                                Request Status
                                <span id="statusLockIcon" class="ms-2 text-muted" style="display: none;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </label>
                            <input type="text" id="status" name="status" class="form-control" value="{{$table->status}}" readonly />
                            <small id="statusHelp" class="text-muted lock-help" style="display: none;">
                                <i class="fas fa-lock me-1"></i> This field is locked.
                            </small>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <h5 class="section-divider"><i class="fas fa-calendar-alt me-2"></i>Edit Date</h5>

                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label>Approved Date</label>
                                <input type="date" name="app_date" class="form-control" />
                                @error('app_date') {{$message}} @enderror
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label>For Release Date</label>
                                <input type="date" name="rel_date" class="form-control" />
                                @error('rel_date') {{$message}} @enderror
                            </div>
                        </div>

                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>

                </form>
        </div>
    </div>
</div>

{{-- 🧩 Improved UX for locked fields --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusField = document.getElementById('status');
        const claimerField = document.getElementById('claimer_id');

        if (statusField && claimerField) {
            const statusValue = statusField.value.trim().toLowerCase();

            if (statusValue === 'for release') {
                // Disable visually + functionally
                [claimerField, statusField].forEach(field => {
                    field.setAttribute('readonly', true);
                    field.style.backgroundColor = '#f3f4f6';
                    field.style.cursor = 'not-allowed';
                    field.style.opacity = '0.8';
                });

                // Show lock icons & help text
                document.getElementById('claimerLockIcon').style.display = 'inline';
                document.getElementById('statusLockIcon').style.display = 'inline';
                document.getElementById('claimerHelp').style.display = 'block';
                document.getElementById('statusHelp').style.display = 'block';

                // SweetAlert — only once
                if (!window._claimerNoticeShown) {
                    window._claimerNoticeShown = true;
                    Swal.fire({
                        icon: 'info',
                        title: 'Locked Fields',
                        text: 'Claimer and Status fields are disabled while the request is still in for release.',
                        confirmButtonColor: '#1dd3b0'
                    });
                }
            }
        }
    });
</script>
@endsection