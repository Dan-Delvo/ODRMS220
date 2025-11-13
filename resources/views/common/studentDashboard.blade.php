@extends('layout.studentpage')

@section('content')
@include('layout.partials.studentMessage')

<div class="main-content mt-5" style="background-color: #0f172a; min-height: 100vh;">
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
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Processing Time</th>
                                        <th class="border-0 py-3 px-4 fw-semibold text-uppercase small">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($DocRequests as $item)
                                    @php
                                        // Combine request_date and request_time
                                        $requestDateTime = $item->request_date . ' ' . $item->request_time;
                                        $createdDate = \Carbon\Carbon::parse($requestDateTime);
                                        $now = \Carbon\Carbon::now();
                                        $daysProcessing = (int)$createdDate->diffInDays($now);
                                        $estimatedDays = 5;
                                        $daysRemaining = max(0, $estimatedDays - $daysProcessing);
                                    @endphp
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

                                        <td class="px-4 py-3">
                                            @if($item->status == 'Claimed')
                                                <span class="badge" style="background:#22c55e; color:#0f172a;">
                                                    <i class="fas fa-check-circle me-1"></i>Completed
                                                </span>
                                            @elseif($item->status == 'Declined')
                                                <span class="badge" style="background:#ef4444; color:#f1f5f9;">
                                                    <i class="fas fa-ban me-1"></i>N/A
                                                </span>
                                            @else
                                                <div class="d-flex flex-column align-items-start">
                                                    <span class="badge mb-1" style="background:#334155; color:#94a3b8;">
                                                        <i class="fas fa-calendar-day me-1"></i>{{ $daysProcessing }} {{ $daysProcessing == 1 ? 'day' : 'days' }}
                                                    </span>
                                                    @if($daysRemaining > 0)
                                                        <small style="color:#64748b; font-size:0.7rem;">
                                                            ~{{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }} left
                                                        </small>
                                                    @else
                                                        <small style="color:#facc15; font-size:0.7rem;">
                                                            <i class="fas fa-exclamation-triangle"></i> Overdue
                                                        </small>
                                                    @endif
                                                </div>
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
                            @php
                                $requestDateTime = $item->request_date . ' ' . $item->request_time;
                                $createdDate = \Carbon\Carbon::parse($requestDateTime);
                                $now = \Carbon\Carbon::now();
                                $daysProcessing = (int)$createdDate->diffInDays($now);
                                $estimatedDays = 5;
                                $daysRemaining = max(0, $estimatedDays - $daysProcessing);
                            @endphp
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
                                        <div class="col-5">
                                            <small class="text-white">Document:</small>
                                        </div>
                                        <div class="col-7">
                                            <span class="fw-medium">{{ $item->documents->DocType }}</span>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-5">
                                            <small class="text-white">School:</small>
                                        </div>
                                        <div class="col-7">
                                            <small style="color:#cbd5e1;">{{ Str::limit($item->request_schl_entity, 25) }}</small>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-5">
                                            <small class="text-white">Release:</small>
                                        </div>
                                        <div class="col-7">
                                            <span class="badge" style="background:#1e293b; color:#e2e8f0; border:1px solid #334155; font-size:0.7rem;">{{ $item->release_mode }}</span>
                                        </div>
                                    </div>

                                    @if(!in_array($item->status, ['Claimed', 'Declined']))
                                    <div class="row mb-3">
                                        <div class="col-5">
                                            <small class="text-white">Processing:</small>
                                        </div>
                                        <div class="col-7">
                                            <span class="badge" style="background:#334155; color:#94a3b8; font-size:0.7rem;">
                                                <i class="fas fa-calendar-day me-1"></i>{{ $daysProcessing }} {{ $daysProcessing == 1 ? 'day' : 'days' }}
                                            </span>
                                            @if($daysRemaining > 0)
                                                <br><small style="color:#64748b; font-size:0.65rem;">~{{ $daysRemaining }} days left</small>
                                            @else
                                                <br><small style="color:#facc15; font-size:0.65rem;"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Action Button -->
                                    <div class="text-center d-flex justify-content-center gap-2 mt-3">
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
                                        <th class="border-0 py-3 px-3 fw-semibold text-uppercase small">Days</th>
                                        <th class="border-0 py-3 px-3 fw-semibold text-uppercase small">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($DocRequests as $item)
                                    @php
                                        $requestDateTime = $item->request_date . ' ' . $item->request_time;
                                        $createdDate = \Carbon\Carbon::parse($requestDateTime);
                                        $now = \Carbon\Carbon::now();
                                        $daysProcessing = (int)$createdDate->diffInDays($now);
                                        $estimatedDays = 5;
                                        $daysRemaining = max(0, $estimatedDays - $daysProcessing);
                                    @endphp
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
                                            <span class="badge rounded-pill" style="background:#475569; color:#f5f5f9; font-size:0.7rem;">
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
                                            @if(!in_array($item->status, ['Claimed', 'Declined']))
                                                <span class="badge" style="background:#334155; color:#94a3b8; font-size:0.65rem;">
                                                    {{ $daysProcessing }}d
                                                </span>
                                            @else
                                                <span style="color:#64748b; font-size:0.7rem;">-</span>
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
@php
    // Combine request_date and request_time for modal
    $requestDateTime = $item->request_date . ' ' . $item->request_time;
    $createdDate = \Carbon\Carbon::parse($requestDateTime);
    $now = \Carbon\Carbon::now();
    $daysProcessing = (int)$createdDate->diffInDays($now);
    $estimatedDays = 5;
    $daysRemaining = max(0, $estimatedDays - $daysProcessing);
@endphp
<div class="modal fade" id="requestModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog {{ $item->status === 'Declined' ? 'modal-xl' : 'modal-lg' }} modal-dialog-centered modal-dialog-scrollable">
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
                @if($item->status === 'Declined')
                <!-- DECLINED STATUS: Two Column Layout with Document Preview -->
                <div class="row g-4">
                    <!-- LEFT SIDE: All Information & Upload Form -->
                    <div class="col-lg-6">
                        <!-- Request Information -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 pb-2 border-bottom" style="color:#1dd3b0; border-color:#334155 !important;">
                                <i class="bi bi-info-circle me-2"></i>Request Information
                            </h6>

                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-file-earmark me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div>
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">Document Type</small>
                                        <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->documents->DocType }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-building me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div>
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">School Entity</small>
                                        <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->request_schl_entity }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-truck me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div>
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">Release Mode</small>
                                        <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->release_mode }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($item->claimer && $item->claimer->full_name !== 'Blank Blank')
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-person-check me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div>
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">Claimer</small>
                                        <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->claimer->full_name }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-clock-history me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div>
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">Status</small>
                                        <span class="badge rounded-pill" style="background:#ef4444; color:#f5f5f9; font-size:0.85rem; padding:0.4rem 0.8rem;">
                                            <i class="fas fa-times me-1"></i>Declined
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($item->remarks)
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-chat-left-dots me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">Admin Remarks</small>
                                        @if(strlen($item->remarks) > 80)
                                        <p class="mb-1" style="color:#cbd5e1; font-size:0.9rem;">{{ Str::limit($item->remarks, 80) }}</p>
                                        <button class="btn btn-sm mt-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#remarksModal{{ $item->id }}"
                                            style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent; padding:0.25rem 0.5rem; font-size:0.7rem;">
                                            <i class="bi bi-eye"></i> View Full
                                        </button>
                                        @else
                                        <p class="mb-0" style="color:#cbd5e1; font-size:0.9rem;">{{ $item->remarks }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($item->reason)
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-journal-text me-2 mt-1" style="color:#1dd3b0;"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-uppercase d-block" style="color:#94a3b8; font-size:0.7rem; letter-spacing:0.5px;">Request Reason</small>
                                        @if(strlen($item->reason) > 80)
                                        <p class="mb-1" style="color:#cbd5e1; font-size:0.9rem;">{{ Str::limit($item->reason, 80) }}</p>
                                        <button class="btn btn-sm mt-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reasonModal{{ $item->id }}"
                                            style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent; padding:0.25rem 0.5rem; font-size:0.7rem;">
                                            <i class="bi bi-eye"></i> View Full
                                        </button>
                                        @else
                                        <p class="mb-0" style="color:#cbd5e1; font-size:0.9rem;">{{ $item->reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Upload Form -->
                        <div class="mt-4">
                            <h6 class="fw-semibold mb-3 pb-2 border-bottom" style="color:#1dd3b0; border-color:#334155 !important;">
                                <i class="bi bi-cloud-upload me-2"></i>Re-submit Document
                            </h6>

                            <form class="accountUpdateForm" action="{{ route('document-request.replaceFile', $item->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small" style="color:#1dd3b0;">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i>New Supporting Document
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="supporting_document" class="form-control form-control-sm" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required
                                        style="background:#0f172a; border:1px solid #334155; color:#f1f5f9;">
                                    <small class="text-white d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Max 10MB • Images, PDF, Word, Excel, PowerPoint, Text
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small" style="color:#1dd3b0;">
                                        <i class="bi bi-pencil-square me-1"></i>Reason for Re-requesting
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="reason" class="form-control form-control-sm" rows="3" required
                                        placeholder="Explain why you're re-requesting..."
                                        style="background:#0f172a; border:1px solid #334155; color:#f5f5f9; resize: vertical;"></textarea>
                                    <small class="text-white d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Provide a clear explanation
                                    </small>
                                </div>

                                <div class="alert py-2 mb-3" style="background:#334155; border:1px solid #facc15; color:#e2e8f0; font-size:0.85rem;">
                                    <i class="fas fa-exclamation-triangle me-2" style="color:#facc15;"></i>
                                    <strong>Note:</strong> Status will reset to "Pending" for admin review.
                                </div>

                                <button type="submit" class="btn w-100 py-2 d-flex align-items-center justify-content-center gap-2"
                                    style="background:#0ea5e9; color:#0f172a; font-weight:600; border-radius:0.5rem; transition:all .2s;"
                                    onmouseover="this.style.background='#0284c7';"
                                    onmouseout="this.style.background='#0ea5e9';">
                                    <i class="bi bi-arrow-repeat"></i> Upload & Request Again
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: Supporting Document Preview -->
                    @if($item->supporting_document)
                    <div class="col-lg-6">
                        <div class="sticky-top" style="top: 20px;">
                            <h6 class="fw-semibold mb-3 pb-2 border-bottom text-center" style="color:#1dd3b0; border-color:#334155 !important;">
                                <i class="bi bi-folder2-open me-2"></i>Previous Document
                            </h6>

                            <div class="p-3 rounded" style="background:#0f172a; border:1px solid #334155; min-height: 300px;">
                                @php
                                $fileExtension = strtolower(pathinfo($item->supporting_document, PATHINFO_EXTENSION));
                                $documentPath = $item->supporting_document;
                                @endphp

                                @if(in_array($fileExtension, ['jpg','jpeg','png','gif','webp']))
                                <!-- IMAGE PREVIEW -->
                                <img src="/public/{{trim($documentPath)}}"
                                    alt="Supporting Document"
                                    class="img-fluid w-100 rounded"
                                    style="max-height: 600px; object-fit: contain;"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image-placeholder.png') }}';">
                                
                                @elseif($fileExtension === 'pdf')
                                <!-- PDF PREVIEW -->
                                <div class="text-center py-3">
                                    <i class="fas fa-file-pdf mb-3" style="font-size: 3rem; color:#dc2626;"></i>
                                    <h6 class="mb-2" style="color:#f1f5f9;">PDF Document</h6>
                                    <p class="text-muted small mb-3">{{ basename($item->supporting_document) }}</p>
                                    <iframe src="/public/{{trim($documentPath)}}" width="100%" height="500px" class="rounded" style="border: 1px solid #334155;"></iframe>
                                </div>
                                
                                @else
                                <!-- OTHER FILE TYPES -->
                                <div class="text-center py-5">
                                    @switch($fileExtension)
                                    @case('doc') @case('docx')
                                    <i class="fas fa-file-word mb-3" style="font-size: 4rem; color:#2b579a;"></i>
                                    @break
                                    @case('xls') @case('xlsx')
                                    <i class="fas fa-file-excel mb-3" style="font-size: 4rem; color:#217346;"></i>
                                    @break
                                    @case('ppt') @case('pptx')
                                    <i class="fas fa-file-powerpoint mb-3" style="font-size: 4rem; color:#d24726;"></i>
                                    @break
                                    @case('txt')
                                    <i class="fas fa-file-alt mb-3" style="font-size: 4rem; color:#64748b;"></i>
                                    @break
                                    @default
                                    <i class="fas fa-file mb-3" style="font-size: 4rem; color:#94a3b8;"></i>
                                    @endswitch

                                    <h6 class="mb-2" style="color:#f1f5f9;">{{ strtoupper($fileExtension) }} Document</h6>
                                    <p class="text-muted small mb-4">{{ basename($item->supporting_document) }}</p>

                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="/public/{{trim($documentPath)}}"
                                            class="btn btn-sm"
                                            style="background:#1dd3b0; color:#0f172a;"
                                            download>
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                        <a href="/public/{{trim($documentPath)}}"
                                            class="btn btn-sm"
                                            style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent;"
                                            target="_blank">
                                            <i class="fas fa-external-link-alt me-1"></i> Open
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                @else
                <!-- OTHER STATUSES: Single Column Card Layout -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom" style="color:#1dd3b0; border-color:#334155 !important;">
                            <i class="bi bi-info-circle me-2"></i>Request Information
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155; height:100%;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-file-earmark me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Document Type</small>
                            </div>
                            <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->documents->DocType }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155; height:100%;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-building me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">School Entity</small>
                            </div>
                            <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->request_schl_entity }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155; height:100%;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-truck me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Release Mode</small>
                            </div>
                            <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->release_mode }}</p>
                        </div>
                    </div>

                    @if($item->claimer && $item->claimer->full_name !== 'Blank Blank')
                    <div class="col-md-6">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155; height:100%;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-check me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Claimer</small>
                            </div>
                            <p class="mb-0 fw-semibold" style="color:#f1f5f9;">{{ $item->claimer->full_name }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155; height:100%;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-clock-history me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Status</small>
                            </div>
                            @switch($item->status)
                            @case('Claimed')
                            <span class="badge rounded-pill" style="background:#22c55e; color:#0f172a; font-size:0.85rem; padding:0.4rem 0.8rem;">
                                <i class="fas fa-check me-1"></i>Claimed
                            </span>
                            @break
                            @case('Processing')
                            <span class="badge rounded-pill" style="background:#facc15; color:#0f172a; font-size:0.85rem; padding:0.4rem 0.8rem;">
                                <i class="fas fa-clock me-1"></i>Processing
                            </span>
                            @break
                            @case('Pending')
                            <span class="badge rounded-pill" style="background:#475569; color:#f5f5f9; font-size:0.85rem; padding:0.4rem 0.8rem;">
                                <i class="fas fa-hourglass-half me-1"></i>Pending
                            </span>
                            @break
                            @case('For Release')
                            <span class="badge rounded-pill" style="background:#0ea5e9; color:#0f172a; font-size:0.85rem; padding:0.4rem 0.8rem;">
                                <i class="fas fa-paper-plane me-1"></i>For Release
                            </span>
                            @break
                            @endswitch
                        </div>
                    </div>

                    <!-- Processing Time Card -->
                    @if(!in_array($item->status, ['Claimed', 'Declined']))
                    <div class="col-md-6">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155; height:100%;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-week me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Processing Time</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge" style="background:#334155; color:#f1f5f9; font-size:0.85rem; padding:0.4rem 0.8rem;">
                                    <i class="fas fa-calendar-day me-1"></i>{{ $daysProcessing }} {{ $daysProcessing == 1 ? 'day' : 'days' }}
                                </span>
                                @if($daysRemaining > 0)
                                    <small style="color:#64748b;">
                                        ~{{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }} remaining
                                    </small>
                                @else
                                    <small style="color:#facc15;">
                                        <i class="fas fa-exclamation-triangle"></i> Processing overdue
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($item->remarks)
                    <div class="col-12">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-chat-left-dots me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                    <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Admin Remarks</small>
                                </div>
                                @if(strlen($item->remarks) > 100)
                                <button class="btn btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#remarksModal{{ $item->id }}"
                                    style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent; padding:0.25rem 0.5rem; font-size:0.75rem;">
                                    <i class="bi bi-eye"></i> View Full
                                </button>
                                @endif
                            </div>
                            <p class="mb-0" style="color:#cbd5e1; font-size:0.9rem;">
                                {{ strlen($item->remarks) > 100 ? Str::limit($item->remarks, 100) : $item->remarks }}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($item->reason)
                    <div class="col-12">
                        <div class="info-card p-3 rounded" style="background:#0f172a; border:1px solid #334155;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-journal-text me-2" style="color:#1dd3b0; font-size:1.2rem;"></i>
                                    <small class="text-uppercase" style="color:#94a3b8; font-size:0.75rem; letter-spacing:0.5px;">Request Reason</small>
                                </div>
                                @if(strlen($item->reason) > 100)
                                <button class="btn btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#reasonModal{{ $item->id }}"
                                    style="border:1px solid #1dd3b0; color:#1dd3b0; background:transparent; padding:0.25rem 0.5rem; font-size:0.75rem;">
                                    <i class="bi bi-eye"></i> View Full
                                </button>
                                @endif
                            </div>
                            <p class="mb-0" style="color:#cbd5e1; font-size:0.9rem;">
                                {{ strlen($item->reason) > 100 ? Str::limit($item->reason, 100) : $item->reason }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
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

<!-- Remarks Modal -->
@if($item->remarks && strlen($item->remarks) > 50)
<div class="modal fade" id="remarksModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"
            style="background:#1e293b; color:#f1f5f9; border:1px solid #334155; border-radius:1rem;">
            
            <div class="modal-header" style="background:#0f172a; border-bottom:1px solid #334155;">
                <h5 class="modal-title" style="color:#1dd3b0;">
                    <i class="bi bi-chat-left-dots me-2"></i>Full Remarks
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter: invert(1) grayscale(100%) brightness(200%); opacity:.8;"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="alert alert-info mb-3" style="background:#334155; border:1px solid #475569; color:#e2e8f0;">
                    <strong>Request #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</strong>
                </div>
                <div class="p-3 rounded" style="background:#0f172a; border:1px solid #334155; word-wrap: break-word; white-space: pre-line;">{{ $item->remarks }}</div>
            </div>
            
            <div class="modal-footer" style="background:#0f172a; border-top:1px solid #334155;">
                <button type="button" class="btn btn-sm" 
                    style="background:#1dd3b0; color:#0f172a;" 
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Reason Modal -->
@if($item->reason && strlen($item->reason) > 50)
<div class="modal fade" id="reasonModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"
            style="background:#1e293b; color:#f1f5f9; border:1px solid #334155; border-radius:1rem;">
            
            <div class="modal-header" style="background:#0f172a; border-bottom:1px solid #334155;">
                <h5 class="modal-title" style="color:#1dd3b0;">
                    <i class="bi bi-journal-text me-2"></i>Request Reason
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter: invert(1) grayscale(100%) brightness(200%); opacity:.8;"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="alert alert-info mb-3" style="background:#334155; border:1px solid #475569; color:#e2e8f0;">
                    <strong>Request #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</strong>
                </div>
                <div class="p-3 rounded" style="background:#0f172a; border:1px solid #334155; word-wrap: break-word; white-space: pre-line;">{{ $item->reason }}</div>
            </div>
            
            <div class="modal-footer" style="background:#0f172a; border-top:1px solid #334155;">
                <button type="button" class="btn btn-sm" 
                    style="background:#1dd3b0; color:#0f172a;" 
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif
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

    /* Remarks modal styling */
    .modal .alert-info strong {
        color: #1dd3b0;
    }

    /* Button hover effects for remarks */
    .btn-outline-info:hover {
        background: #1dd3b0 !important;
        color: #0f172a !important;
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

        // Handle nested modals (remarks/reason modal from details modal)
        document.querySelectorAll('[data-bs-target^="#remarksModal"], [data-bs-target^="#reasonModal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Get the parent modal ID
                const parentModal = this.closest('.modal');
                if (parentModal) {
                    // Hide parent modal temporarily
                    const bsModal = bootstrap.Modal.getInstance(parentModal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
            });
        });

        // When remarks/reason modal closes, show parent modal again
        document.querySelectorAll('[id^="remarksModal"], [id^="reasonModal"]').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const modalId = this.id.replace('remarksModal', 'requestModal').replace('reasonModal', 'requestModal');
                const parentModal = document.getElementById(modalId);
                if (parentModal) {
                    const bsModal = new bootstrap.Modal(parentModal);
                    bsModal.show();
                }
            });
        });
    });
</script>

@endsection