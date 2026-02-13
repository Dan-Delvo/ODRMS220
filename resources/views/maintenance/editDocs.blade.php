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

    .edit-card-body .form-control {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .edit-card-body .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .edit-card-body .input-group-text {
        border-radius: 10px 0 0 10px;
        background: var(--primary-dark);
        color: white;
        border: 1px solid var(--primary-dark);
        font-weight: 600;
    }

    .edit-card-body .input-group .form-control {
        border-radius: 0 10px 10px 0;
    }

    .btn-update {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-update:hover {
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

        .edit-card-body .form-label {
            font-size: 0.75rem;
        }

        .edit-card-body .form-control {
            font-size: 0.8rem;
        }

        .btn-update {
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
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-edit">
        <h1>Edit Document</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('doc') }}">Documents List</a></li>
            <li class="breadcrumb-item active">Edit Document</li>
        </ol>
    </div>

    <!-- Edit Card -->
    <div class="edit-card">
        <div class="edit-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-file-alt"></i></span>
                <h5>Edit Document</h5>
            </div>
            <a href="{{ route('doc') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="edit-card-body">
            <form action="{{ route('doc.update', $document->id) }}" method="POST"
                data-swal-loading="true"
                data-swal-title="Updating Document"
                data-swal-text="This may take a few seconds...">
                @csrf
                @method('PUT')

                <!-- Document Type -->
                <div class="mb-3">
                    <label for="Type" class="form-label">Document Type</label>
                    <input type="text" name="Type" id="DocType"
                        class="form-control @error('Type') is-invalid @enderror"
                        value="{{ old('Type', $document->DocType) }}">
                    @error('Type')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Document Price -->
                <div class="mb-3">
                    <label for="Price" class="form-label">Document Price</label>
                    <div class="input-group">
                        <span class="input-group-text">&#8369;</span>
                        <input type="text" name="Price" id="DocPrice"
                            class="form-control @error('Price') is-invalid @enderror"
                            value="{{ old('Price', $document->Price) }}">
                    </div>
                    <small id="priceHelp" class="text-muted small">&#10060; Only numbers allowed</small>
                    @error('Price')
                    <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Save Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-update float-end">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
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
