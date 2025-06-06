@extends('layout.blankpage')

@section ('content')

<style>
    input::placeholder {
        color: #D3D3D3 !important;
        opacity: 1; /* For full visibility in some browsers */
    }

    /* Optional: Only target specific input */
    input[name="role"]::placeholder {
        color: #D3D3D3 !important;
    }
</style>

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Adding Roles</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Add Role</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Add Role</span></h1>
    </div>
</div>

<!-- Status Alerts -->
<div class="row mb-4">
    <div class="col-md-12">
        @if(session('Status'))
            <div class="alert alert-success">
                {{ session('Status') }}
            </div>
        @endif

        @if(session('Danger'))
            <div class="alert alert-danger">
                {{ session('Danger') }}
            </div>
        @endif
    </div>
</div>

<!-- Add Role Form -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 70px;">
                <h4 class="mb-0">Add Role</h4>
                <a href="{{ url('panel/role') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Back
                </a>
            </div>

            <div class="card-body" style="background-color: rgb(34, 43, 55);">
                <form action="{{ route('role.insert') }}" method="POST">
                    @csrf

                    <!-- Role Name -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase text-white">Role Name</label>
                        <input type="text" name="role" class="form-control shadow-sm text-light" placeholder="Enter role name" style="background: #2d3748; border: none; color: #e2e8f0;" />
                        @error('role')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase text-white fs-5 mb-3">Permissions</label>

                        @foreach($data as $value)
                        <div class="rounded p-3 mb-4" style="background: #2d3748; border: none; color: #e2e8f0;">
                            <div class="mb-2">
                                <strong class="fs-6" style="color: #1dd3b0;">{{ $value['name'] }}</strong>
                            </div>

                            <div class="row">
                                @foreach($value['group'] as $group)
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="permission_{{ $group['id'] }}" name="permission_id[]" value="{{ $group['id'] }}">
                                            <label class="form-check-label text-light" for="permission_{{ $group['id'] }}">
                                                {{ $group['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
