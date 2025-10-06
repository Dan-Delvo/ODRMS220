@extends('layout.studentpage')

@section('content')
@include('layout.partials.studentMessage')

<div class="main-content" style="background-color: #0f172a; min-height: 100vh;">
    <div class="container-fluid py-4 text-light">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center flex-wrap">
                    <div class="me-3 mb-2">
                        <i class="fas fa-user-graduate fs-2" style="color:#1dd3b0;"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold" style="color:#f1f5f9;">Student Dashboard</h2>
                        <p class="mb-0" style="color:#e2e8f0;">Track your document requests and account information</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- Student Info Card -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="card h-100 shadow-sm border-0" style="background:#1e293b; border:1px solid #334155;">
                    <div class="card-header border-0" style="background:#1dd3b0; color:#0f172a;">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-id-card me-2"></i>
                            Student Information
                        </h5>
                    </div>
                    <div class="card-body text-center" style="color:#f1f5f9;">

                        <!-- Student Avatar -->
                        <div class="mb-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px; background:#334155;">
                                <i class="fas fa-user fs-1" style="color:#1dd3b0;"></i>
                            </div>
                            <h4 class="mt-3 mb-1 fw-bold">{{ $studInfo->FirstName }} {{ $studInfo->LastName }}</h4>
                            <span class="badge" style="background:#1dd3b0; color:#0f172a;">{{ $studInfo->Std_status }}</span>
                        </div>

                        <!-- Student Details -->
                        <div class="student-details text-start">
                            <div class="row mb-3">
                                <div class="col-5">
                                    <small class="fw-semibold" style="color:#e2e8f0;">Student ID:</small>
                                </div>
                                <div class="col-7">
                                    <span class="fw-medium">{{ $studInfo->id }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5">
                                    <small class="fw-semibold" style="color:#e2e8f0;">LRN:</small>
                                </div>
                                <div class="col-7">
                                    <span class="fw-medium">{{ $studInfo->LRN }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5">
                                    <small class="fw-semibold" style="color:#e2e8f0;">Grade Level:</small>
                                </div>
                                <div class="col-7">
                                    <span class="fw-medium">{{ $studInfo->Grade_level }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <small class="fw-semibold" style="color:#e2e8f0;">Last SY:</small>
                                </div>
                                <div class="col-7">
                                    <span class="fw-medium">{{ $studInfo->Last_sy_attended }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Requests Table -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="card shadow-sm border-0" style="background:#0f172a; border:1px solid #1e293b;">
                    <div class="card-header border-bottom" style="background:#0f172a; color:#f1f5f9;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="fas fa-file-alt me-2" style="color:#1dd3b0;"></i>
                                Document Requests
                            </h5>
                            <span class="badge mt-2 mt-md-0" style="background:#1e293b; color:#e2e8f0;">
                                {{ $DocRequests->total() ?? count($DocRequests) }} total
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($DocRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fs-1 mb-3" style="color:#475569;"></i>
                            <h5 style="color:#e2e8f0;">No document requests yet</h5>
                            <p class="mb-0" style="color:#94a3b8;">Your document requests will appear here</p>
                        </div>
                        @else

                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table align-middle mb-0" style="background:#0f172a; color:#e2e8f0; border-collapse: separate; border-spacing: 0;">
                                <thead style="background:#020617; color:#1dd3b0;">
                                    <tr>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Request ID</th>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Document Type</th>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">School</th>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Release Mode</th>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Status</th>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($DocRequests as $item)
                                    <tr style="transition: background 0.2s; background:#0f172a;"
                                        onmouseover="this.style.background='#1e293b';"
                                        onmouseout="this.style.background='#0f172a';">
                                        <td class="px-4 py-3 fw-medium" style="color:#f1f5f9;">#{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-4 py-3" style="color:#f1f5f9;">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-text me-2" style="color:#1dd3b0;"></i>
                                                {{ $item->documents->DocType }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <small style="color:#cbd5e1;">{{ Str::limit($item->request_schl_entity, 20) }}</small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge" style="background:#1e293b; color:#e2e8f0; border:1px solid #334155;">{{ $item->release_mode }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($item->status == 'Claimed')
                                            <span class="badge rounded-pill" style="background:#22c55e; color:#0f172a;">
                                                <i class="fas fa-check me-1"></i>Claimed
                                            </span>
                                            @elseif($item->status == 'Processing')
                                            <span class="badge rounded-pill" style="background:#facc15; color:#0f172a;">
                                                <i class="fas fa-clock me-1"></i>Processing
                                            </span>
                                            @elseif($item->status == 'Pending')
                                            <span class="badge rounded-pill" style="background:#475569; color:#f1f5f9;">
                                                <i class="fas fa-hourglass-half me-1"></i>Pending
                                            </span>
                                            @elseif($item->status == 'For Release')
                                            <span class="badge rounded-pill" style="background:#0ea5e9; color:#0f172a;">
                                                <i class="fas fa-paper-plane me-1"></i>For Release
                                            </span>
                                            @else
                                            <span class="badge rounded-pill" style="background:#ef4444; color:#f1f5f9;">
                                                <i class="fas fa-times me-1"></i>Declined
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-nowrap">
                                            <button class="btn btn-sm"
                                                style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#requestModal{{ $item->id }}"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-lg-none mobile-requests-container">
                            @foreach ($DocRequests as $item)
                            <div class="mobile-request-card" style="background:#1e293b; border:1px solid #334155; margin:1rem; border-radius:0.5rem; overflow:hidden;">

                                <!-- Card Header -->
                                <div class="mobile-card-header p-3 d-flex justify-content-between align-items-center" style="background:#0f172a; border-bottom:1px solid #334155;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-text me-2" style="color:#1dd3b0;"></i>
                                        <span class="fw-medium" style="color:#f1f5f9;">#{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div>
                                        @if($item->status == 'Claimed')
                                        <span class="badge rounded-pill" style="background:#22c55e; color:#0f172a; font-size:0.75rem;">
                                            <i class="fas fa-check me-1"></i>Claimed
                                        </span>
                                        @elseif($item->status == 'Processing')
                                        <span class="badge rounded-pill" style="background:#facc15; color:#0f172a; font-size:0.75rem;">
                                            <i class="fas fa-clock me-1"></i>Processing
                                        </span>
                                        @elseif($item->status == 'Pending')
                                        <span class="badge rounded-pill" style="background:#475569; color:#f1f5f9; font-size:0.75rem;">
                                            <i class="fas fa-hourglass-half me-1"></i>Pending
                                        </span>
                                        @elseif($item->status == 'For Release')
                                        <span class="badge rounded-pill" style="background:#0ea5e9; color:#0f172a; font-size:0.75rem;">
                                            <i class="fas fa-paper-plane me-1"></i>For Release
                                        </span>
                                        @else
                                        <span class="badge rounded-pill" style="background:#ef4444; color:#f1f5f9; font-size:0.75rem;">
                                            <i class="fas fa-times me-1"></i>Declined
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="mobile-card-body p-3" style="color:#f1f5f9;">
                                    <div class="row mb-2">
                                        <div class="col-4">
                                            <small class="text-muted">Document:</small>
                                        </div>
                                        <div class="col-8">
                                            <span class="fw-medium">{{ $item->documents->DocType }}</span>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-4">
                                            <small class="text-muted">School:</small>
                                        </div>
                                        <div class="col-8">
                                            <small style="color:#cbd5e1;">{{ Str::limit($item->request_schl_entity, 25) }}</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <small class="text-muted">Release:</small>
                                        </div>
                                        <div class="col-8">
                                            <span class="badge" style="background:#1e293b; color:#e2e8f0; border:1px solid #334155; font-size:0.7rem;">{{ $item->release_mode }}</span>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="text-center d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm w-100"
                                            style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent; padding:0.5rem;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#requestModal{{ $item->id }}">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Tablet View (Medium screens) -->
                        <div class="table-responsive d-none d-md-block d-lg-none tablet-view">
                            <table class="table align-middle mb-0" style="background:#0f172a; color:#e2e8f0;">
                                <thead style="background:#020617; color:#1dd3b0;">
                                    <tr>
                                        <th class="border-0 py-3 px-3 fw-semibold text-uppercase small">ID</th>
                                        <th class="border-0 py-3 px-3 fw-semibold text-uppercase small">Document</th>
                                        <th class="border-0 py-3 px-3 fw-semibold text-uppercase small">Status</th>
                                        <th class="border-0 py-3 px-3 fw-semibold text-uppercase small">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($DocRequests as $item)
                                    <tr style="transition: background 0.2s; background:#0f172a;"
                                        onmouseover="this.style.background='#1e293b';"
                                        onmouseout="this.style.background='#0f172a';">
                                        <td class="px-3 py-3 fw-medium" style="color:#f1f5f9;">#{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-3 py-3" style="color:#f1f5f9;">
                                            <div>
                                                <div class="fw-medium">{{ Str::limit($item->documents->DocType, 20) }}</div>
                                                <small style="color:#cbd5e1;">{{ Str::limit($item->request_schl_entity, 25) }}</small>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if($item->status == 'Claimed')
                                            <span class="badge rounded-pill" style="background:#22c55e; color:#0f172a; font-size:0.7rem;">
                                                <i class="fas fa-check me-1"></i>Claimed
                                            </span>
                                            @elseif($item->status == 'Processing')
                                            <span class="badge rounded-pill" style="background:#facc15; color:#0f172a; font-size:0.7rem;">
                                                <i class="fas fa-clock me-1"></i>Processing
                                            </span>
                                            @elseif($item->status == 'Pending')
                                            <span class="badge rounded-pill" style="background:#475569; color:#f1f5f9; font-size:0.7rem;">
                                                <i class="fas fa-hourglass-half me-1"></i>Pending
                                            </span>
                                            @elseif($item->status == 'For Release')
                                            <span class="badge rounded-pill" style="background:#0ea5e9; color:#0f172a; font-size:0.7rem;">
                                                <i class="fas fa-paper-plane me-1"></i>For Release
                                            </span>
                                            @else
                                            <span class="badge rounded-pill" style="background:#ef4444; color:#f1f5f9; font-size:0.7rem;">
                                                <i class="fas fa-times me-1"></i>Declined
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <button class="btn btn-sm"
                                                style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#requestModal{{ $item->id }}"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($DocRequests, 'links'))
                        <div class="card-footer" style="background:#0f172a; border-top:1px solid #1e293b;">
                            <div class="d-flex justify-content-center">
                                {{ $DocRequests->links() }}
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Request Details -->
@foreach ($DocRequests as $item)
<div class="modal fade" id="requestModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"
            style="background:#1e293b; color:#f1f5f9; border:1px solid #334155; border-radius:1rem; box-shadow:0 8px 24px rgba(0,0,0,0.4);">

            <!-- Header -->
            <div class="modal-header"
                style="background:#0f172a; border-bottom:1px solid #334155; border-top-left-radius:1rem; border-top-right-radius:1rem;">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2"
                    style="color:#1dd3b0; font-size:1.15rem; letter-spacing:0.5px;">
                    <i class="bi bi-file-earmark-text"></i>
                    Request Details • #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter: invert(1) grayscale(100%) brightness(200%); opacity:.8;"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left column -->
                    <div class="col-12 col-md-6">
                        <p class="mb-2"><i class="bi bi-file-earmark me-2" style="color:#1dd3b0;"></i>
                            <span style="color:#1dd3b0; font-weight:600;">Document Type:</span>
                            {{ $item->documents->DocType }}
                        </p>
                        <p class="mb-2"><i class="bi bi-building me-2" style="color:#1dd3b0;"></i>
                            <span style="color:#1dd3b0; font-weight:600;">School Entity:</span>
                            {{ $item->request_schl_entity }}
                        </p>
                        <p class="mb-2"><i class="bi bi-truck me-2" style="color:#1dd3b0;"></i>
                            <span style="color:#1dd3b0; font-weight:600;">Release Mode:</span>
                            {{ $item->release_mode }}
                        </p>
                    </div>

                    <!-- Right column -->
                    <div class="col-12 col-md-6">
                        @if($item->claimer && $item->claimer->full_name !== 'Blank Blank')
                        <p class="mb-2"><i class="bi bi-person-check me-2" style="color:#1dd3b0;"></i>
                            <span style="color:#1dd3b0; font-weight:600;">Claimer:</span>
                            {{ $item->claimer->full_name }}
                        </p>
                        @endif

                        <!-- Status -->
                        <p class="mb-2">
                            <i class="bi bi-info-circle me-1" style="color:#1dd3b0;"></i>
                            <span style="color:#1dd3b0; font-weight:600;">Status:</span>

                            @switch($item->status)
                            @case('Claimed')
                            <span class="badge rounded-pill" style="background:#22c55e; color:#0f172a; font-size:0.75rem;">
                                <i class="fas fa-check me-1"></i>Claimed
                            </span>
                            @break
                            @case('Processing')
                            <span class="badge rounded-pill" style="background:#facc15; color:#0f172a; font-size:0.75rem;">
                                <i class="fas fa-clock me-1"></i>Processing
                            </span>
                            @break
                            @case('Pending')
                            <span class="badge rounded-pill" style="background:#475569; color:#f1f5f9; font-size:0.75rem;">
                                <i class="fas fa-hourglass-half me-1"></i>Pending
                            </span>
                            @break
                            @case('For Release')
                            <span class="badge rounded-pill" style="background:#0ea5e9; color:#0f172a; font-size:0.75rem;">
                                <i class="fas fa-paper-plane me-1"></i>For Release
                            </span>
                            @break
                            @default
                            <span class="badge rounded-pill" style="background:#ef4444; color:#f1f5f9; font-size:0.75rem;">
                                <i class="fas fa-times me-1"></i>Declined
                            </span>
                            @endswitch
                        </p>

                        @if($item->remarks)
                        <p class="mb-0"><i class="bi bi-chat-left-dots me-2" style="color:#1dd3b0;"></i>
                            <span style="color:#1dd3b0; font-weight:600;">Remarks:</span>
                            {{ $item->remarks }}
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Show supporting document + upload option ONLY if Declined -->
                @if($item->status === 'Declined')
                @php
                $fileExtension = strtolower(pathinfo($item->supporting_document, PATHINFO_EXTENSION));
                $documentPath = $item->supporting_document;
                @endphp

                <h6 class="fw-semibold mb-3 text-center" style="color:#1dd3b0;">
                    <i class="bi bi-folder2-open me-1"></i> Old Supporting Document
                </h6>
                <div class="mb-3 mt-4">
                    @if(in_array($fileExtension, ['jpg','jpeg','png','gif','webp']))
                    <!-- IMAGE PREVIEW -->
                    <img src="{{ asset($documentPath) }}"
                        alt="Supporting Document for {{ $item->req_no }}"
                        class="img-fluid w-100 mb-2"
                        style="max-height: 70vh; object-fit: contain;"
                        onerror="this.onerror=null; this.src='{{ asset('images/no-image-placeholder.png') }}'; this.alt='Document not available';">
                    @elseif($fileExtension === 'pdf')
                    <!-- PDF PREVIEW -->
                    <div class="p-4 text-center">
                        <i class="fas fa-file-pdf text-danger" style="font-size: 4rem;"></i>
                        <h5 class="mt-2">PDF Document</h5>
                        <p class="text-muted">{{ basename($item->supporting_document) }}</p>
                        <iframe src="{{ asset($documentPath) }}" width="100%" height="400px" style="border: 1px solid #ddd;"></iframe>
                    </div>
                    @else
                    <!-- OTHER FILE TYPES -->
                    <div class="p-5 text-center">
                        @switch($fileExtension)
                        @case('doc') @case('docx')
                        <i class="fas fa-file-word text-primary" style="font-size: 4rem;"></i>
                        @break
                        @case('xls') @case('xlsx')
                        <i class="fas fa-file-excel text-success" style="font-size: 4rem;"></i>
                        @break
                        @case('ppt') @case('pptx')
                        <i class="fas fa-file-powerpoint text-warning" style="font-size: 4rem;"></i>
                        @break
                        @case('txt')
                        <i class="fas fa-file-alt text-secondary" style="font-size: 4rem;"></i>
                        @break
                        @default
                        <i class="fas fa-file text-muted" style="font-size: 4rem;"></i>
                        @endswitch

                        <h5 class="mt-3">{{ strtoupper($fileExtension) }} Document</h5>
                        <p class="text-muted">{{ basename($item->supporting_document) }}</p>

                        <!-- ✅ Download & Open Buttons -->
                        <div class="mt-3">
                            <a href="{{ asset($documentPath) }}"
                                class="btn btn-sm btn-primary me-2"
                                download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                            <a href="{{ asset($documentPath) }}"
                                class="btn btn-sm btn-outline-secondary"
                                target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
                            </a>
                        </div>
                    </div>
                    @endif
                </div>


                <!-- Upload & Request Again -->
                <form class="accountUpdateForm" action="{{ route('document-request.replaceFile', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1dd3b0;">Upload New Supporting Document:</label>
                        <input type="file" name="supporting_document" class="form-control" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
                    </div>

                    <button type="submit" class="btn px-4 py-2 d-flex align-items-center gap-2"
                        style="background:#0ea5e9; color:#0f172a; font-weight:600; border-radius:0.5rem; transition:all .2s;"
                        onmouseover="this.style.background='#0284c7';"
                        onmouseout="this.style.background='#0ea5e9';">
                        <i class="bi bi-arrow-repeat"></i> Upload & Request Again
                    </button>
                </form>
                @endif
            </div>

            <!-- Footer -->
            <div class="modal-footer justify-content-center"
                style="background:#0f172a; border-top:1px solid #334155; border-bottom-left-radius:1rem; border-bottom-right-radius:1rem;">

                <button type="button" class="btn px-4 py-2 d-flex align-items-center gap-2"
                    style="background:#1dd3b0; color:#0f172a; font-weight:600; border-radius:0.5rem; transition:all .2s;"
                    data-bs-dismiss="modal"
                    onmouseover="this.style.background='#14b89f';"
                    onmouseout="this.style.background='#1dd3b0';">
                    <i class="bi bi-x-circle"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach



<!-- Enhanced Responsive Styles -->
<style>
    /* Base table styles */
    .table-responsive {
        overflow-x: auto;
        border-radius: 0.5rem;
        background-color: #0f172a !important;
    }

    .table {
        --bs-table-bg: #0f172a !important;
        --bs-table-striped-bg: #0f172a !important;
        --bs-table-hover-bg: #1e293b !important;
        --bs-table-color: #f1f5f9 !important;
        background-color: #0f172a !important;
        color: #f1f5f9 !important;
    }

    .table> :not(caption)>*>* {
        background-color: #0f172a !important;
        border-bottom-width: 0 !important;
        color: #f1f5f9 !important;
    }

    .table thead th {
        background-color: #020617 !important;
        color: #1dd3b0 !important;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: none !important;
    }

    .table tbody tr {
        background-color: #0f172a !important;
    }

    .table tbody tr:hover {
        background-color: #1e293b !important;
    }

    .table tbody tr td {
        background-color: inherit !important;
        border: none !important;
    }

    .table td,
    .table th {
        border: none !important;
        vertical-align: middle !important;
    }

    /* Mobile card styles */
    .mobile-request-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .mobile-request-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.1);
    }

    .mobile-requests-container {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #334155 #1e293b;
    }

    .mobile-requests-container::-webkit-scrollbar {
        width: 6px;
    }

    .mobile-requests-container::-webkit-scrollbar-track {
        background: #1e293b;
    }

    .mobile-requests-container::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 3px;
    }

    /* Responsive breakpoint adjustments */
    @media (max-width: 991.98px) {
        .main-content {
            padding: 0.5rem !important;
        }

        .container-fluid {
            padding: 0.5rem !important;
        }

        .row.g-4 {
            --bs-gutter-x: 0.5rem;
            --bs-gutter-y: 0.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .card-header h5 {
            font-size: 1rem;
        }

        .mobile-request-card {
            margin: 0.5rem !important;
        }

        .mobile-card-body .row {
            margin-bottom: 0.5rem !important;
        }

        .badge {
            font-size: 0.65rem !important;
            padding: 0.25rem 0.5rem !important;
        }
    }

    @media (max-width: 575.98px) {
        .page-header h2 {
            font-size: 1.25rem;
        }

        .page-header p {
            font-size: 0.875rem;
        }

        .student-details .row {
            margin-bottom: 0.5rem !important;
        }

        .mobile-request-card {
            margin: 0.25rem !important;
            border-radius: 0.375rem !important;
        }

        .mobile-card-header,
        .mobile-card-body {
            padding: 0.75rem !important;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .modal-title {
            font-size: 0.9rem;
        }
    }

    /* Tablet specific styles */
    @media (min-width: 768px) and (max-width: 991.98px) {

        .tablet-view .table td,
        .tablet-view .table th {
            padding: 0.5rem !important;
            font-size: 0.875rem;
        }

        .tablet-view .badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
        }
    }

    /* Enhanced button styles for mobile */
    @media (max-width: 767.98px) {
        .btn {
            touch-action: manipulation;
        }

        .mobile-card-body .btn {
            min-height: 44px;
            /* Better touch target */
        }
    }

    /* Improved modal responsiveness */
    @media (max-width: 575.98px) {
        .modal-dialog-lg {
            max-width: 95%;
        }

        .modal-body .row>div {
            margin-bottom: 1rem;
        }

        .modal-body p {
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }
    }

    /* Loading states and smooth transitions */
    .mobile-request-card,
    .table tbody tr {
        transition: all 0.2s ease-in-out;
    }

    /* Focus states for accessibility */
    .btn:focus,
    .btn:focus-visible {
        box-shadow: 0 0 0 2px rgba(29, 211, 176, 0.5);
        outline: none;
    }

    /* Pagination responsiveness */
    @media (max-width: 575.98px) {
        .card-footer .pagination {
            justify-content: center;
        }

        .card-footer .pagination .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.accountUpdateForm').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your information.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: '#334155',
                    color: '#f1f5f9',
                    confirmButtonColor: '#1dd3b0',
                });
            });
        });
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggler = document.querySelector('.sidebar-toggler');

        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-shrink');
            });
        }

        // Auto-adjust on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 1024) {
                document.body.classList.remove('sidebar-shrink');
            }
        });

        // Add smooth scrolling for mobile card container
        const mobileContainer = document.querySelector('.mobile-requests-container');
        if (mobileContainer) {
            mobileContainer.style.scrollBehavior = 'smooth';
        }

        // Enhanced touch interactions for mobile cards
        const mobileCards = document.querySelectorAll('.mobile-request-card');
        mobileCards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });

            card.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>

@endsection