@extends('layout.blankpage')

@section('content')

@include('layout.partials.message')

<div class="container-fluid px-4 py-4">

{{-- Page Header --}}
<div class="page-header-walkin">
    <div>
        <h1><i class="fas fa-walking me-2"></i>Walk-in Document Request</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Walk-in Request</li>
        </ol>
    </div>
</div>

{{-- Main Form Card --}}
<div class="walkin-card">
    <div class="walkin-card-header">
        <div class="header-left">
            <span class="header-icon"><i class="fas fa-file-alt"></i></span>
            <h5>Document Request Form</h5>
        </div>
    </div>
    <div class="walkin-card-body">

        <form action="{{ route('walkin.store') }}" method="POST" id="walkinForm">
            @csrf

            {{-- Document Request Info --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon"><i class="fas fa-file-invoice"></i></span>
                    <h6>Document Request Information</h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('request_schl_entity') is-invalid @enderror"
                                id="inputRequestSchlEntity" type="text" name="request_schl_entity"
                                value="{{ old('request_schl_entity') }}"
                                placeholder="Enter Requesting School/Entity" required
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Enter the type of document to be requested.">
                            <label for="inputRequestSchlEntity" class="required-label">Requesting School/Entity</label>
                            @error('request_schl_entity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('document_id') is-invalid @enderror"
                                id="inputDocumentId" name="document_id" required
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Enter the name of the school requesting the document.">
                                <option value="" disabled selected>Select Document Type</option>
                                @foreach($DocType as $doc)
                                <option value="{{ $doc->id }}" {{ old('document_id') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->DocType }}
                                </option>
                                @endforeach
                            </select>
                            <label for="inputDocumentId" class="required-label">Requested Document</label>
                            @error('document_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control" id="inputReleaseMode" type="text"
                                value="Pickup" name="release_mode" readonly>
                            <label for="inputReleaseMode" class="required-label">Release Mode</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Requesting for Others Section --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon"><i class="fas fa-user-tag"></i></span>
                    <h6>Requester Information</h6>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check walkin-check">
                            <input class="form-check-input" type="checkbox" id="requestingForOthers" name="requesting_for_others" value="1"
                                {{ old('requesting_for_others') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requestingForOthers">
                                <i class="fas fa-user-friends me-1"></i>
                                Requesting for others
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('relationship') is-invalid @enderror"
                                id="inputRelationship"
                                type="text"
                                name="relationship"
                                value="{{ old('relationship') }}"
                                placeholder="Enter Relationship with Student"
                                disabled
                                readonly
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Enter the name of the person making the request.">
                            <label for="inputRelationship" id="relationshipLabel">Name of Requestor</label>
                            @error('relationship')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Student Info --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon"><i class="fas fa-user-graduate"></i></span>
                    <h6>Student Information</h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('student_first_name') is-invalid @enderror"
                                id="inputStudentFirstName" type="text" name="student_first_name"
                                value="{{ old('student_first_name') }}"
                                placeholder="Enter Student's First Name" required>
                            <label for="inputStudentFirstName" class="required-label">First Name</label>
                            @error('student_first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('student_last_name') is-invalid @enderror"
                                id="inputStudentLastName" type="text" name="student_last_name"
                                value="{{ old('student_last_name') }}"
                                placeholder="Enter Student's Last Name" required>
                            <label for="inputStudentLastName" class="required-label">Last Name</label>
                            @error('student_last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('lrn') is-invalid @enderror"
                                id="inputLRN" type="text" name="lrn"
                                value="{{ old('lrn') }}"
                                placeholder="Enter LRN">
                            <label for="inputLRN">LRN (Optional)</label>
                            @error('lrn')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="lrn-error-desktop" class="text-danger mt-1" style="font-size: 0.875rem; display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>LRN must be exactly 12 digits
                            </div>
                            <div id="lrn-success-desktop" class="text-success mt-1" style="font-size: 0.875rem; display: none;">
                                <i class="fas fa-check-circle me-1"></i>Valid LRN
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('grade_level') is-invalid @enderror"
                                id="inputGradeLevel" name="grade_level" required
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Enter the grade level or the last level attended.">
                                <option value="" disabled selected>Select Grade Level</option>
                                @foreach($grade as $g)
                                <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                            <label for="inputGradeLevel" class="required-label">Grade Level</label>
                            @error('grade_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('student_status') is-invalid @enderror"
                                id="inputStudentStatus" name="student_status" required>
                                <option value="" disabled selected>Select Student Status</option>
                                @foreach($stat as $s)
                                <option value="{{ $s }}" {{ old('student_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            <label for="inputStudentStatus" class="required-label">Student Status</label>
                            @error('student_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('last_sy_attended') is-invalid @enderror"
                                id="inputLastSYAttended" type="text" name="last_sy_attended"
                                value="{{ old('last_sy_attended') }}"
                                placeholder="Enter Last SY Attended" required>
                            <label for="inputLastSYAttended" class="required-label">Last School Year Attended</label>
                            @error('last_sy_attended')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <input class="form-control @error('email_address') is-invalid @enderror"
                                id="email_address"
                                type="email"
                                name="email_address"
                                value="{{ old('email_address') }}"
                                placeholder="Enter Email Address"
                                required>
                            <label for="email_address" class="required-label" id="emailLabel">Email Address</label>
                            @error('email_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" id="emailHelp">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="emailHelpText">Required for account creation and notifications</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-4 text-center">
                <button class="btn btn-submit-walkin"
                    id="submitButton"
                    type="button">
                    <i class="fas fa-paper-plane me-2"></i>Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

</div> {{-- close container-fluid --}}

<style>
    /* ===== CORE VARIABLES ===== */
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* ===== PAGE HEADER ===== */
    .page-header-walkin {
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
    .page-header-walkin h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }
    .page-header-walkin .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }
    .page-header-walkin .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
    }
    .page-header-walkin .breadcrumb-item.active {
        color: #d1d5db;
    }

    /* ===== CARD ===== */
    .walkin-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
    }
    .walkin-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    /* ===== CARD HEADER ===== */
    .walkin-card-header {
        background: var(--primary-dark);
        color: white;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .header-icon {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }
    .header-left h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* ===== CARD BODY ===== */
    .walkin-card-body {
        padding: 2rem;
    }

    /* ===== FORM SECTIONS ===== */
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .section-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }
    .section-icon {
        background: rgba(29, 211, 176, 0.1);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        color: var(--primary-green);
    }
    .section-title h6 {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
        color: var(--primary-dark);
    }

    /* ===== FORM CONTROLS ===== */
    .form-floating > .form-control,
    .form-floating > .form-select {
        border-radius: 10px;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
    }
    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }
    .form-floating > label {
        color: #6b7280;
    }
    .required-label::after {
        content: " *";
        color: #ef4444;
        font-weight: 600;
    }

    /* ===== CHECKBOX ===== */
    .walkin-check {
        background: rgba(29, 211, 176, 0.06);
        border: 1px solid rgba(29, 211, 176, 0.15);
        border-radius: 10px;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
    }
    .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        border-color: var(--primary-green);
    }
    .form-check-label {
        font-weight: 500;
        color: var(--primary-dark);
    }

    /* ===== SUBMIT BUTTON ===== */
    .btn-submit-walkin {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2.5rem;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.025em;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.35);
    }
    .btn-submit-walkin:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(29, 211, 176, 0.5);
        color: white;
    }
    .btn-submit-walkin:active {
        transform: translateY(0);
    }
    .btn-submit-walkin:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
    }

    /* ===== VALIDATION ===== */
    .form-control.is-valid {
        border-color: var(--primary-green);
    }
    .form-control.is-valid:focus {
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }

    /* ===== TOOLTIP FIX ===== */
    .tooltip-inner {
        background-color: var(--primary-dark);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.8rem;
    }
    .tooltip .tooltip-arrow::before {
        border-top-color: var(--primary-dark);
    }

    /* ===== SMOOTH TRANSITIONS ===== */
    .btn, .form-control, .form-select {
        transition: all 0.2s ease-in-out;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 767px) {
        .page-header-walkin {
            padding: 1.25rem;
            border-radius: 12px;
            flex-direction: column;
            align-items: flex-start;
        }
        .page-header-walkin h1 { font-size: 1.35rem; }
        .walkin-card { border-radius: 12px; }
        .walkin-card-header { padding: 0.875rem 1rem; }
        .walkin-card-body { padding: 1.25rem; }
        .form-section { margin-bottom: 1.5rem; padding-bottom: 1rem; }
    }
    @media (max-width: 575px) {
        .page-header-walkin h1 { font-size: 1.15rem; }
        .walkin-card-body { padding: 1rem; }
        .btn-submit-walkin { width: 100%; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // LRN Validation
        const errorLrnMessage = document.getElementById('lrn-error-desktop')
        const successLrnMessage = document.getElementById('lrn-success-desktop')
        const lrnInput = document.getElementById('inputLRN')
        const submitLrn = document.getElementById('submitButton')

        if (lrnInput) {
            lrnInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');

                // Limit input to 12 digits
                if (this.value.length > 12) {
                    this.value = this.value.slice(0, 12);
                }
                const value = this.value
                const isValid = /^\d{12}$/.test(value);

                if (value.length === 0) {
                    errorLrnMessage.style.display = 'none'
                    successLrnMessage.style.display = 'none'
                    lrnInput.classList.remove('is-invalid', 'is-valid');
                    submitLrn.disabled = false;
                } else if (isValid) {
                    errorLrnMessage.style.display = 'none'
                    successLrnMessage.style.display = 'block'
                    lrnInput.classList.remove('is-invalid');
                    lrnInput.classList.add('is-valid');
                    submitLrn.disabled = false;
                } else {
                    errorLrnMessage.style.display = 'block'
                    successLrnMessage.style.display = 'none'
                    lrnInput.classList.remove('is-valid');
                    lrnInput.classList.add('is-invalid');
                    submitLrn.disabled = true;
                }
            });

            const form = lrnInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const value = lrnInput.value;
                    if (value.length > 0 && !/^\d{12}$/.test(value)) {
                        e.preventDefault();
                        errorLrnMessage.style.display = 'block'
                        lrnInput.classList.add('is-invalid');
                        lrnInput.focus();
                    }
                })
            }
        }

        // Requesting for Others Toggle + Email Toggle
        const requestingCheckbox = document.getElementById('requestingForOthers');
        const relationshipInput = document.getElementById('inputRelationship');
        const relationshipLabel = document.getElementById('relationshipLabel');
        const emailInput = document.getElementById('email_address');
        const emailLabel = document.getElementById('emailLabel');
        const emailHelpText = document.getElementById('emailHelpText');

        if (requestingCheckbox && relationshipInput && emailInput) {
            // Check on page load if checkbox was previously checked (old input)
            if (requestingCheckbox.checked) {
                // Enable relationship field
                relationshipInput.disabled = false;
                relationshipInput.readOnly = false;
                relationshipInput.required = true;
                relationshipLabel.classList.add('required-label');

                  // Email is optional for guest requests
                  emailInput.required = false;
                emailLabel.classList.remove('required-label');
                emailHelpText.textContent = 'Optional for guest requests; provide one to receive status notifications';
            }

            requestingCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // GUEST REQUEST MODE
                    // Enable relationship field
                    relationshipInput.disabled = false;
                    relationshipInput.readOnly = false;
                    relationshipInput.required = true;
                    relationshipLabel.classList.add('required-label');
                    relationshipInput.focus();

                      // Email is optional for guest requests
                      emailInput.required = false;
                    emailLabel.classList.remove('required-label');
                    emailHelpText.textContent = 'Optional for guest requests; provide one to receive status notifications';
                    emailInput.classList.remove('is-invalid');
                } else {
                    // SELF REQUEST MODE
                    // Disable relationship field
                    relationshipInput.disabled = true;
                    relationshipInput.readOnly = true;
                    relationshipInput.required = false;
                    relationshipInput.value = '';
                    relationshipLabel.classList.remove('required-label');
                    relationshipInput.classList.remove('is-invalid', 'is-valid');

                    // Make email required
                    emailInput.required = true;
                    emailLabel.classList.add('required-label');
                    emailHelpText.textContent = 'Required for account creation and notifications';
                }
            });
        }

        // Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Form Submission with additional validation
        document.getElementById('submitButton').addEventListener('click', function() {
            const button = this;
            const form = document.getElementById('walkinForm');

            // Check if relationship is required but empty
            if (requestingCheckbox.checked && !relationshipInput.value.trim()) {
                relationshipInput.classList.add('is-invalid');
                relationshipInput.focus();
                return;
            }

            // Check form validity
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            button.disabled = true;

            // Custom confirmation message based on request type
            const isGuestRequest = requestingCheckbox.checked;
            const confirmText = isGuestRequest
                ? "You are submitting a request on behalf of the student. No account will be created."
                : "Are you sure you want to submit this request?";

            Swal.fire({
                title: 'Confirm Submission',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1dd3b0',
                cancelButtonColor: '#1f2937',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                } else {
                    button.disabled = false;
                }
            });
        });

    });
</script>
@endsection
