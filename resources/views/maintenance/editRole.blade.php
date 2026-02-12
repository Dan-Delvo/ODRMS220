@extends('layout.blankpage')

@section('content')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-role {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header-role h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-role .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-role .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-role .breadcrumb-item.active {
        color: #d1d5db;
    }

    .role-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .role-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .role-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .role-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .role-card-header .header-icon {
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

    .role-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 0.45rem 1.15rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        transform: translateY(-1px);
    }

    .role-card-body {
        background: var(--primary-dark);
        padding: 1.5rem;
    }

    .role-card-body .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #d1d5db;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
    }

    .role-card-body .form-control {
        background: #374151;
        border: 1px solid #4b5563;
        border-radius: 10px;
        color: #f3f4f6;
        padding: 0.6rem 0.85rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .role-card-body .form-control::placeholder {
        color: #9ca3af;
    }

    .role-card-body .form-control:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.2);
        background: #374151;
        color: #f3f4f6;
    }

    .perm-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: white;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .perm-group {
        background: #374151;
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid #4b5563;
        transition: all 0.2s;
    }

    .perm-group:hover {
        border-color: rgba(29, 211, 176, 0.3);
        background: #3b4a5e;
    }

    .perm-group-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1dd3b0;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #4b5563;
    }

    .perm-group .form-check {
        padding-top: 0.15rem;
        padding-bottom: 0.15rem;
    }

    .perm-group .form-check-label {
        font-size: 0.85rem;
        color: #d1d5db;
        cursor: pointer;
    }

    .perm-group .form-check-input {
        cursor: pointer;
        border-color: #6b7280;
    }

    .perm-group .form-check-input:checked {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
    }

    .perm-group .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.25);
    }

    .btn-save {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.55rem 2rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: white;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.35);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(29, 211, 176, 0.5);
        color: white;
    }

    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
    }

    .role-disabled-msg {
        color: #9ca3af;
        font-style: italic;
        font-size: 0.875rem;
        background: #374151;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        border: 1px dashed #4b5563;
    }

    @media (max-width: 767px) {
        .page-header-role {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-role h1 {
            font-size: 1.35rem;
        }

        .role-card {
            border-radius: 12px;
        }

        .role-card-header {
            padding: 0.875rem 1.25rem;
        }

        .role-card-body {
            padding: 1rem;
        }

        .perm-group {
            padding: 1rem;
        }

        .perm-group .col-md-3 {
            width: 50%;
        }
    }

    @media (max-width: 575px) {
        .page-header-role h1 {
            font-size: 1.15rem;
        }

        .role-card-header h5 {
            font-size: 0.875rem;
        }

        .perm-group .col-md-3 {
            width: 100%;
        }

        .btn-save {
            width: 100%;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-role">
        <div>
            <h1>Edit Role</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Edit Role</li>
            </ol>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('Status'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('Status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('Danger'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        {{ session('Danger') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Edit Role Form -->
    <div class="role-card">
        <div class="role-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-pen"></i></span>
            </div>
            <a href="{{ url('panel/role') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="role-card-body">
            <form action="{{ route('role.update', $roles->id) }}" method="POST"
                data-swal-loading="true"
                data-swal-title="Updating role"
                data-swal-text="This may take a few seconds...">
                @csrf

                <!-- Role Name -->
                <div class="mb-4">
                    <label for="role" class="form-label">Role Name</label>
                    <input type="text" id="role" name="role" value="{{ $roles->name }}"
                        class="form-control"
                        placeholder="Enter role name"
                        @if($roles->id == 1) disabled @endif />
                    @error('role')
                    <div class="text-danger mt-1" style="font-size: 0.825rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Permissions Section -->
                <div class="mb-4">
                    <div class="perm-section-title">
                        <i class="fas fa-lock" style="color: #1dd3b0;"></i> Permissions
                    </div>

                    @if($roles->id == 1)
                    <div class="role-disabled-msg">This role cannot be modified.</div>
                    @else
                    @foreach($getPermission as $value)
                    <div class="perm-group">
                        <div class="perm-group-name">{{ $value['name'] }}</div>

                        <div class="row">
                            @foreach($value['group'] as $group)
                            @php
                            $checked = '';
                            foreach ($getRolePermission as $role) {
                            if ($role->permission_id == $group['id']) {
                            $checked = 'checked';
                            break;
                            }
                            }
                            @endphp

                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="permission_{{ $group['id'] }}"
                                        name="permission_id[]" value="{{ $group['id'] }}" {{ $checked }}>
                                    <label class="form-check-label" for="permission_{{ $group['id'] }}">
                                        {{ $group['name'] }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                <!-- Error Message + Submit -->
                @if($roles->id != 1)
                <div id="permission-error" class="text-danger fw-semibold mb-3" style="display: none; font-size: 0.875rem;">
                    Please select at least one permission.
                </div>

                <div class="text-end">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>

                <!-- JavaScript Validation -->
                <script>
                    document.querySelector('form').addEventListener('submit', function(e) {
                        const checkboxes = document.querySelectorAll('input[name="permission_id[]"]');
                        const errorDiv = document.getElementById('permission-error');
                        let isChecked = false;

                        checkboxes.forEach((checkbox) => {
                            if (checkbox.checked) {
                                isChecked = true;
                            }
                        });

                        if (!isChecked) {
                            e.preventDefault();
                            errorDiv.style.display = 'block';
                        } else {
                            errorDiv.style.display = 'none';
                        }
                    });
                </script>
                @endif

            </form>
        </div>
    </div>
</div>

@endsection
