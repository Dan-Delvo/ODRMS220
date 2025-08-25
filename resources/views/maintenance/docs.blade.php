
@extends('layout.blankpage')

@section ('content')

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Document Type</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
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
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">
                    Document Types
                </h4>
                <a href="{{route('doc.add')}}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Add Document
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered bg-white text-dark">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Document ID</th>
                                <th>Document Name</th>
                                <th>Document Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Doc as $item)
                            <tr>
                                <td>{{ $loop->iteration + $Doc->firstItem() - 1 }}</td>
                                <td>{{ $item->DocType }}</td>
                                <td>{{ $item->DocPrice }}</td>
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
