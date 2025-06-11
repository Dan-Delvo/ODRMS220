@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Pending Requests</span>
        </h1>
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Pending Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item "><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item "><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Pending Requests</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Pending: {{ $totalCount }}</span></h1>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Pending: {{ $totalCount }}</span></h1>
    </div>
</div>

<div class="row">
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

        <div class="card shadow-lg border-0 rounded-lg mt-3">
            <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="background-color: #1f2937;">
                <h5 class="mb-2 mb-md-0">Pending Document Requests</h5>
            <div class="card-header text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="background-color: #1f2937;">
                <h5 class="mb-2 mb-md-0">Pending Document Requests</h5>
            </div>

            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark" >
                        <thead class="table-dark" >
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
                                <td><span class="badge bg-warning text-dark">{{ $item->status }}</span></td>
                                <td>{{ $item->receipt_no}}</td>
                                <td class="text-nowrap">
                                    @if(!empty($approvePending))
                                    <form action="{{ route('pending.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mb-1">Decline</button>
                                    </form>

                                    <form action="{{ route('document-request.complete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success mb-1">Accept</button>
                                    </form>
                                    @endif

                                    @if(!empty($PermissionEdit))
                                    <a href="{{ route('pending.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                    @endif

                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $item->id }}">
                                        Receipt
                                    </button>
                                </td>
                            </tr>
                            @endforeach
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
