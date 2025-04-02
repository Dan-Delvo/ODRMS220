@extends('layout.blankpage')

@section ('content')

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">Roles</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="#" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Roles List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">Roles Total: {{ $roles->count() }}</h1>
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

<!-- Roles Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header bg-black text-white">
                <h4>Roles
                    <a href="{{ route('role.add') }}" class="btn btn-light float-end text-dark">Add Role</a>
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>ID</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <a href="{{ route('role.edit', ['id' => $item->id]) }}" class="btn btn-success">Edit</a>
                                    <form action="{{ route('role.delete', ['id' => $item->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
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
