@extends('layout.blankpage')

@section('content')

<div class="row mb-3">
    <div class="col-12 col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Ongoing Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class = "text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Ongoing Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Ongoing: {{ $totalCount }}</span></h1>
    </div>
</div>

<div class="row">
    <div class="col-12">

        @if (session('Status'))
            <div class="alert alert-success">{{ session('Status') }}</div>
        @endif

        @if (session('Danger'))
            <div class="alert alert-danger">{{ session('Danger') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="background-color: #1f2937;">
                <h5 class="mb-2 mb-md-0">Ongoing Document Requests</h5>
            </div>


            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Claimer</th>
                                <th>Student</th>
                                <th>Document</th>
                                <th>School</th>
                                <th>Requested Via</th>
                                <th>Release Mode</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Receipt</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($DocRequests as $item)
                                <tr>
                                    <td class="text-center">{{ $item->id }}</td>
                                    <td>{{ $item->claimer->full_name }}</td>
                                    <td>{{ $item->studentInformation->full_name }}</td>
                                    <td>{{ $item->documents->DocType }}</td>
                                    <td>{{ $item->request_schl_entity }}</td>
                                    <td>{{ $item->request_mode }}</td>
                                    <td>{{ $item->release_mode }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td><span class="badge bg-success text-white">{{ $item->status }}</span></td>
                                    <td>{{ $item->receipt_no }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            @if(!empty($approveOngoing))
                                                <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>

                                                <form action="{{ route('document-request2.complete', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                                </form>
                                            @endif

                                            @if (!empty($PermissionEdit))
                                                <a href="{{ route('ongoing.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @endif

                                            @if (!empty($deleteCompleted))
                                                <form action="{{ route('ongoing.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            @endif

                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $item->id }}">
                                                Receipt
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No ongoing requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $DocRequests->links() }}
                </div>
                @foreach ($DocRequests as $item)
                    @if ($item->receipt)
                        <div class="modal fade" id="receiptModal{{ $item->id }}" tabindex="-1" aria-labelledby="receiptModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content border-0 shadow-sm">
                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title mx-auto" id="receiptModalLabel{{ $item->id }}">
                                            Receipt #{{ $item->receipt->receipt_no }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body bg-white text-dark px-4 py-3" style="font-family: 'Courier New', Courier, monospace;">
                                        <div class="text-center mb-3">
                                            <!-- 🖼 Logo -->
                                            <img src="{{ asset('images/UBLOGO.png') }}" alt="UB Logo" class="mb-2" style="max-height: 80px;">

                                            <h5 class="fw-bold mb-1">Upper Bicutan National High School</h5>
                                            <div class="text-muted small">Official Receipt</div>
                                        </div>

                                        <hr>

                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>Document:</strong>
                                            <span>{{ $item->documents->DocType }}</span>
                                        </div>

                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>Amount Paid:</strong>
                                            <span>₱{{ number_format($item->receipt->doc_amount, 2) }}</span>
                                        </div>

                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>Student ID:</strong>
                                            <span>{{ $item->receipt->name_request }}</span>
                                        </div>

                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($item->receipt->time_request)->format('F d, Y - h:i A') }}</span>
                                        </div>

                                        <hr>

                                        <div class="text-center mt-3">
                                            <div class="text-muted small">Thank you for your request!</div>
                                        </div>
                                    </div>

                                    <div class="modal-footer bg-light border-top-0">
                                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close Receipt</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>
</div>

@endsection
