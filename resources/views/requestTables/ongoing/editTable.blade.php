@extends('layout.blankpage')

@section('content')

<div class="row mb-3">
    <div class="col-md-12">
        <h1 class="mt-4">Ongoing Requests</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Edit Ongoing Request</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Ongoing Request</h4>
                <a href="{{ url('ongoing') }}" class="btn btn-danger btn-sm">Back</a>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('ongoing.update', $ongoing->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $ongoing->id }}">

                    <div class="mb-3">
                        <label class="form-label">Claimer</label>
                        <input type="text" name="claimer_id" class="form-control" value="{{ $ongoing->claimer->full_name }}" readonly>
                        @error('claimer_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="inputDocumentId" class="form-label">Requested Document</label>
                        <select class="form-control" id="inputDocumentId" name="document_id">
                            @foreach($DocType as $doc)
                                <option value="{{ $doc->id }}" {{ $ongoing->document_id == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->DocType }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requesting School</label>
                        <input type="text" name="request_schl_entity" class="form-control" value="{{ $ongoing->request_schl_entity }}">
                        @error('request_schl_entity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Mode</label>
                        <input type="text" name="requested_sf10" class="form-control" value="{{ $ongoing->request_mode }}">
                        @error('requested_sf10')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Release Mode</label>
                        <input type="text" name="release_mode" class="form-control" value="{{ $ongoing->release_mode }}">
                        @error('release_mode')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $ongoing->remarks }}">
                        @error('remarks')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Status</label>
                        <input type="text" name="status" class="form-control" value="{{ $ongoing->status }}">
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
