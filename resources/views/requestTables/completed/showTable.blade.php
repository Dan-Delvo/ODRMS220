@extends('layout.blankpage')

@section ('content')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-show {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }
    .page-header-show h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }
    .page-header-show .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }
    .page-header-show .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }
    .page-header-show .breadcrumb-item.active {
        color: #d1d5db;
    }

    .detail-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .detail-card:hover {
        box-shadow: var(--card-hover-shadow);
    }
    .detail-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .detail-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .detail-card-header .header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    .detail-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }
    .btn-back {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-back:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        color: white;
    }
    .detail-card-body {
        padding: 1.5rem;
    }
    .detail-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .detail-item label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6b7280;
        margin-bottom: 0.25rem;
        display: block;
    }
    .detail-item p {
        font-size: 0.95rem;
        color: #1f2937;
        margin: 0;
        font-weight: 500;
    }
    .status-badge-show {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #1f2937;
    }

    @media (max-width: 767px) {
        .page-header-show { padding: 1.25rem; border-radius: 12px; }
        .page-header-show h1 { font-size: 1.35rem; }
        .detail-card { border-radius: 12px; }
        .detail-card-header { padding: 0.875rem 1rem; }
        .detail-card-body { padding: 1rem; }
    }
    @media (max-width: 575px) {
        .page-header-show h1 { font-size: 1.15rem; }
        .detail-card-header h5 { font-size: 0.875rem; }
        .btn-back { font-size: 0.8rem; padding: 0.4rem 1rem; }
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="page-header-show">
        <h1><i class="fas fa-clipboard-check me-2"></i>For Release Requests</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('tables') }}">For Release Requests</a></li>
            <li class="breadcrumb-item active">Request Details</li>
        </ol>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-info-circle"></i></span>
                <h5>Requester's Information</h5>
            </div>
            <a href="{{ url('tables') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="detail-card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>ID</label>
                        <p>{{ $table->id }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Claimer</label>
                        <p>{{ $table->claimer->full_name }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Student Information ID</label>
                        <p>{{ $table->student_information_id }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Document ID</label>
                        <p>{{ $table->document_id }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Requesting School / Entity</label>
                        <p>{{ $table->request_schl_entity }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Requested SF10</label>
                        <p>{{ $table->requested_sf10 }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Release Mode</label>
                        <p>{{ $table->release_mode }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Remarks</label>
                        <p>{{ $table->remarks }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Status</label>
                        <p><span class="status-badge-show">{{ $table->status }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
