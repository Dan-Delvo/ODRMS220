@extends('layout.blankpage')

@section('content')
@include ('layout.partials.message')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">Add Document Types</h1>
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

                    <div class="mb-3">
                        <label for="DocPrice" class="form-label">Document Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text"
                                name="DocPrice"
                                id="DocPrice"
                                class="form-control"
                                value="0"
                                placeholder="Enter price">
                        </div>
                        <small id="priceHelp" class="text-muted">❌ Only numbers allowed</small>
                        @error('DocPrice') <small class="text-danger">{{ $message }}</small> @enderror
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const priceInput = document.getElementById('DocPrice');
    const priceHelp = document.getElementById('priceHelp');

    priceInput.addEventListener('input', function () {
        // Remove any non-digit characters
        this.value = this.value.replace(/\D/g, '');

        if (/^\d+$/.test(this.value)) {
            priceHelp.textContent = "✅ Only numbers allowed";
            priceHelp.className = "text-success small";
        } else {
            priceHelp.textContent = "❌ Only numbers allowed";
            priceHelp.className = "text-danger small";
        }
    });
});


</script>

@endsection
