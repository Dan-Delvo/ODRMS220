@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">Add Document Type</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('doc') }}">Document Types</a></li>
            <li class="breadcrumb-item active">Add Document</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header bg-dark text-white">
                <h4>Add Document Type
                    <a href="{{ route('doc') }}" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('doc.insert') }}" method="POST">
                    @csrf
                    <!-- Document Type Input -->
                    <div class="mb-3">
                        <label for="DocType" class="form-label">Document Type</label>
                        <input type="text" name="DocType" id="DocType" class="form-control" value="{{ old('DocType') }}">
                        @error('DocType') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn btn-success float-end">Add</button>
                        <a href="{{ route('doc') }}" class="btn btn-secondary float-end me-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
