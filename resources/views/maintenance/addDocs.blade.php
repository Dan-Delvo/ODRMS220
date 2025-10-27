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
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">Add Document Type</h4>
                <a href="{{ route('doc') }}"
                    class="btn text-black fw-semibold"
                    style="background-color: #1dd3b0;">
                    Back
                </a>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('doc.insert') }}" method="POST"
                    data-swal-loading="true"
                    data-swal-title="Adding Document"
                    data-swal-text="This may take a few seconds...">
                    @csrf

                    <!-- Document Type Input -->
                    <div class="mb-3">
                        <label for="Type" class="form-label">Document Type</label>
                        <input type="text" name="Type" id="DocType"
                            class="form-control @error('Type') is-invalid @enderror"
                            value="{{ $errors->has('Type') ? '' : old('Type') }}">
                        @error('Type')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Document Price Input -->
                    <div class="mb-3">
                        <label for="Price" class="form-label">Document Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" name="Price" id="DocPrice"
                                class="form-control @error('Price') is-invalid @enderror"
                                value="{{ $errors->has('Price') ? '0' : old('Price')}}">
                        </div>
                        <small id="priceHelp" class="text-muted small">❌ Only numbers allowed</small>
                        @error('Price')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn fw-semibold text-white float-end"
                            style="background-color: #1dd3b0;">
                            Add
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceInput = document.getElementById('DocPrice');
        const priceHelp = document.getElementById('priceHelp');

        priceInput.addEventListener('input', function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');

            if (/^\d+$/.test(this.value) && this.value !== '') {
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