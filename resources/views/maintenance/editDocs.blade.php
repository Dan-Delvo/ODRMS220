@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">Edit Document</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('doc') }}">Documents List</a></li>
            <li class="breadcrumb-item active">Edit Document</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit Document
                    <a href="{{ route('doc') }}" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('doc.update', $document->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Document Type -->
                    <div class="mb-3">
                        <label for="DocType" class="form-label">Document Type</label>
                        <input type="text" name="DocType" id="DocType" class="form-control" value="{{ old('DocType', $document->DocType) }}">
                        @error('DocType') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>


                    <!-- Document Price -->
                    <div class="mb-3">
                        <label for="DocPrice" class="form-label">Document Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text"
                                name="DocPrice"
                                id="DocPrice"
                                class="form-control"
                                value="{{ old('DocPrice', $document->DocPrice) }}"
                                placeholder="Enter price">
                        </div>
                        <small id="priceHelp" class="text-muted">❌ Only numbers allowed</small>
                        @error('DocPrice') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Save Button -->
                    <div>
                        <button type="submit" class="btn btn-primary float-end">Update</button>
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
