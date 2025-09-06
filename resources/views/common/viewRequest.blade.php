@extends('layout.studentpage')

@section('content')

<style>
    :root {
        --sidebar-width: 270px;
        --sidebar-collapsed-width: 85px;
        --primary-teal: #1dd3b0;
        --dark-bg: #0f172a;
        --card-bg: #1e293b;
        --hover-bg: #334155;
        --border-color: #475569;
        --text-primary: #f1f5f9;
        --text-secondary: #cbd5e1;
        --text-muted: #94a3b8;
    }

    body, html {
        background-color: var(--dark-bg);
        color: #e2e8f0;
    }

    footer,
    .footer {
        display: none !important;
    }

    #main-content {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.4s ease;
        padding: 6rem 2rem 2rem;
    }

    body.sidebar-shrink #main-content {
        margin-left: var(--sidebar-collapsed-width);
    }

    .page-title {
        color: var(--primary-teal);
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 2rem;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-title i {
        font-size: 1.8rem;
    }

    .card-container {
        background: linear-gradient(135deg, var(--card-bg) 0%, #283548 100%);
        border: 1px solid var(--border-color);
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }

    .card-header-custom {
        background: transparent;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--primary-teal);
        margin-bottom: 2rem;
    }

    .card-header-custom h4 {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.4rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Enhanced Table Styles */
    .custom-table {
        background: var(--dark-bg);
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        width: 100%;
    }

    .custom-table thead {
        background: linear-gradient(135deg, #020617 0%, #1e293b 100%);
    }

    .custom-table th {
        background: transparent;
        color: var(--primary-teal);
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 1.25rem 1rem;
        border: none;
        border-bottom: 2px solid var(--primary-teal);
        position: relative;
    }

    .custom-table th::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: var(--primary-teal);
        transition: width 0.3s ease;
    }

    .custom-table th:hover::after {
        width: 100%;
    }

    .custom-table tbody tr {
        background: var(--dark-bg);
        transition: all 0.3s ease;
        border: none;
    }

    .custom-table tbody tr:nth-child(even) {
        background: rgba(30, 41, 59, 0.3);
    }

    .custom-table tbody tr:hover {
        background: var(--hover-bg) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.15);
    }

    .custom-table td {
        background: transparent;
        color: var(--text-secondary);
        padding: 1rem;
        border: none;
        border-bottom: 1px solid rgba(71, 85, 105, 0.3);
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .custom-table td:first-child {
        color: var(--primary-teal);
        font-weight: 600;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-claimed {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
    }

    .status-processing {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .status-pending {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }

    .status-for-release {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
    }

    .status-declined {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    /* Release Mode Badges */
    .release-mode-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(29, 211, 176, 0.1);
        color: var(--primary-teal);
        border: 1px solid rgba(29, 211, 176, 0.3);
    }

    /* Document Type Styling */
    .doc-type {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .doc-type i {
        color: var(--primary-teal);
        font-size: 1.1rem;
    }

    /* School Entity Truncation */
    .school-entity {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    /* Pagination Styles */
    .pagination {
        gap: 0.5rem;
    }

    .pagination .page-item .page-link {
        background: var(--card-bg);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-teal), #0fb8a0);
        color: var(--dark-bg);
        border-color: var(--primary-teal);
        font-weight: 700;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    .pagination .page-item:not(.disabled) .page-link:hover {
        background: var(--hover-bg);
        color: var(--primary-teal);
        border-color: var(--primary-teal);
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--border-color);
        margin-bottom: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #main-content {
            margin-left: 0 !important;
            padding: 6.5rem 1rem 2rem;
        }

        .card-container {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-header-custom h4 {
            font-size: 1.2rem;
        }

        .custom-table th,
        .custom-table td {
            font-size: 0.8rem;
            padding: 0.75rem 0.5rem;
        }

        .school-entity {
            max-width: 100px;
        }
    }

    @media (max-width: 480px) {
        .custom-table th,
        .custom-table td {
            font-size: 0.75rem;
            padding: 0.6rem 0.4rem;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 0.4rem 0.8rem;
        }

        .school-entity {
            max-width: 80px;
        }
    }

    /* Loading Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-table tbody tr {
        animation: fadeInUp 0.6s ease forwards;
    }

    .custom-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
    .custom-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
    .custom-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
    .custom-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
    .custom-table tbody tr:nth-child(5) { animation-delay: 0.5s; }
</style>

<div id="main-content">
    <div class="container-fluid py-5">
        <h1 class="page-title">
            <i class="fas fa-file-text"></i>
            All Requests: {{ $totalCount }}
        </h1>

        <div class="col-12">
            <div class="card-container shadow">
                <div class="card-header-custom">
                    <h4>
                        <i class="fas fa-list-ul"></i>
                        Document Request Management
                    </h4>
                </div>

                @if($DocRequests->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No document requests found</h5>
                        <p>All document requests will appear here once submitted.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm custom-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-user me-1"></i>Claimer</th>
                                    <th><i class="fas fa-user-graduate me-1"></i>Student</th>
                                    <th><i class="fas fa-file-alt me-1"></i>Document</th>
                                    <th><i class="fas fa-school me-1"></i>School</th>
                                    <th><i class="fas fa-truck me-1"></i>Release Mode</th>
                                    <th><i class="fas fa-comment me-1"></i>Remarks</th>
                                    <th><i class="fas fa-flag me-1"></i>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($DocRequests as $item)
                                <tr>
                                    <td><strong>#{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>
                                        @if($item->claimer->full_name !== 'Blank Blank')
                                            {{ $item->claimer->full_name }}
                                        @else
                                            <em class="text-muted">Not claimed</em>
                                        @endif
                                    </td>
                                    <td>{{ $item->studentInformation->full_name }}</td>
                                    <td>
                                        <div class="doc-type">
                                            <i class="fas fa-file-text"></i>
                                            {{ $item->documents->DocType }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="school-entity" title="{{ $item->request_schl_entity }}">
                                            {{ $item->request_schl_entity }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="release-mode-badge">{{ $item->release_mode }}</span>
                                    </td>
                                    <td>
                                        @if($item->remarks)
                                            <div class="school-entity" title="{{ $item->remarks }}">
                                                {{ $item->remarks }}
                                            </div>
                                        @else
                                            <em class="text-muted">No remarks</em>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'Claimed')
                                            <span class="status-badge status-claimed">
                                                <i class="fas fa-check"></i>
                                                Claimed
                                            </span>
                                        @elseif($item->status == 'Processing')
                                            <span class="status-badge status-processing">
                                                <i class="fas fa-spinner"></i>
                                                Processing
                                            </span>
                                        @elseif($item->status == 'Pending')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-hourglass-half"></i>
                                                Pending
                                            </span>
                                        @elseif($item->status == 'For Release')
                                            <span class="status-badge status-for-release">
                                                <i class="fas fa-paper-plane"></i>
                                                For Release
                                            </span>
                                        @else
                                            <span class="status-badge status-declined">
                                                <i class="fas fa-times"></i>
                                                Declined
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $DocRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-shrink');
            });
        }

        // Add smooth scroll effect for table rows
        const tableRows = document.querySelectorAll('.custom-table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(20px)';

            setTimeout(() => {
                row.style.transition = 'all 0.6s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>

@endsection
