@extends('layout.blankpage')

@section ('content')

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">Document Type</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="#" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Document Types</li>
        </ol>
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

<!-- Document Types Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header bg-black text-white">
                <h4>Document Types
                    <a href="{{route('doc.add')}}" class="btn btn-light float-end text-dark">Add Document</a>
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Document ID</th>
                                <th>Document Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Doc as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->DocType }}</td>
                                <td class="d-flex justify-content-start">
                                    <a href="{{ route('doc.edit', ['id' => $item->id]) }}" class="btn btn-success me-2">Edit</a>

                                    <form action="{{ route('doc.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $Doc->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
