@extends('layout.blankpage')

@section ('content')

<div class="row">
<div class="col-md-6">
<h1 class="mt-4">Completed Requests</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
    <li class="breadcrumb-item active">Complteted Requests</li>
</ol>
</div>
<div class="col-md-6">
    <h1 class="mt-4">Completed Requests Total: {{ $totalCount }}</h1>
</div>
</div>

<div class="row">
    <div class="col-md-12">

    @session('Status')
    <div class="alert alert-success">
        {{ session('Status') }}
    </div>
    @endsession

    @session('Danger')
    <div class="alert alert-danger">
        {{ session('Danger') }}
    </div>
    @endsession

        <div class="card">
            <div class="card-header">

                <h4>Completed Requests
                </h4>

            </div>

            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>claimer</th>
                            <th>student</th>
                            <th>document</th>
                            <th>School</th>
                            <th>Requested Via</th>
                            <th>Release Mode</th>
                            <th>remarks</th>
                            <th>status</th>
                            <th>Action</th>
                        </tr>
                        <tbody>
                            @foreach ($DocRequests as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->claimer->full_name }}</td>
                                <td>{{ $item->studentInformation->full_name }}</td>
                                <td>{{ $item->documents->DocType }}</td>
                                <td>{{ $item->request_schl_entity }}</td>
                                <td>{{ $item->request_mode }}</td>
                                <td>{{ $item->release_mode }}</td>
                                <td>{{ $item->remarks }}</td>
                                <td>{{ $item->status }}</td>
                                <td>
                                    <div class="d-inline">
                                        @if(!empty($PermissionEdit))
                                            <a href="{{ route('tables.edit', $item->id) }}" class="btn btn-success me-2">Edit</a>
                                        @endif
                                        @if(!empty($deleteCompleted))
                                        <form action="{{ route('tables.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger me-2">Delete</button>
                                        </form>
                                        @endif
                                        <a href="{{ route('tables.show', $item->id) }}" class="btn btn-info">Info</a>
                                    </div>
                                </td>
                            </tr>


                            @endforeach
                        </tbody>
                    </thead>
                </table>
                <div>
                    {{ $DocRequests->links() }}
                </div>
            </div>

        </div>
    </div>
</div>



@endsection
