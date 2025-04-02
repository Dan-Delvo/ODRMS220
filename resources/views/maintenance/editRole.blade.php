@extends('layout.blankpage')

@section ('content')

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">Edit Role</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="#" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Edit Role</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">Edit Role</h1>
    </div>
</div>

<!-- Status Alerts (if any) -->
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

<!-- Edit Role Form -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header bg-black text-white">
                <h4>Edit Role
                    <a href="{{ url('panel/role') }}" class="btn btn-light float-end text-dark">Back</a>
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('role.update', $roles->id) }}" method="POST">
                    @csrf

                    <!-- Role Name -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role Name</label>
                        <input type="text" id="role" name="role" value="{{ $roles->name }}" class="form-control" />
                        @error('role')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions Section -->
                    <div class="mb-3">
                        <label class="form-label" style="display:block; margin-bottom: 20px; font-size: 1.25rem; font-weight: bold; color: black; text-transform: uppercase; letter-spacing: 1px;">
                            Permissions
                        </label>
                        @foreach($getPermission as $value)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <strong>{{ $value['name'] }}</strong>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    @foreach($value['group'] as $group)
                                        @php
                                            $checked = '';
                                        @endphp

                                        @foreach($getRolePermission as $role)
                                            @if($role->permission_id == $group['id'])
                                                @php
                                                    $checked = 'checked';
                                                @endphp
                                            @endif
                                        @endforeach

                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" {{ $checked }} value="{{$group['id']}}" name="permission_id[]">
                                                {{ $group['name'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <hr>
                        @endforeach
                    </div>

                    <!-- Save Button -->
                    <div>
                        <button type="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
