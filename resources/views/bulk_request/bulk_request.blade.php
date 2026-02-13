@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --shadow-soft: 0 2px 15px rgba(0,0,0,0.08);
        --shadow-hover: 0 4px 20px rgba(0,0,0,0.12);
    }

    /* ===== Page Header ===== */
    .page-header-bulk {
        background: var(--primary-dark);
        border-radius: 14px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .page-header-bulk h1 {
        color: #fff;
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
    }
    .page-header-bulk h1 i {
        color: var(--primary-green);
    }
    .page-header-bulk .breadcrumb {
        background: transparent;
        margin: 0;
        padding: 0;
    }
    .page-header-bulk .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }
    .page-header-bulk .breadcrumb-item.active {
        color: rgba(255,255,255,0.6);
    }
    .page-header-bulk .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255,255,255,0.4);
    }

    /* ===== Kanban Board ===== */
    .kanban-column {
        background: #fff;
        border-radius: 14px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        height: calc(100vh - 220px);
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.3s ease;
    }
    .kanban-column:hover {
        box-shadow: var(--shadow-hover);
    }

    .kanban-header {
        padding: 1.1rem 1.25rem;
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kanban-header.pending {
        background: var(--primary-dark);
    }
    .kanban-header.processing {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .kanban-header.for-release {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .kanban-header.claimed {
        background: linear-gradient(135deg, var(--primary-green), #17b89a);
    }

    .kanban-count {
        background: rgba(255,255,255,0.2);
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .kanban-body {
        padding: 1rem;
        overflow-y: auto;
        flex: 1;
        background: #f9fafb;
    }
    .kanban-body::-webkit-scrollbar {
        width: 5px;
    }
    .kanban-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .kanban-body::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .kanban-body::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* ===== Request Card ===== */
    .request-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.15rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        transition: all 0.25s ease;
    }
    .request-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: var(--primary-green);
        transform: translateY(-2px);
    }

    .request-card-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1f2937;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .request-card-title i {
        color: var(--primary-green);
        font-size: 0.9rem;
    }

    .request-card-subtitle {
        font-size: 0.7rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.65rem;
    }

    .request-card-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #374151;
        font-size: 0.85rem;
        margin-bottom: 0.85rem;
        padding: 0.45rem 0.65rem;
        background: #f3f4f6;
        border-radius: 8px;
    }
    .request-card-info i {
        color: var(--primary-green);
        font-size: 0.8rem;
    }

    /* ===== Kanban Buttons ===== */
    .btn-view-students {
        width: 100%;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 0.55rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.82rem;
        transition: all 0.2s ease;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }
    .btn-view-students:hover {
        background: #fff;
        border-color: var(--primary-green);
        color: var(--primary-green);
        transform: translateY(-1px);
    }

    .btn-move {
        width: 100%;
        background: linear-gradient(135deg, var(--primary-green), #17b89a);
        border: none;
        color: #fff;
        padding: 0.6rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.82rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }
    .btn-move:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
        color: #fff;
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #9ca3af;
    }
    .empty-state i {
        font-size: 2.2rem;
        display: block;
        margin-bottom: 0.6rem;
        opacity: 0.35;
    }
    .empty-state p {
        font-size: 0.85rem;
        margin: 0;
    }

    /* ===== Claimer Info Box ===== */
    .claimer-info-box {
        margin-top: 0.85rem;
        padding: 0.85rem;
        background: rgba(29, 211, 176, 0.08);
        border-left: 3px solid var(--primary-green);
        border-radius: 8px;
    }
    .claimer-info-box small {
        color: #374151;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        font-size: 0.8rem;
    }
    .claimer-info-box i {
        color: var(--primary-green);
        margin-right: 0.4rem;
        width: 14px;
    }

    /* ===== Modal ===== */
    .claimer-modal .modal-content {
        background: #fff;
        border: none;
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(31, 41, 55, 0.2);
        overflow: hidden;
    }
    .claimer-modal .modal-header {
        background: var(--primary-dark);
        border: none;
        padding: 1.2rem 1.5rem;
    }
    .claimer-modal .modal-title {
        color: #fff;
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .claimer-modal .modal-title i {
        font-size: 1.2rem;
        color: var(--primary-green);
    }
    .claimer-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
        transition: all 0.3s ease;
    }
    .claimer-modal .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .claimer-modal .modal-body {
        padding: 1.5rem 2rem;
        background: #fff;
    }
    .claimer-modal .form-label {
        color: #374151;
        font-weight: 600;
        font-size: 0.82rem;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .claimer-modal .form-label i {
        color: var(--primary-green);
        font-size: 0.9rem;
    }
    .claimer-modal .form-control {
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        color: #1f2937;
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .claimer-modal .form-control:focus {
        background: #fff;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
        color: #1f2937;
        outline: none;
    }
    .claimer-modal .form-control::placeholder {
        color: #9ca3af;
    }
    .claimer-modal .input-icon {
        position: relative;
    }
    .claimer-modal .input-icon > i {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-green);
        font-size: 0.9rem;
        pointer-events: none;
        z-index: 10;
    }
    .claimer-modal .input-icon .form-control {
        padding-left: 2.8rem;
    }

    .claimer-modal .modal-footer {
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
        gap: 0.75rem;
        display: flex;
        justify-content: flex-end;
    }
    .claimer-modal .btn-cancel {
        background: transparent;
        border: 1px solid #d1d5db;
        color: #6b7280;
        padding: 0.6rem 1.4rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .claimer-modal .btn-cancel:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #374151;
    }
    .claimer-modal .btn-confirm {
        background: linear-gradient(135deg, var(--primary-green), #17b89a);
        border: none;
        color: #fff;
        padding: 0.6rem 1.4rem;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.25);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .claimer-modal .btn-confirm:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(29, 211, 176, 0.35);
    }
    .claimer-modal .btn-confirm i {
        font-size: 0.9rem;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    /* Accent divider for form groups */
    .claimer-modal .mb-4 {
        position: relative;
        padding-bottom: 1.25rem;
    }
    .claimer-modal .mb-4::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 36px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-green), transparent);
        border-radius: 2px;
    }
    .claimer-modal .mb-4:last-child::after {
        display: none;
    }
    .claimer-modal .mb-4:last-child {
        padding-bottom: 0;
    }

    /* ===== Responsive ===== */
    @media (max-width: 991px) {
        .kanban-column {
            height: auto;
            min-height: 300px;
            max-height: 500px;
        }
    }
    @media (max-width: 767px) {
        .page-header-bulk {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.2rem 1.25rem;
        }
        .page-header-bulk h1 {
            font-size: 1.15rem;
        }
        .col-md-3 {
            margin-bottom: 1rem;
        }
    }
    @media (max-width: 575px) {
        .page-header-bulk {
            padding: 1rem;
            border-radius: 10px;
        }
    }
</style>
@endpush

@section('content')
@include('layout.partials.message')

<div class="container-fluid px-4 py-4">

{{-- Page Header --}}
<div class="page-header-bulk">
    <div>
        <h1><i class="fas fa-layer-group me-2"></i>Bulk Requests</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('bulk_request.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Bulk Requests</li>
        </ol>
    </div>
</div>

<div class="row g-3">
    {{-- Pending Column --}}
    <div class="col-md-3">
        <div class="kanban-column">
            <div class="kanban-header pending">
                <span>Pending</span>
                <span class="kanban-count">{{ $requests->where('Status', 'Pending')->count() }}</span>
            </div>
            <div class="kanban-body">
                @php $hasPending = false; @endphp

                @foreach($requests as $req)
                @if($req->Status == 'Pending')
                @php $hasPending = true; @endphp
                <div class="request-card">
                    <div class="request-card-title">
                        <i class="fas fa-school"></i>
                        {{ $req->School_Name }}
                    </div>
                    <div class="request-card-subtitle">School Name</div>
                    <div class="request-card-info">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ $req->Doc_Type }}</span>
                    </div>

                    <button type="button" class="btn-view-students" data-bs-toggle="modal" data-bs-target="#modal-{{ $req->Request_ID }}">
                        <i class="fas fa-users"></i>
                        {{ $req->students_count }} Students
                    </button>

                    <form action="{{ route('bulk_request.moveToProcessing', $req->Request_ID) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-move">
                            <i class="fas fa-chevron-right"></i>
                            Move to Processing
                        </button>
                    </form>
                </div>
                <x-student-count-modal :request="$req" :students="$students" />
                @endif
                @endforeach

                @if(!$hasPending)
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No pending requests</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Processing Column --}}
    <div class="col-md-3">
        <div class="kanban-column">
            <div class="kanban-header processing">
                <span>Processing</span>
                <span class="kanban-count">{{ $requests->where('Status', 'Processing')->count() }}</span>
            </div>
            <div class="kanban-body">
                @php $hasProcessing = false; @endphp
                @foreach($requests as $req)
                @if($req->Status == 'Processing')
                @php $hasProcessing = true; @endphp
                <div class="request-card">
                    <div class="request-card-title">
                        <i class="fas fa-school"></i>
                        {{ $req->School_Name }}
                    </div>
                    <div class="request-card-subtitle">School Name</div>
                    <div class="request-card-info">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ $req->Doc_Type }}</span>
                    </div>

                    <button type="button" class="btn-view-students" data-bs-toggle="modal" data-bs-target="#modal-{{ $req->Request_ID }}">
                        <i class="fas fa-users"></i>
                        {{ $req->students_count }} Students
                    </button>

                    <form action="{{ route('bulk_request.moveToForRelease', $req->Request_ID) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-move">
                            <i class="fas fa-chevron-right"></i>
                            Move to For Release
                        </button>
                    </form>
                </div>
                <x-student-count-modal :request="$req" :students="$students" />
                @endif
                @endforeach

                @if(!$hasProcessing)
                <div class="empty-state">
                    <i class="fas fa-cogs"></i>
                    <p>No processing requests</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- For Release Column --}}
    <div class="col-md-3">
        <div class="kanban-column">
            <div class="kanban-header for-release">
                <span>For Release</span>
                <span class="kanban-count">{{ $requests->where('Status', 'For Release')->count() }}</span>
            </div>
            <div class="kanban-body">
                @php $hasForRelease = false; @endphp

                @foreach($requests as $req)
                @if($req->Status == 'For Release')
                @php $hasForRelease = true; @endphp
                <div class="request-card">
                    <div class="request-card-title">
                        <i class="fas fa-school"></i>
                        {{ $req->School_Name }}
                    </div>
                    <div class="request-card-subtitle">School Name</div>
                    <div class="request-card-info">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ $req->Doc_Type }}</span>
                    </div>

                    <button type="button" class="btn-view-students" data-bs-toggle="modal" data-bs-target="#modal-{{ $req->Request_ID }}">
                        <i class="fas fa-users"></i>
                        {{ $req->students_count }} Students
                    </button>

                    <button type="button" class="btn-move" data-bs-toggle="modal" data-bs-target="#claimerModal-{{ $req->Request_ID }}">
                        <i class="fas fa-check"></i>
                        Move to Claimed
                    </button>
                </div>

                <x-student-count-modal :request="$req" :students="$students" />

                {{-- Modern Claimer Modal --}}
                <div class="modal fade claimer-modal" id="claimerModal-{{ $req->Request_ID }}" tabindex="-1" aria-labelledby="claimerModalLabel-{{ $req->Request_ID }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="claimerModalLabel-{{ $req->Request_ID }}">
                                    <i class="fas fa-user-check"></i>
                                    Enter Claimer Information
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('bulk_request.moveToClaimed', $req->Request_ID) }}"
                                method="POST"
                                class="claimer-form"
                                data-request-id="{{ $req->Request_ID }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-4">
                                        <label for="claimer_fname_{{ $req->Request_ID }}" class="form-label">
                                            <i class="fas fa-user"></i>
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <i class="fas fa-signature"></i>
                                            <input type="text"
                                                class="form-control"
                                                id="claimer_fname_{{ $req->Request_ID }}"
                                                name="claimer_fname"
                                                required
                                                placeholder="Enter first name">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="claimer_lname_{{ $req->Request_ID }}" class="form-label">
                                            <i class="fas fa-user"></i>
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <i class="fas fa-signature"></i>
                                            <input type="text"
                                                class="form-control"
                                                id="claimer_lname_{{ $req->Request_ID }}"
                                                name="claimer_lname"
                                                required
                                                placeholder="Enter last name">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="claimed_date_{{ $req->Request_ID }}" class="form-label">
                                            <i class="fas fa-calendar-check"></i>
                                            Date Claimed <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                            <input type="date"
                                                class="form-control"
                                                id="claimed_date_{{ $req->Request_ID }}"
                                                name="claimed_date"
                                                required
                                                max="{{ date('Y-m-d') }}"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-confirm">
                                        <i class="fas fa-check-circle"></i>
                                        Confirm Claim
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach

                @if(!$hasForRelease)
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No requests for release</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Claimed Column --}}
    <div class="col-md-3">
        <div class="kanban-column">
            <div class="kanban-header claimed">
                <span>Claimed</span>
                <span class="kanban-count">{{ $requests->where('Status', 'Claimed')->count() }}</span>
            </div>
            <div class="kanban-body">
                @php $hasClaimed = false; @endphp

                @foreach($requests as $req)
                @if($req->Status == 'Claimed')
                @php $hasClaimed = true; @endphp
                <div class="request-card">
                    <div class="request-card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                        {{ $req->School_Name }}
                    </div>
                    <div class="request-card-subtitle">School Name</div>
                    <div class="request-card-info">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ $req->Doc_Type }}</span>
                    </div>

                    <button type="button" class="btn-view-students" data-bs-toggle="modal" data-bs-target="#modal-{{ $req->Request_ID }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        {{ $req->students_count }} Students
                    </button>

                    @if($req->claimer)
                    <div class="claimer-info-box">
                        <small>
                            <span>
                                <i class="fas fa-user-check"></i>
                                <strong>Claimed by:</strong> {{ $req->claimer->full_name }}
                            </span>
                            <span>
                                <i class="fas fa-calendar"></i>
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($req->claimer->claimed_date)->format('M d, Y') }}
                            </span>
                        </small>
                    </div>
                    @endif
                </div>
                <x-student-count-modal :request="$req" :students="$students" />
                @endif
                @endforeach

                @if(!$hasClaimed)
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No claimed requests</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div> {{-- close container-fluid --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all claimer forms
        const claimerForms = document.querySelectorAll('.claimer-form');

        claimerForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const firstName = formData.get('claimer_fname');
                const lastName = formData.get('claimer_lname');
                const claimedDate = formData.get('claimed_date');

                // Show confirmation dialog
                Swal.fire({
                    title: 'Confirm Claimer Information',
                    html: `
                    <div style="text-align: left; padding: 1rem;">
                        <p><strong><i class="fas fa-user"></i> Name:</strong> ${firstName} ${lastName}</p>
                        <p><strong><i class="fas fa-calendar"></i> Date Claimed:</strong> ${new Date(claimedDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    </div>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1dd3b0',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check"></i> Yes, Mark as Claimed',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                    customClass: {
                        popup: 'animated fadeInDown'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Close the modal first
                        const modal = form.closest('.modal');
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }

                        // Show loading animation
                        Swal.fire({
                            title: 'Processing...',
                            html: `
                            <div style="padding: 2rem 0;">
                                <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: #1dd3b0;"></i>
                                <p style="margin-top: 1rem; color: #64748b;">Moving request to Claimed status...</p>
                            </div>
                        `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            // didOpen: () => {
                            //     Swal.showLoading();
                            // }
                        });

                        // Submit the form
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                });
            });
        });
    });
</script>
@endsection
