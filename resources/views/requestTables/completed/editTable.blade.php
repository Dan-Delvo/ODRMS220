@extends('layout.blankpage')

@section('content')
<h1 class="mt-4">For Release Requests</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tables.index') }}" class="text-dark">For Release Requests</a></li>
    <li class="breadcrumb-item active">For Release Requests</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow border-0 rounded-lg">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 70px;">
                <h4 class="mb-0">
                    Edit Request
                </h4>
                <a href="{{ route('tables.index') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0;">
                    Back
                </a>
            </div>

            <div class="card-body">
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
                            <small id="claimerHelp" class="text-muted" style="display: none;">
                                🔒 This field is locked while the request is pending.
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
                            <small id="statusHelp" class="text-muted" style="display: none;">
                                🔒 This field is locked.
                            </small>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <h5>Edit Date</h5>

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
                        <button type="submit" class="btn text-white fw-semibold" style="background-color: #1dd3b0;">Save Changes</button>
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