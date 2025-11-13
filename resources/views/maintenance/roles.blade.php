
@extends('layout.blankpage')

@section ('content')

@include('layout.partials.message')
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Roles</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Roles List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Roles Total: {{ $roles->count() }}</span></h1>
    </div>
</div>

<!-- Roles Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">
                    Roles
                </h4>
                <a href="{{ route('role.add') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0;">
                    Add Role
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <a href="{{ route('role.edit', ['id' => $item->id]) }}" class="btn btn-success">Edit</a>
                                    @if(!in_array($item->id, [1, 2, 4]))
                                    <form action="{{ route('role.delete', ['id' => $item->id]) }}" method="POST" class="d-inline" 
                                        data-swal-loading="true" 
                                        data-swal-delete="true"
                                        data-swal-delete-title="Delete Role?"
                                        data-swal-delete-text="The user accounts connected to this role will also be deleted. This action cannot be undone!">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    @elseif(in_array($item->id, [1, 2, 4]))
                                    <button type="button" class="btn btn-danger" disabled>Delete</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
