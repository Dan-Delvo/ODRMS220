@extends('layout.blankpage')

@section('content')

<h1 class="mt-4">Pending Requests</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pending.index') }}">Pending Requests</a></li>
    <li class="breadcrumb-item active">Edit Request</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Request</h4>
                <a href="{{ route('pending.index') }}" class="btn btn-danger btn-sm">Back</a>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('pending.update', $pending->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $pending->id }}">

                    <div class="mb-3">
                        <label class="form-label">Claimer</label>
                        <input type="text" name="claimer_id" class="form-control" value="{{ $pending->claimer->full_name }}" readonly>
                        @error('claimer_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requested Document</label>
                        <select class="form-control" name="document_id">
                            @foreach($DocType as $doc)
                                <option value="{{ $doc->id }}" {{ $doc->id == $pending->document_id ? 'selected' : '' }}>
                                    {{ $doc->DocType }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requesting School</label>
                        <input type="text" name="request_schl_entity" class="form-control" value="{{ $pending->request_schl_entity }}">
                        @error('request_schl_entity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Mode</label>
                        <input type="text" name="request_mode" class="form-control" value="{{ $pending->request_mode }}">
                        @error('request_mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Release Mode</label>
                        <input type="text" name="release_mode" class="form-control" value="{{ $pending->release_mode }}">
                        @error('release_mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $pending->remarks }}">
                        @error('remarks') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Status</label>
                        <input type="text" name="status" class="form-control" value="{{ $pending->status }}">
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
