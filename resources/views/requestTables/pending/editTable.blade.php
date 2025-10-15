@extends('layout.blankpage')

@section('content')
@include('layout.partials.swal-loading')

<h1 class="mt-4">
    <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Pending Requests</span>
</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pending.index') }}" class="text-dark">Pending Requests</a></li>
    <li class="breadcrumb-item active">Edit Request</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow border-0 rounded-lg">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 70px;">
                <h4 class="mb-0">
                    Edit Request
                </h4>
                <a href="{{ route('pending.index') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Back
                </a>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('pending.update', $pending->id) }}" method="POST" data-swal-loading="true">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $pending->id }}">

                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label class="form-label d-flex align-items-center">
                                Claimer
                                <span id="claimerLockIcon" class="ms-2 text-muted" style="display: none;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </label>
                            <input type="text" name="claimer_id" id="claimer_id" class="form-control"
                                value="{{ $pending->claimer->full_name }}">
                            <small id="claimerHelp" class="text-muted" style="display: none;">
                                🔒 This field is locked while the request is pending.
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
                        {{-- Other Fields --}}
                        <div class="mb-3 col-lg-8">
                            <label class="form-label">Requesting School</label>
                            <input type="text" name="request_schl_entity" class="form-control" value="{{ $pending->request_schl_entity }}">
                            @error('request_schl_entity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-lg-4">
                            <label class="form-label">Request Mode</label>
                            <input type="text" name="request_mode" class="form-control" value="{{ $pending->request_mode }}">
                            @error('request_mode') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Release Mode</label>
                            <input type="text" name="release_mode" class="form-control" value="{{ $pending->release_mode }}">
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
                                <span id="statusLockIcon" class="ms-2 text-muted" style="display: none;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </label>
                            <input type="text" id="status" name="status" class="form-control" value="{{ $pending->status }}">
                            <small id="statusHelp" class="text-muted" style="display: none;">
                                🔒 This field is locked.
                            </small>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>


                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn text-black fw-semibold"
                            style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
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

            if (statusValue === 'pending') {
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
                        text: 'Claimer and Status fields are disabled while the request is pending.',
                        confirmButtonColor: '#1dd3b0'
                    });
                }
            }
        }
    });
</script>
@endsection