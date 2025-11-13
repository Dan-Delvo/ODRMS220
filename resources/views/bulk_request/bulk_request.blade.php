@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --primary-color: #1dd3b0;
        --primary-hover: #17b89a;
        --secondary-color: #222b37;
        --danger-color: #ef4444;
        --success-color: #10b981;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    /* Kanban Board Styling */
    .kanban-column {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
    }

    .kanban-header {
        padding: 1.25rem 1.5rem;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ✅ Updated color schemes using Bootstrap colors */
    .kanban-header.pending {
        background: #6c757d;
        /* Bootstrap bg-secondary */
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    }

    .kanban-header.processing {
        background: #ffc107;
        /* Bootstrap bg-warning */
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }

    .kanban-header.for-release {
        background: #ffff00;
        /* Pure yellow */
        background: linear-gradient(135deg, #ffff00 0%, #e6e600 100%);
    }

    .kanban-header.claimed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .kanban-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* ✅ Dark count badge for yellow backgrounds */
    .kanban-header.processing .kanban-count,
    .kanban-header.for-release .kanban-count {
        background: rgba(0, 0, 0, 0.15);
    }

    .kanban-body {
        padding: 1rem;
        overflow-y: auto;
        flex: 1;
        background: var(--bg-light);
    }

    .kanban-body::-webkit-scrollbar {
        width: 6px;
    }

    .kanban-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .kanban-body::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 10px;
    }

    .kanban-body::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    /* Request Card Styling */
    .request-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-sm);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .request-card:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }

    .request-card-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .request-card-subtitle {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }

    .request-card-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-dark);
        font-size: 0.9rem;
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: var(--bg-light);
        border-radius: 8px;
    }

    .request-card-info i {
        color: var(--primary-color);
    }

    .btn-view-students {
        width: 100%;
        background: var(--bg-light);
        border: 2px solid var(--border-color);
        color: var(--text-dark);
        padding: 0.65rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-view-students:hover {
        background: white;
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-1px);
    }

    .btn-move {
        width: 100%;
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
        border: none;
        color: white;
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-move:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 1rem;
        opacity: 0.3;
        stroke: var(--text-muted);
    }

    .empty-state p {
        font-size: 0.9rem;
    }

    /* Claimer Info Box */
    .claimer-info-box {
        margin-top: 1rem;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(29, 211, 176, 0.05) 0%, rgba(29, 211, 176, 0.1) 100%);
        border-left: 4px solid var(--primary-color);
        border-radius: 8px;
    }

    .claimer-info-box small {
        color: var(--text-dark);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .claimer-info-box i {
        color: var(--primary-color);
        margin-right: 0.5rem;
        width: 16px;
    }

    /* Modern Modal Styling */
    .claimer-modal .modal-content {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(34, 43, 55, 0.2);
        overflow: hidden;
    }

    .claimer-modal .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        border: none;
        padding: 1.5rem 2rem;
    }

    .claimer-modal .modal-title {
        color: #fff;
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .claimer-modal .modal-title i {
        font-size: 1.4rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .claimer-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
        transition: all 0.3s ease;
    }

    .claimer-modal .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .claimer-modal .modal-body {
        padding: 2rem;
        background: #ffffff;
    }

    .claimer-modal .form-label {
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .claimer-modal .form-label i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    .claimer-modal .form-control {
        background: var(--bg-light);
        border: 2px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-dark);
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .claimer-modal .form-control:focus {
        background: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.1);
        color: var(--text-dark);
        outline: none;
    }

    .claimer-modal .form-control::placeholder {
        color: var(--text-muted);
    }

    .claimer-modal .input-icon {
        position: relative;
    }

    .claimer-modal .input-icon i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        font-size: 1rem;
        pointer-events: none;
        z-index: 10;
    }

    .claimer-modal .input-icon .form-control {
        padding-left: 3rem;
    }

    .claimer-modal .modal-footer {
        background: var(--bg-light);
        border-top: 2px solid var(--border-color);
        padding: 1.25rem 2rem;
        gap: 1rem;
        display: flex;
        justify-content: flex-end;
    }

    .claimer-modal .btn-cancel {
        background: #ffffff;
        border: 2px solid var(--border-color);
        color: var(--text-muted);
        padding: 0.75rem 1.75rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .claimer-modal .btn-cancel:hover {
        background: var(--bg-light);
        border-color: var(--text-muted);
        color: var(--text-dark);
        transform: translateY(-1px);
    }

    .claimer-modal .btn-confirm {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        border: none;
        color: #fff;
        padding: 0.75rem 1.75rem;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.25);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .claimer-modal .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(29, 211, 176, 0.35);
    }

    .claimer-modal .btn-confirm i {
        font-size: 1rem;
    }

    .text-danger {
        color: var(--danger-color) !important;
    }

    /* Accent divider for form groups */
    .claimer-modal .mb-4 {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .claimer-modal .mb-4::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-color), transparent);
        border-radius: 2px;
    }

    .claimer-modal .mb-4:last-child::after {
        display: none;
    }

    .claimer-modal .mb-4:last-child {
        padding-bottom: 0;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 2rem;
    }

    .page-badge {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-size: 1.75rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .breadcrumb {
        background: transparent;
        margin-bottom: 0;
        padding: 0.5rem 0;
    }

    .breadcrumb-item a {
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .breadcrumb-item a:hover {
        color: var(--primary-color);
    }

    .breadcrumb-item.active {
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="page-header">
    <div class="row g-2">
        <div class="col-md-12">
            <h1 class="mt-4 mb-3">
                <span class="page-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Bulk Requests
                </span>
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('bulk_request.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bulk Requests</li>
            </ol>
        </div>
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

                    <form action="{{ route('bulk_request.moveToProcessing', $req->Request_ID) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-move">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                            Move to Processing
                        </button>
                    </form>
                </div>
                <x-student-count-modal :request="$req" :students="$students" />
                @endif
                @endforeach

                @if(!$hasPending)
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
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

                    <form action="{{ route('bulk_request.moveToForRelease', $req->Request_ID) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-move">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                            Move to For Release
                        </button>
                    </form>
                </div>
                <x-student-count-modal :request="$req" :students="$students" />
                @endif
                @endforeach

                @if(!$hasProcessing)
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
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

                    <button type="button" class="btn-move" data-bs-toggle="modal" data-bs-target="#claimerModal-{{ $req->Request_ID }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p>No claimed requests</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

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