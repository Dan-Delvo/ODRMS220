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

    /* ===== Main Card ===== */
    .bulk-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
        transition: box-shadow 0.3s ease;
    }
    .bulk-card:hover {
        box-shadow: var(--shadow-hover);
    }
    .bulk-card-header {
        background: var(--primary-dark);
        padding: 1.1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .bulk-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .bulk-card-header .header-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary-green), #17b89a);
        color: #fff;
        font-size: 1rem;
    }
    .bulk-card-header h5 {
        color: #fff;
        margin: 0;
        font-size: 1.05rem;
        font-weight: 600;
    }
    .bulk-card-body {
        padding: 1.5rem 2rem 2rem;
    }

    /* ===== Form Sections ===== */
    .form-section {
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
    }
    .form-section .section-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1rem;
        font-size: 1rem;
        font-weight: 600;
    }
    .form-section .section-title h6 {
        margin: 0;
        font-weight: 700;
        font-size: 1rem;
        color: #1f2937;
    }
    .section-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(29, 211, 176, 0.15);
        color: var(--primary-green);
        font-size: 0.85rem;
    }

    /* ===== Form Controls ===== */
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
        font-size: 0.85rem;
    }
    .form-control {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 0.6rem 0.9rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        font-size: 0.9rem;
    }
    .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
        outline: none;
    }
    .form-control.is-invalid {
        border-color: #ef4444;
    }
    .form-control.is-invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    .invalid-feedback {
        display: block;
        color: #ef4444;
        font-size: 0.82rem;
        margin-top: 0.35rem;
        font-weight: 500;
    }

    /* ===== Student Add Row ===== */
    .input-group-modern {
        display: flex;
        gap: 0.75rem;
        align-items: stretch;
    }
    .input-group-modern input {
        flex: 1;
    }
    .btn-add-student {
        background: linear-gradient(135deg, var(--primary-green), #17b89a);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.3rem;
        font-weight: 600;
        color: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        white-space: nowrap;
    }
    .btn-add-student:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
        color: #fff;
    }

    /* ===== Student Table ===== */
    .student-table-container {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem;
        margin-top: 1.25rem;
        height: 380px;
        overflow-y: auto;
    }
    .student-table-container::-webkit-scrollbar {
        width: 6px;
    }
    .student-table-container::-webkit-scrollbar-track {
        background: transparent;
    }
    .student-table-container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .student-table-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    .table-header {
        display: flex;
        padding: 0.65rem 1rem;
        background: #f3f4f6;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        font-weight: 600;
        font-size: 0.8rem;
        color: #6b7280;
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
        padding: 0.85rem 1rem;
        background: #fff;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s ease;
    }
    .student-row:last-child {
        border-bottom: none;
    }
    .student-row:hover {
        background: #f9fafb;
    }
    .student-name {
        flex: 1;
        font-weight: 500;
        color: #1f2937;
    }
    .student-actions {
        width: 100px;
        text-align: right;
    }
    .btn-delete {
        background: #fff;
        border: 1px solid #ef4444;
        color: #ef4444;
        border-radius: 8px;
        padding: 0.35rem 0.9rem;
        font-size: 0.82rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-delete:hover {
        background: #ef4444;
        color: #fff;
        transform: translateY(-1px);
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }
    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        opacity: 0.4;
    }
    .empty-state p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* ===== Badge Count ===== */
    .badge-count {
        background: rgba(29, 211, 176, 0.15);
        color: var(--primary-green);
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    /* ===== Submit Button ===== */
    .btn-submit-bulk {
        background: linear-gradient(135deg, var(--primary-green), #17b89a);
        border: none;
        border-radius: 12px;
        padding: 0.85rem 2.5rem;
        font-weight: 600;
        color: #fff;
        font-size: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.25);
    }
    .btn-submit-bulk:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(29, 211, 176, 0.35);
        color: #fff;
    }

    /* ===== Responsive ===== */
    @media (max-width: 767px) {
        .page-header-bulk {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.2rem 1.25rem;
        }
        .page-header-bulk h1 {
            font-size: 1.15rem;
        }
        .bulk-card-body {
            padding: 1.25rem;
        }
        .input-group-modern {
            flex-direction: column;
        }
        .btn-add-student {
            width: 100%;
            text-align: center;
        }
    }
    @media (max-width: 575px) {
        .bulk-card-header {
            padding: 0.9rem 1rem;
        }
        .bulk-card-body {
            padding: 1rem;
        }
        .btn-submit-bulk {
            width: 100%;
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
        <h1><i class="fas fa-layer-group me-2"></i>Bulk Document Request</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('bulk_request.index') }}">Bulk Dashboard</a></li>
            <li class="breadcrumb-item active">Add Bulk Request</li>
        </ol>
    </div>
</div>

{{-- Main Form Card --}}
<div class="bulk-card">
    <div class="bulk-card-header">
        <div class="header-left">
            <span class="header-icon"><i class="fas fa-file-alt"></i></span>
            <h5>Bulk Request Form</h5>
        </div>
    </div>
    <div class="bulk-card-body">
        <form id="schoolForm" method="POST" action="{{ route('bulk_request_add.store') }}"
            data-swal-loading="true"
            data-swal-title="Accepting Bulk Document Request"
            data-swal-text="This may take a few seconds...">
            @csrf

            {{-- School Information Section --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon"><i class="fas fa-school"></i></span>
                    <h6>School Information</h6>
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

            {{-- Students Section --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon"><i class="fas fa-users"></i></span>
                    <h6>Students</h6>
                    <span class="badge-count" id="studentCount">0</span>
                </div>

                <div class="input-group-modern">
                    <input type="text" id="studentName" class="form-control" placeholder="Enter student name">
                    <button type="button" class="btn btn-add-student" id="addStudentBtn">
                        <i class="fas fa-plus me-1"></i> Add Student
                    </button>
                </div>

                <div class="student-table-container">
                    <div class="table-header">
                        <div class="table-header-col">Student Name</div>
                        <div class="table-header-actions">Actions</div>
                    </div>
                    <div id="studentsList">
                        <div class="empty-state">
                            <i class="fas fa-user-friends"></i>
                            <p>No students added yet. Add students using the form above.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden input array to store student names -->
            <div id="studentsInputs"></div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-submit-bulk">
                    <i class="fas fa-check me-2"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

</div> {{-- close container-fluid --}}

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
                    <i class="fas fa-user-friends"></i>
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
