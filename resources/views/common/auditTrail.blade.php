@extends('layout.blankpage')

@section('content')
@include ('layout.partials.message')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* ── Page Header ── */
    .page-header-audit {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header-audit .header-left h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-audit .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-audit .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }

    .page-header-audit .breadcrumb-item.active {
        color: #d1d5db;
    }

    .page-header-audit .total-badge {
        background: rgba(29, 211, 176, 0.15);
        color: white;
        border-radius: 50px;
        padding: 0.5rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .page-header-audit .total-badge span {
        background: var(--primary-green);
        color: var(--primary-dark);
        border-radius: 50px;
        padding: 0.15rem 0.65rem;
        font-weight: 700;
        margin-left: 0.35rem;
        font-size: 0.85rem;
    }

    /* ── Filter Card ── */
    .audit-filter-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .audit-filter-card .filter-header {
        background: var(--primary-dark);
        padding: 0.875rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .audit-filter-card .filter-header .header-icon {
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

    .audit-filter-card .filter-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .audit-filter-card .filter-body {
        padding: 1.25rem 1.5rem;
    }

    .audit-filter-card .form-label {
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
    }

    .audit-filter-card .form-control,
    .audit-filter-card .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .audit-filter-card .form-control:focus,
    .audit-filter-card .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .btn-reset {
        background: var(--primary-dark);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-reset:hover {
        background: #374151;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(31, 41, 55, 0.3);
    }

    /* ── Active Filters ── */
    .active-filters-bar {
        background: rgba(29, 211, 176, 0.08);
        border: 1px solid rgba(29, 211, 176, 0.2);
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        font-size: 0.85rem;
    }

    .active-filters-bar .badge {
        border-radius: 50px;
        padding: 0.3rem 0.75rem;
        font-weight: 500;
    }

    .btn-clear-filters {
        background: var(--primary-dark);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.25rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-clear-filters:hover {
        background: #374151;
        color: white;
    }

    /* ── Loading Indicator ── */
    .loading-overlay {
        text-align: center;
        padding: 2rem 0;
    }

    .loading-overlay .spinner-border {
        color: var(--primary-green) !important;
        width: 2.5rem;
        height: 2.5rem;
    }

    /* ── Audit Table Card ── */
    .audit-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .audit-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .audit-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .audit-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .audit-card-header .header-icon {
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

    .audit-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .audit-card-header .showing-badge {
        background: rgba(29, 211, 176, 0.15);
        color: #d1fae5;
        border-radius: 50px;
        padding: 0.35rem 0.85rem;
        font-size: 0.78rem;
        font-weight: 500;
    }

    .audit-card-body {
        padding: 0;
    }

    /* ── Backup & Restore Section ── */
    .backup-section-header {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.25rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .backup-section-header .header-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .backup-section-header h4 {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .backup-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
        height: 100%;
    }

    .backup-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .backup-card .backup-card-header {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .backup-card .backup-card-header.backup-type {
        background: var(--primary-dark);
    }

    .backup-card .backup-card-header.restore-type {
        background: #991b1b;
    }

    .backup-card .backup-card-header .header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .backup-card .backup-card-header.backup-type .header-icon {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
    }

    .backup-card .backup-card-header.restore-type .header-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .backup-card .backup-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .backup-card .backup-card-body {
        padding: 1.5rem;
    }

    .backup-card .backup-card-body p {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .backup-card .form-label {
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
    }

    .backup-card .form-control {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .backup-card .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .btn-backup {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: white;
        width: 100%;
        transition: all 0.2s;
    }

    .btn-backup:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .btn-restore {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: white;
        width: 100%;
        transition: all 0.2s;
    }

    .btn-restore:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        color: white;
    }

    .alert-styled {
        border-radius: 10px;
        border: none;
        font-size: 0.82rem;
        padding: 0.75rem 1rem;
    }

    .alert-styled.alert-info {
        background: rgba(29, 211, 176, 0.08);
        color: #065f46;
        border: 1px solid rgba(29, 211, 176, 0.2);
    }

    .alert-styled.alert-warning {
        background: rgba(245, 158, 11, 0.08);
        color: #92400e;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    /* ── Responsive Breakpoints ── */
    @media (max-width: 991px) {
        .container-fluid.px-4 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .audit-filter-card .filter-body .row .col-md-5,
        .audit-filter-card .filter-body .row .col-md-4,
        .audit-filter-card .filter-body .row .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 767px) {
        .container-fluid.px-4 {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 1rem !important;
        }

        .page-header-audit {
            padding: 1.25rem;
            border-radius: 12px;
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header-audit .header-left h1 {
            font-size: 1.35rem;
        }

        .page-header-audit .total-badge {
            font-size: 0.8rem;
        }

        .audit-filter-card {
            border-radius: 12px;
        }

        .audit-filter-card .filter-header {
            padding: 0.75rem 1.25rem;
        }

        .audit-filter-card .filter-body {
            padding: 1rem;
        }

        .audit-filter-card .filter-body .row .col-md-5,
        .audit-filter-card .filter-body .row .col-md-4,
        .audit-filter-card .filter-body .row .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .audit-card {
            border-radius: 12px;
        }

        .audit-card-header {
            padding: 0.875rem 1.25rem;
        }

        .backup-section-header {
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        .backup-card {
            border-radius: 12px;
        }
    }

    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-audit {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-audit .header-left h1 {
            font-size: 1.15rem;
        }

        .page-header-audit .breadcrumb {
            font-size: 0.75rem;
        }

        .page-header-audit .total-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.85rem;
        }

        .audit-filter-card .filter-header,
        .audit-card-header {
            padding: 0.75rem 1rem;
        }

        .audit-filter-card .filter-body {
            padding: 0.875rem;
        }

        .audit-card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .audit-card-header .header-left {
            justify-content: center;
        }

        .audit-card-header .showing-badge {
            text-align: center;
        }

        .backup-section-header {
            border-radius: 10px;
            padding: 0.875rem 1rem;
        }

        .backup-section-header h4 {
            font-size: 1rem;
        }

        .backup-card .backup-card-body {
            padding: 1rem;
        }

        .backup-card .backup-card-header {
            padding: 0.75rem 1rem;
        }

        .btn-backup, .btn-restore {
            font-size: 0.85rem;
            padding: 0.65rem 1rem;
        }
    }

    @media (max-width: 400px) {
        .page-header-audit .header-left h1 {
            font-size: 1rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-audit">
        <div class="header-left">
            <h1>Audit Trail</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Audit Trail</li>
            </ol>
        </div>
        <div class="total-badge" id="totalRecords">
            Total Records: <span>{{ $auditTrail->total() }}</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="audit-filter-card">
        <div class="filter-header">
            <span class="header-icon"><i class="fas fa-search"></i></span>
            <h5>Search &amp; Filter</h5>
        </div>
        <div class="filter-body">
            <form id="filterForm">
                <div class="row g-3">
                    <!-- Search Input with Clear Button -->
                    <div class="col-md-5">
                        <label for="search" class="form-label">
                            <i class="fas fa-search me-1"></i>Search
                        </label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search audit logs..."
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none; border-radius: 0 10px 10px 0;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Type -->
                    <div class="col-md-4">
                        <label for="filter" class="form-label">
                            <i class="fas fa-filter me-1"></i>Search In
                        </label>
                        <select name="filter" id="filter" class="form-select">
                            <option value="all" {{ request('filter', 'all') == 'all' ? 'selected' : '' }}>All Fields</option>
                            <option value="type" {{ request('filter') == 'type' ? 'selected' : '' }}>Action Type</option>
                            <option value="user" {{ request('filter') == 'user' ? 'selected' : '' }}>Changed By</option>
                            <option value="table" {{ request('filter') == 'table' ? 'selected' : '' }}>Table Name</option>
                            <option value="date" {{ request('filter') == 'date' ? 'selected' : '' }}>Date</option>
                        </select>
                    </div>

                    <!-- Action Type Filter -->
                    <div class="col-md-3">
                        <label for="action_type" class="form-label">
                            <i class="fas fa-tag me-1"></i>Action Type
                        </label>
                        <select name="action_type" id="action_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="CREATE" {{ request('action_type') == 'CREATE' ? 'selected' : '' }}>Create</option>
                            <option value="UPDATE" {{ request('action_type') == 'UPDATE' ? 'selected' : '' }}>Update</option>
                            <option value="DELETE" {{ request('action_type') == 'DELETE' ? 'selected' : '' }}>Delete</option>
                            <option value="LOGIN" {{ request('action_type') == 'LOGIN' ? 'selected' : '' }}>Login</option>
                            <option value="BACKUP" {{ request('action_type') == 'BACKUP' ? 'selected' : '' }}>Back Up</option>
                            <option value="RESTORE" {{ request('action_type') == 'RESTORE' ? 'selected' : '' }}>Restore</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-12">
                        <a href="#" id="resetFilters" class="btn-reset">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters Display -->
    <div id="activeFilters" style="display: none;">
        <div class="active-filters-bar">
            <i class="fas fa-info-circle" style="color: var(--primary-green);"></i>
            <strong>Active Filters:</strong>
            <span id="filterBadges"></span>
            <a href="#" id="clearAllFilters" class="btn-clear-filters ms-auto">
                <i class="fas fa-times me-1"></i>Clear All
            </a>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" style="display: none;" class="loading-overlay">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading audit records...</p>
    </div>

    <!-- Audit Table Card -->
    <div class="audit-card" id="auditTableCard">
        <div class="audit-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-clipboard-list"></i></span>
                <h5>System Audit Log</h5>
            </div>
            <span class="showing-badge" id="recordsInfo">
                @if($auditTrail->count() > 0)
                Showing {{ $auditTrail->firstItem() }} - {{ $auditTrail->lastItem() }} of {{ $auditTrail->total() }}
                @else
                No records
                @endif
            </span>
        </div>

        <div class="audit-card-body" id="auditTableBody">
            @include('common.audit_table', ['auditTrail' => $auditTrail])
        </div>
    </div>

    <!-- Backup & Restore Section Header -->
    <div class="backup-section-header">
        <span class="header-icon"><i class="fas fa-database"></i></span>
        <h4>Backup &amp; Restore</h4>
    </div>

    <div class="row mb-4">
        <!-- Backup Button -->
        <div class="col-md-6 mb-3">
            <div class="backup-card">
                <div class="backup-card-header backup-type">
                    <span class="header-icon"><i class="fas fa-download"></i></span>
                    <h5>Backup Database</h5>
                </div>
                <div class="backup-card-body">
                    <p>
                        Download a secure, encrypted backup of the entire database.
                        A unique password will be sent to <strong>nubzman123@gmail.com</strong>.
                    </p>
                    <button class="btn btn-backup mt-2" id="backupBtn">
                        <i class="fas fa-download me-2"></i> Create Backup
                    </button>
                    <div class="alert-styled alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Note:</strong> Save the password from your email - you'll need it to restore this backup.
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore Form -->
        <div class="col-md-6 mb-3">
            <div class="backup-card">
                <div class="backup-card-header restore-type">
                    <span class="header-icon"><i class="fas fa-upload"></i></span>
                    <h5>Restore Database</h5>
                </div>
                <div class="backup-card-body">
                    <p>
                        Upload a backup file and enter the password from your email to restore the database.
                    </p>
                    <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data" id="restoreForm" class="mt-2">
                        @csrf
                        <div class="mb-3">
                            <label for="backup_file" class="form-label">
                                <i class="fas fa-file-archive me-1"></i> Select Backup File
                            </label>
                            <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".zip" required>
                            <small class="text-muted">Accepted format: .zip</small>
                        </div>
                        <div class="mb-3">
                            <label for="backup_password" class="form-label">
                                <i class="fas fa-key me-1"></i> Backup Password
                            </label>
                            <input type="text" name="backup_password" id="backup_password" class="form-control"
                                placeholder="XXXX-XXXX-XXXX-XXXX"
                                required
                                minlength="12"
                                style="font-family: 'Courier New', monospace; letter-spacing: 1px;">
                            <small class="text-muted">Check email: <strong>nubzman123@gmail.com</strong></small>
                        </div>
                        <div class="alert-styled alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Warning:</strong> This will replace ALL current data!
                        </div>
                        <button type="button" id="restoreBtn" class="btn btn-restore">
                            <i class="fas fa-upload me-2"></i> Restore Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Backup button with SweetAlert
        $('#backupBtn').on('click', function() {
            Swal.fire({
                title: 'Create Database Backup?',
                html: '<p>A unique backup password will be emailed to:<br><strong>nubzman123@gmail.com</strong></p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1f2937',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-download me-2"></i> Create Backup',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Creating Backup...',
                        html: 'Please wait while we backup your database.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Redirect to backup download
                    window.location.href = '{{ route('backup.download') }}';
                }
            });
        });

        // Restore form with SweetAlert
        $('#restoreBtn').on('click', function(e) {
            e.preventDefault();

            // Validate form fields
            const backupFile = $('#backup_file').val();
            const backupPassword = $('#backup_password').val();

            if (!backupFile) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing File',
                    text: 'Please select a backup file to restore.',
                    confirmButtonColor: '#1dd3b0'
                });
                return;
            }

            if (!backupPassword || backupPassword.length < 12) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Password',
                    text: 'Please enter the backup password (minimum 12 characters).',
                    confirmButtonColor: '#1dd3b0'
                });
                return;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Restore Database?',
                html: '<div class="text-start">' +
                      '<p><strong>⚠️ WARNING:</strong> This will replace ALL current data with the backup data.</p>' +
                      '<p>This action <strong>cannot be undone</strong>.</p>' +
                      '<p>Are you absolutely sure you want to continue?</p>' +
                      '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-upload me-2"></i> Yes, Restore Database',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Restoring Database...',
                        html: 'Please wait. This may take a few moments.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Submit the form
                    $('#restoreForm').submit();
                }
            });
        });

        let searchTimeout;
        const searchInput = $('#search');
        const filterSelect = $('#filter');
        const actionTypeSelect = $('#action_type');
        const clearSearchBtn = $('#clearSearch');
        const loadingIndicator = $('#loadingIndicator');
        const auditTableCard = $('#auditTableCard');

        // Show/hide clear button based on input value
        function toggleClearButton() {
            if (searchInput.val().length > 0) {
                clearSearchBtn.show();
            } else {
                clearSearchBtn.hide();
            }
        }

        // Update active filters display
        function updateActiveFilters() {
            const search = searchInput.val();
            const filter = filterSelect.val();
            const actionType = actionTypeSelect.val();

            let badges = '';
            let hasFilters = false;

            if (search) {
                badges += `<span class="badge bg-primary me-1">Search: "${search}"</span>`;
                hasFilters = true;
            }
            if (filter && filter !== 'all') {
                badges += `<span class="badge bg-success me-1">Search In: ${filter.charAt(0).toUpperCase() + filter.slice(1)}</span>`;
                hasFilters = true;
            }
            if (actionType) {
                badges += `<span class="badge bg-warning text-dark me-1">Type: ${actionType}</span>`;
                hasFilters = true;
            }

            if (hasFilters) {
                $('#filterBadges').html(badges);
                $('#activeFilters').show();
            } else {
                $('#activeFilters').hide();
            }
        }

        // Clear all filters
        function clearAllFilters() {
            searchInput.val('');
            filterSelect.val('all');
            actionTypeSelect.val('');
            toggleClearButton();
            loadAuditTrail();
        }

        // Load audit trail data via AJAX
        function loadAuditTrail(page = 1) {
            const search = searchInput.val();
            const filter = filterSelect.val();
            const actionType = actionTypeSelect.val();

            // Show loading, hide table
            loadingIndicator.show();
            auditTableCard.css('opacity', '0.5');

            $.ajax({
                url: '{{ route("audit.index") }}',
                method: 'GET',
                data: {
                    search: search,
                    filter: filter,
                    action_type: actionType,
                    page: page,
                    ajax: 1
                },
                success: function(response) {
                    // Update table body
                    $('#auditTableBody').html(response.html);

                    // Update total records badge
                    $('#totalRecords').html('Total Records: <span>' + response.total + '</span>');

                    // Update records info
                    if (response.count > 0) {
                        $('#recordsInfo').text(`Showing ${response.from} - ${response.to} of ${response.total}`);
                    } else {
                        $('#recordsInfo').text('No records');
                    }

                    // Update active filters
                    updateActiveFilters();

                    // Hide loading, show table
                    loadingIndicator.hide();
                    auditTableCard.css('opacity', '1');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    loadingIndicator.hide();
                    auditTableCard.css('opacity', '1');
                    alert('An error occurred while loading the audit trail. Please try again.');
                }
            });
        }

        // Search input with debounce
        searchInput.on('input', function() {
            toggleClearButton();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadAuditTrail();
            }, 500);
        });

        // Clear search button
        clearSearchBtn.on('click', function() {
            searchInput.val('');
            toggleClearButton();
            loadAuditTrail();
        });

        // Filter dropdowns - instant change
        filterSelect.on('change', function() {
            loadAuditTrail();
        });

        actionTypeSelect.on('change', function() {
            loadAuditTrail();
        });

        // Close active filters alert
        $('#closeActiveFilters').on('click', function() {
            $('#activeFilters').hide();
        });

        // Clear All button - AJAX version
        $(document).on('click', '#clearAllFilters', function(e) {
            e.preventDefault();
            clearAllFilters();
        });

        // Reset button - AJAX version
        $(document).on('click', '#resetFilters', function(e) {
            e.preventDefault();
            clearAllFilters();
        });

        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            loadAuditTrail(page);

            // Scroll to top of table
            $('html, body').animate({
                scrollTop: auditTableCard.offset().top - 100
            }, 300);
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl+F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
            // ESC to clear search
            if (e.key === 'Escape' && searchInput.val() !== '') {
                e.preventDefault();
                clearAllFilters();
                searchInput.focus();
            }
        });

        // Initial state
        toggleClearButton();
        updateActiveFilters();
    });
</script>



<!-- SweetAlert Notifications -->
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#1dd3b0',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('Status'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('Status') }}',
            confirmButtonColor: '#1dd3b0',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('Danger'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('Danger') }}',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
        });
    @endif
</script>

@endsection
