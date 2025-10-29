@extends('layout.blankpage')

@section('content')
@include('layout.partials.swal-loading')
@include('layout.partials.message')

<!-- Page Title and Breadcrumbs -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="mt-4 text-dark"><span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Students Information</span></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark">Students Information List</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;" id="students-total-badge">
                Students Total: {{ $user->total() }}
            </span>
        </h1>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-6">
                            <label for="search" class="form-label fw-bold">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="Search by name, LRN, or grade level..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label fw-bold">Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Default Order</option>
                                <option value="LastName" {{ request('sort_by') == 'LastName' ? 'selected' : '' }}>Last Name</option>
                                <option value="FirstName" {{ request('sort_by') == 'FirstName' ? 'selected' : '' }}>First Name</option>
                                <option value="LRN" {{ request('sort_by') == 'LRN' ? 'selected' : '' }}>LRN</option>
                                <option value="Grade_level" {{ request('sort_by') == 'Grade_level' ? 'selected' : '' }}>Grade Level</option>
                                <option value="Last_sy_attended" {{ request('sort_by') == 'Last_sy_attended' ? 'selected' : '' }}>Last SY Attended</option>
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-3">
                            <label for="sort_order" class="form-label fw-bold">Sort Order</label>
                            <select name="sort_order" id="sort_order" class="form-select">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('sort_order', 'asc') == 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="col-md-12">
                            <button type="submit" class="btn me-2 text-white" style="background-color: #1dd3b0;">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <button type="button" id="resetBtn" class="btn text-white" style="background-color: #1f2937;">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="loading-indicator" style="display: none;">
    <div class="text-center my-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading students...</p>
    </div>
</div>

<!-- Students Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 bg-white text-dark">
            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 60px;">
                <h4 class="mb-0">Students Information</h4>
                <span class="badge bg-light text-dark">
                    Showing {{ $user->firstItem() ?? 0 }} - {{ $user->lastItem() ?? 0 }} of {{ $user->total() }}
                </span>
            </div>

            <div class="card-body" id="table-container">
                @include('maintenance.table', [
                    'items' => $user,
                    'columns' => $tableColumns,
                    'routePrefix' => 'student',
                    'primaryKey' => 'id',
                    'permissions' => [
                        'edit' => $PermissionEdit,
                        'delete' => $PermissionDelete,
                        'info' => $PermissionInfo
                    ],
                    'emptyMessage' => 'No students found matching your search criteria.'
                ])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @include('maintenance.pagination', ['items' => $user])
            </div>
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
        const studentsTotalBadge = document.getElementById('students-total-badge');

        let searchTimer;

        // AJAX function to load students
        function loadStudents(url = null) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            const requestUrl = url || '{{ route('student') }}?' + params.toString();

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
                    studentsTotalBadge.textContent = 'Students Total: ' + data.total;
                    
                    // Hide loading
                    loadingIndicator.style.display = 'none';
                    
                    // Update URL without reload
                    window.history.pushState({}, '', requestUrl);
                    
                    // Re-attach delete button handlers
                    attachDeleteHandlers();
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                loadingIndicator.style.display = 'none';
                tableContainer.style.opacity = '1';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load students. Please try again.'
                });
            });
        }

        // Handle form submission
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadStudents();
        });

        // Auto-submit when sort options change
        sortBy.addEventListener('change', function() {
            loadStudents();
        });

        sortOrder.addEventListener('change', function() {
            loadStudents();
        });

        // Search as you type with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                loadStudents();
            }, 300);
        });

        // Submit on Enter key in search field
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimer);
                loadStudents();
            }
        });

        // Reset button
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            sortBy.value = 'id';
            sortOrder.value = 'asc';
            loadStudents('{{ route('student') }}');
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
                e.preventDefault();
                const link = e.target.matches('.pagination a') ? e.target : e.target.closest('.pagination a');
                const url = link.getAttribute('href');
                if (url) {
                    loadStudents(url);
                }
            }
        });

        // Delete confirmation function
        function attachDeleteHandlers() {
            document.querySelectorAll(".btn-delete").forEach(button => {
                button.addEventListener("click", function(e) {
                    let form = this.closest("form");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "This student record will be permanently deleted!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#1f2937",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }

        // Initial attachment of delete handlers
        attachDeleteHandlers();
    });
</script>

<style>
    .form-label {
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .table td {
        vertical-align: middle;
    }

    #loading-indicator {
        position: relative;
        z-index: 10;
    }
</style>

@endsection