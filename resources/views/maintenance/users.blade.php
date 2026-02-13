@extends('layout.blankpage')

@section ('content')

@include('layout.partials.message')

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-users {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header-users h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-users .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-users .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-users .breadcrumb-item.active {
        color: #d1d5db;
    }

    .total-counter {
        background: rgba(29, 211, 176, 0.15);
        border: 1px solid rgba(29, 211, 176, 0.3);
        border-radius: 12px;
        padding: 0.5rem 1.25rem;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .total-counter span {
        color: #1dd3b0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .users-filter-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .users-filter-header {
        background: var(--primary-dark);
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .users-filter-header .filter-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .users-filter-header h6 {
        font-size: 0.875rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .users-filter-body {
        padding: 1.25rem 1.5rem;
    }

    .users-filter-body .form-label {
        margin-bottom: 0.4rem;
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .users-filter-body .form-control,
    .users-filter-body .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .users-filter-body .form-control:focus,
    .users-filter-body .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .users-filter-body .input-group-text {
        border-radius: 10px 0 0 10px;
        background: var(--primary-dark);
        color: white;
        border: 1px solid var(--primary-dark);
    }

    .users-filter-body .input-group .form-control {
        border-radius: 0;
    }

    .users-filter-body .input-group .btn-outline-secondary {
        border-radius: 0 10px 10px 0;
        border-color: #e2e8f0;
    }

    .btn-reset-users {
        background: var(--primary-dark);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-reset-users:hover {
        background: #374151;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(31, 41, 55, 0.3);
        color: white;
    }

    .users-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .users-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .users-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .users-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .users-card-header .header-icon {
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

    .users-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .btn-add-user {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-add-user:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .users-card-body {
        padding: 1.5rem;
    }

    .table-users {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .table-users thead th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border: none;
    }

    .table-users tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-users tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }

    .table-users tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
    }

    .table-users .btn {
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .table-users .btn:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .table-users .btn-success {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
    }

    .table-users .btn-danger:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .users-card-body .table {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .users-card-body .table thead th {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 1rem;
        border: none;
    }

    .users-card-body .table tbody tr {
        transition: background-color 0.15s ease;
    }

    .users-card-body .table tbody tr:hover {
        background-color: rgba(29, 211, 176, 0.06);
    }

    .users-card-body .table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        color: #374151;
    }

    .users-card-body .table .btn {
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .users-card-body .table .btn:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .users-card-body .table .btn-success {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
    }

    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
    }

    #loading-indicator {
        position: relative;
        z-index: 10;
    }

    #loading-indicator .spinner-border {
        color: var(--primary-green) !important;
    }

    /* ===== Tablet (max-width: 991px) ===== */
    @media (max-width: 991px) {
        .container-fluid.px-4 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .users-filter-body .row.g-3 > .col-md-5 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .users-filter-body .row.g-3 > .col-md-3,
        .users-filter-body .row.g-3 > .col-md-2 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }

    /* ===== Mobile (max-width: 767px) ===== */
    @media (max-width: 767px) {
        .container-fluid.px-4 {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 1rem !important;
        }

        /* Page Header */
        .page-header-users {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.25rem;
            border-radius: 12px;
            gap: 0.75rem;
        }

        .page-header-users h1 {
            font-size: 1.35rem;
        }

        .total-counter {
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
            align-self: flex-start;
        }

        .total-counter span {
            font-size: 1.1rem;
        }

        /* Filter Card */
        .users-filter-card,
        .users-card {
            border-radius: 12px;
        }

        .users-filter-header {
            padding: 0.625rem 1rem;
        }

        .users-filter-body {
            padding: 1rem;
        }

        .users-filter-body .row.g-3 > [class*="col-md"] {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .users-filter-body .form-label {
            font-size: 0.75rem;
        }

        .users-filter-body .form-control,
        .users-filter-body .form-select {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }

        .btn-reset-users {
            width: 100%;
        }

        /* Users Card */
        .users-card-header {
            padding: 0.875rem 1.25rem;
        }

        .users-card-body {
            padding: 0.75rem;
        }

        /* Table */
        .users-card-body .table-responsive {
            margin: 0 -0.75rem;
            padding: 0 0.75rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .users-card-body .table,
        .table-users {
            font-size: 0.8rem;
            min-width: 600px;
        }

        .users-card-body .table thead th,
        .table-users thead th {
            font-size: 0.7rem;
            padding: 0.6rem 0.6rem;
            white-space: nowrap;
        }

        .users-card-body .table tbody td,
        .table-users tbody td {
            padding: 0.55rem 0.6rem;
        }

        .users-card-body .table .btn,
        .table-users .btn {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }

        .btn-add-user {
            font-size: 0.8rem;
            padding: 0.45rem 1rem;
        }

        /* Pagination */
        #pagination-container {
            padding: 0.75rem;
        }

        #pagination-container .pagination {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.25rem;
        }

        #pagination-container .pagination .page-link {
            padding: 0.35rem 0.6rem;
            font-size: 0.75rem;
        }
    }

    /* ===== Small Mobile (max-width: 575px) ===== */
    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-users {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-users h1 {
            font-size: 1.15rem;
        }

        .page-header-users .breadcrumb {
            font-size: 0.75rem;
        }

        .total-counter {
            font-size: 0.8rem;
            padding: 0.35rem 0.85rem;
        }

        .total-counter span {
            font-size: 1rem;
        }

        /* Filter */
        .users-filter-card {
            margin-bottom: 1rem;
        }

        .users-filter-header {
            padding: 0.5rem 0.875rem;
        }

        .users-filter-header .filter-icon {
            width: 24px;
            height: 24px;
            font-size: 0.65rem;
        }

        .users-filter-header h6 {
            font-size: 0.8rem;
        }

        .users-filter-body {
            padding: 0.875rem;
        }

        /* Card Header */
        .users-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .users-card-header .header-left {
            justify-content: center;
        }

        .users-card-header .header-icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .users-card-header h5 {
            font-size: 0.875rem;
        }

        .btn-add-user {
            text-align: center;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: block;
        }

        /* Card Body */
        .users-card-body {
            padding: 0.5rem;
        }

        .users-card-body .table-responsive {
            margin: 0 -0.5rem;
            padding: 0 0.5rem;
        }

        .users-card-body .table,
        .table-users {
            font-size: 0.75rem;
            min-width: 520px;
        }

        .users-card-body .table thead th,
        .table-users thead th {
            font-size: 0.65rem;
            padding: 0.5rem 0.5rem;
        }

        .users-card-body .table tbody td,
        .table-users tbody td {
            padding: 0.5rem 0.5rem;
        }

        .users-card-body .table .btn,
        .table-users .btn {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }

        /* Action buttons stacked on xs */
        .users-card-body .table td .d-flex,
        .users-card-body .table td.d-flex {
            flex-direction: column;
            gap: 0.3rem;
        }

        .users-card-body .table td .d-flex .me-2,
        .users-card-body .table td.d-flex .me-2 {
            margin-right: 0 !important;
        }

        /* Alert */
        .users-card-body .alert {
            font-size: 0.8rem;
            padding: 0.75rem;
        }

        /* Loading */
        #loading-indicator p {
            font-size: 0.8rem;
        }

        /* Pagination */
        #pagination-container {
            padding: 0.5rem;
        }

        #pagination-container small {
            font-size: 0.7rem;
        }
    }

    /* ===== Extra Small (max-width: 400px) ===== */
    @media (max-width: 400px) {
        .page-header-users h1 {
            font-size: 1rem;
        }

        .users-filter-body .input-group {
            flex-wrap: nowrap;
        }

        .users-card-body .table,
        .table-users {
            min-width: 480px;
        }

        #pagination-container .pagination .page-link {
            padding: 0.25rem 0.45rem;
            font-size: 0.7rem;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-users">
        <div>
            <h1>Users</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Users List</li>
            </ol>
        </div>
        <div class="total-counter" id="users-total-badge">
            Total: <span>{{ $user->total() }}</span>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="users-filter-card">
        <div class="users-filter-header">
            <span class="filter-icon"><i class="fas fa-filter"></i></span>
            <h6>Search &amp; Filter</h6>
        </div>
        <div class="users-filter-body">
            <form id="filterForm">
                <div class="row g-3 align-items-end">
                    <!-- Search Input -->
                    <div class="col-md-5">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search by name, email, username, or account ID..."
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select name="sort_by" id="sort_by" class="form-select">
                            <option value="user_account_id" {{ request('sort_by') == 'user_account_id' ? 'selected' : '' }}>Account ID</option>
                            <option value="username" {{ request('sort_by') == 'username' ? 'selected' : '' }}>Username</option>
                            <option value="email_address" {{ request('sort_by') == 'email_address' ? 'selected' : '' }}>Email</option>
                            <option value="role_id" {{ request('sort_by') == 'role_id' ? 'selected' : '' }}>Role</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-2">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <select name="sort_order" id="sort_order" class="form-select">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_order', 'asc') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-2">
                        <button type="button" id="resetBtn" class="btn btn-reset-users w-100">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" style="display: none;">
        <div class="text-center my-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2" style="color: var(--primary-dark); font-weight: 500;">Loading users...</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="users-card">
        <div class="users-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-users"></i></span>
                <h5>Users</h5>
            </div>
            <a href="{{ route('userStud.add') }}" class="btn-add-user">
                <i class="fas fa-plus me-1"></i> Add User
            </a>
        </div>

        <div class="users-card-body" id="table-container">
            @include('maintenance.table', [
                'items' => $user,
                'columns' => $tableColumns,
                'routePrefix' => 'user',
                'primaryKey' => 'user_account_id',
                'permissions' => [
                    'edit' => $PermissionEdit,
                    'delete' => $PermissionDelete,
                    'info' => $PermissionInfo
                ],
                'emptyMessage' => 'No users found matching your search criteria.'
            ])
        </div>

        <!-- Pagination Container -->
        <div id="pagination-container">
            @include('maintenance.pagination', ['items' => $user])
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('search');
        const sortBy = document.getElementById('sort_by');
        const sortOrder = document.getElementById('sort_order');
        const resetBtn = document.getElementById('resetBtn');
        const tableContainer = document.getElementById('table-container');
        const paginationContainer = document.getElementById('pagination-container');
        const loadingIndicator = document.getElementById('loading-indicator');
        const usersTotalBadge = document.getElementById('users-total-badge');
        const clearSearch = document.getElementById('clearSearch');

        let searchTimer;

        searchInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                clearSearch.style.display = 'block';
            } else {
                clearSearch.style.display = 'none';
            }
        });

        clearSearch.addEventListener('click', function () {
            searchInput.value = '';
            clearSearch.style.display = 'none';
            sortBy.value = 'id';
            sortOrder.value = 'asc';
            loadUsers('{{ route('user') }}');
        });

        // Show ❌ button if the input is pre-filled from request()
        if (searchInput.value.trim().length > 0) {
            clearSearch.style.display = 'block';
        }

        // AJAX function to load users
        function loadUsers(url = null) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            const requestUrl = url || '{{ route('user') }}?' + params.toString();

            // Show loading indicator
            loadingIndicator.style.display = 'block';
            tableContainer.style.opacity = '0.5';

            fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update table
                    tableContainer.innerHTML = data.html;
                    tableContainer.style.opacity = '1';

                    // Update pagination
                    paginationContainer.innerHTML = data.pagination;

                    // Update total count
                    usersTotalBadge.innerHTML = 'Total: <span>' + data.total + '</span>';

                    // Hide loading
                    loadingIndicator.style.display = 'none';

                    // Update URL without reload
                    window.history.pushState({}, '', requestUrl);

                    // Re-attach delete button handlers
                    attachDeleteHandlers();
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                loadingIndicator.style.display = 'none';
                tableContainer.style.opacity = '1';

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load users. Please try again.'
                });
            });
        }

        // Handle form submission
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadUsers();
        });

        // Auto-submit when sort options change
        sortBy.addEventListener('change', function() {
            loadUsers();
        });

        sortOrder.addEventListener('change', function() {
            loadUsers();
        });

        // Search as you type with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                loadUsers();
            }, 300);
        });

        // Submit on Enter key in search field
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimer);
                loadUsers();
            }
        });

        // Reset button
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            sortBy.value = 'user_account_id';
            sortOrder.value = 'asc';
            loadUsers('{{ route('user') }}');
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
                e.preventDefault();
                const link = e.target.matches('.pagination a') ? e.target : e.target.closest('.pagination a');
                const url = link.getAttribute('href');
                if (url) {
                    loadUsers(url);
                }
            }
        });

        // Delete confirmation function using event delegation
        function attachDeleteHandlers() {
            document.querySelectorAll(".btn-delete").forEach(button => {
                button.addEventListener("click", function(e) {
                    let form = this.closest("form");

                    // First confirmation
                    Swal.fire({
                        title: "Are you sure?",
                        text: "The user accounts connected to this role will also be deleted",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#1f2937",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Second confirmation
                            Swal.fire({
                                title: "Final Confirmation",
                                text: "This action cannot be undone!",
                                icon: "error",
                                showCancelButton: true,
                                confirmButtonColor: "#d33",
                                cancelButtonColor: "#1f2937",
                                confirmButtonText: "Yes, I understand",
                                cancelButtonText: "Cancel"
                            }).then((finalResult) => {
                                if (finalResult.isConfirmed) {
                                    form.submit();
                                }
                            });
                        }
                    });
                });
            });
        }

        // Initial attachment of delete handlers
        attachDeleteHandlers();
    });
</script>



@endsection
