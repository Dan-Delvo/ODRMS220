@extends('layout.blankpage')

@section('content')
    {{-- Header Section --}}
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h1 class="mt-4">
                <span class="badge page-title-badge">Archived Requests</span>
            </h1>
        </div>
    </div>

    <livewire:archived-doc-requests-table />
@endsection

<style>
    /* ===== CORE VARIABLES ===== */
    :root {
        --primary-color: #1dd3b0;
        --secondary-color: #1f2937;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
    }

    /* ===== HEADER BADGES ===== */
    .page-title-badge {
        background-color: #1dd3b0;
        font-size: 1.5rem !important;
        padding: .5rem .5rem !important;
        font-weight: 700 !important;
    }

    /* ===== CARD STYLES ===== */
    .archived-card {
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .archived-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }

    /* ===== CARD HEADER ===== */
    .card-header-custom {
        background: linear-gradient(135deg, var(--secondary-color) 0%, #374151 100%);
        color: white;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 3px solid var(--primary-color);
    }

    .card-header-custom h5 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    @media (min-width: 768px) {
        .card-header-custom {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    /* ===== SEARCH & FILTER CONTROLS ===== */
    .search-wrapper {
        flex: 1 1 auto;
        min-width: 200px;
        max-width: 350px;
    }

    .search-wrapper .input-group-text {
        background: white;
        border-right: none;
    }

    .search-wrapper .form-control {
        border-left: none;
    }

    .search-wrapper .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.15);
        border-left: none;
    }

    .search-wrapper .form-control:focus + .input-group-text {
        border-color: var(--primary-color);
    }

    .filter-wrapper,
    .sort-wrapper {
        flex: 0 0 auto;
    }

    .filter-wrapper select,
    .sort-wrapper select {
        min-width: 130px;
        background-color: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #1f2937;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .filter-wrapper select:hover,
    .sort-wrapper select:hover {
        background-color: white;
        border-color: var(--primary-color);
    }

    .filter-wrapper select:focus,
    .sort-wrapper select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        background-color: white;
    }

    .reset-btn {
        border: 1px solid rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }

    .reset-btn:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transform: rotate(180deg);
    }

    /* ===== SEARCH INFO BANNER ===== */
    .search-info-banner {
        background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%);
        border-left: 4px solid var(--info-color);
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .search-info-banner .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    /* ===== TABLE STYLES ===== */
    .modern-table {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.75rem;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e5e7eb;
    }

    .modern-table tbody tr:hover {
        background-color: #f9fafb;
        transform: scale(1.01);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .modern-table tbody td {
        padding: 0.875rem 0.75rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .modern-table tbody tr:last-child {
        border-bottom: none;
    }

    /* ===== STATUS BADGE ===== */
    .status-badge-custom {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.45rem 0.85rem;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white !important;
        box-shadow: 0 3px 6px rgba(59, 130, 246, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    /* ===== CLAIMER NAME ===== */
    .claimer-name {
        color: #374151;
        font-weight: 500;
    }

    /* ===== STUDENT NAME ===== */
    .student-name {
        color: #059669;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* ===== DOCUMENT TYPE ===== */
    .document-type {
        color: #0891b2;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        background: white;
        border-radius: 0.5rem;
        padding: 3rem 1rem;
    }

    .empty-state i {
        opacity: 0.3;
    }

    /* ===== LOADING STATE ===== */
    .loading-overlay {
        position: relative;
        z-index: 10;
    }

    .opacity-50 {
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    /* ===== MODERN PAGINATION ===== */
    .pagination-wrapper {
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .pagination-info {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #f3f4f6;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .pagination-modern {
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .pagination-modern .page-item {
        margin: 0;
    }

    .pagination-modern .page-link {
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
        min-width: 40px;
        text-align: center;
        background: white;
    }

    .pagination-modern .page-link:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(29, 211, 176, 0.2);
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-color) 0%, #17a589 100%);
        border-color: var(--primary-color);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(29, 211, 176, 0.3);
    }

    .pagination-modern .page-item.disabled .page-link {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
    }

    /* ===== RESPONSIVE TABLE ===== */
    .table-responsive {
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    @media (max-width: 768px) {
        .search-wrapper {
            max-width: 100%;
        }

        .filter-wrapper select,
        .sort-wrapper select {
            min-width: 100px;
        }

        .modern-table {
            font-size: 0.8rem;
        }

        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.5rem;
        }

        .pagination-modern .page-link {
            padding: 0.375rem 0.5rem;
            min-width: 35px;
            font-size: 0.875rem;
        }
    }

    /* ===== SMOOTH TRANSITIONS ===== */
    .btn,
    .form-control,
    .form-select,
    .badge {
        transition: all 0.2s ease-in-out;
    }

    /* ===== UTILITY CLASSES ===== */
    .fw-semibold {
        font-weight: 600;
    }

    .text-truncate {
        max-width: 300px;
    }
</style>
