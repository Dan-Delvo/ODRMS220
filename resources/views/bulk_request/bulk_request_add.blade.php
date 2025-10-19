@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --primary-color: #1dd3b0;
        --primary-hover: #17b89a;
        --danger-color: #ef4444;
        --danger-hover: #dc2626;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-control {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.1);
        outline: none;
    }

    .form-control.is-invalid {
        border-color: var(--danger-color);
    }

    .form-control.is-invalid:focus {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .btn-primary-custom {
        background: var(--primary-color);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary-custom:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2.5rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        font-size: 1rem;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--border-color), transparent);
        margin: 2rem 0;
    }

    .input-group-modern {
        display: flex;
        gap: 0.75rem;
        align-items: stretch;
    }

    .input-group-modern input {
        flex: 1;
    }

    .student-table-container {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        height: 400px;
        overflow-y: auto;
    }

    .student-table-container::-webkit-scrollbar {
        width: 8px;
    }

    .student-table-container::-webkit-scrollbar-track {
        background: var(--bg-light);
        border-radius: 10px;
    }

    .student-table-container::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 10px;
    }

    .student-table-container::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    .table-header {
        display: flex;
        padding: 0.75rem 1rem;
        background: var(--bg-light);
        border-radius: 8px;
        margin-bottom: 1rem;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table-header-col {
        flex: 1;
    }

    .table-header-actions {
        width: 100px;
        text-align: right;
    }

    .student-row {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: white;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .student-row:last-child {
        border-bottom: none;
    }

    .student-row:hover {
        background: var(--bg-light);
    }

    .student-name {
        flex: 1;
        font-weight: 500;
        color: var(--text-dark);
    }

    .student-actions {
        width: 100px;
        text-align: right;
    }

    .btn-delete {
        background: white;
        border: 1px solid var(--danger-color);
        color: var(--danger-color);
        border-radius: 8px;
        padding: 0.4rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        background: var(--danger-color);
        color: white;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
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
        opacity: 0.5;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-count {
        background: var(--primary-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row g-2">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Bulk Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('bulk_request.index') }}" class="text-dark">Bulk Dashboard</a></li>
            <li class="breadcrumb-item active">Bulk Requests</li>
        </ol>
    </div>
</div>

<div class="container mt-4">
    <form id="schoolForm" method="POST" action="{{ route('bulk_request_add.store') }}"
        data-swal-loading="true"
        data-swal-title="Accepting Bulk Document Request"
        data-swal-text="This may take a few seconds...">
        @csrf

        <div class="form-card">
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                School Information
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">School Name</label>
                    <input type="text" name="school_name" class="form-control @error('school_name') is-invalid @enderror"
                        placeholder="Enter school name" value="{{ old('school_name') }}" required>
                    @error('school_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="school@example.com" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Students
                <span class="badge-count" id="studentCount">0</span>
            </div>

            <div class="input-group-modern">
                <input type="text" id="studentName" class="form-control" placeholder="Enter student name">
                <button type="button" class="btn btn-primary-custom" id="addStudentBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.25rem;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Student
                </button>
            </div>

            <div class="student-table-container">
                <div class="table-header">
                    <div class="table-header-col">Student Name</div>
                    <div class="table-header-actions">Actions</div>
                </div>
                <div id="studentsList">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <p>No students added yet. Add students using the form above.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden input array to store student names -->
        <div id="studentsInputs"></div>

        <div class="text-center">
            <button type="submit" class="btn btn-success-custom">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Submit Request
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    const students = [];
    const addBtn = document.getElementById('addStudentBtn');
    const studentNameInput = document.getElementById('studentName');
    const hiddenInputs = document.getElementById('studentsInputs');
    const studentCount = document.getElementById('studentCount');

    addBtn.addEventListener('click', addStudent);
    studentNameInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addStudent();
        }
    });

    function addStudent() {
        const name = studentNameInput.value.trim();
        if (name === '') {
            alert('Please enter a student name');
            return;
        }

        students.push(name);
        studentNameInput.value = '';
        studentNameInput.focus();

        updateTable();
        updateHiddenInputs();
        updateCount();
    }

    function updateTable() {
        const listContainer = document.getElementById('studentsList');

        if (students.length === 0) {
            listContainer.innerHTML = `
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <p>No students added yet. Add students using the form above.</p>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = students.map((name, index) => `
            <div class="student-row">
                <div class="student-name">${name}</div>
                <div class="student-actions">
                    <button type="button" class="btn btn-delete" onclick="removeStudent(${index})">
                        Delete
                    </button>
                </div>
            </div>
        `).join('');
    }

    function updateHiddenInputs() {
        hiddenInputs.innerHTML = students.map(name =>
            `<input type="hidden" name="students[]" value="${name}">`
        ).join('');
    }

    function updateCount() {
        studentCount.textContent = students.length;
    }

    function removeStudent(index) {
        students.splice(index, 1);
        updateTable();
        updateHiddenInputs();
        updateCount();
    }
</script>
@endpush