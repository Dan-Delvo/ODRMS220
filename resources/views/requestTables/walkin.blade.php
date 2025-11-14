
@extends('layout.blankpage')

@section('content')

<style>
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltip-text {
        visibility: hidden;
        width: 160px;
        background-color: black;
        color: #fff;
        text-align: center;
        padding: 6px;
        border-radius: 4px;

        position: absolute;
        z-index: 1;
        bottom: 125%;
        /* Position above */
        left: 50%;
        transform: translateX(-50%);

        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .required-label::after {
        content: " *";
        color: red;
    }

    .form-check-input:checked {
        background-color: #1dd3b0;
        border-color: #1dd3b0;
    }

    .form-check-label {
        font-weight: 500;
        color: #1f2937;
    }
</style>

@include('layout.partials.message')
<div class="row justify-content-center">
    <div class="col-lg-10 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-lg mt-3">
            <div class="card-header text-white" style="background-color: #1f2937;">
                <h3 class="my-2">Document Request Form</h3>
            </div>
            <div class="card-body p-4">

                <form action="{{ route('walkin.store') }}" method="POST" id="walkinForm">
                    @csrf

                    <!-- Document Request Info -->
                    <h5 class="mb-3">📄 Document Request Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('request_schl_entity') is-invalid @enderror"
                                    id="inputRequestSchlEntity" type="text" name="request_schl_entity"
                                    value="{{ old('request_schl_entity') }}"
                                    placeholder="Enter Requesting School/Entity" required
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Enter the name of the school requesting the document.">
                                <label for="inputRequestSchlEntity" class="required-label">Requesting School/Entity</label>
                                @error('request_schl_entity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('document_id') is-invalid @enderror"
                                    id="inputDocumentId" name="document_id" required>
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

                    <!-- Requesting for Others Section -->
                    <h5 class="mt-4 mb-3">👤 Requester Information</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check">
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
                                    readonly>
                                <label for="inputRelationship" id="relationshipLabel">Name of Requestor</label>
                                @error('relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <h5 class="mt-4 mb-3">👩‍🎓 Student Information</h5>
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
                                    id="inputGradeLevel" name="grade_level" required>
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
                                    id="email_address" type="email" name="email_address"
                                    value="{{ old('email_address') }}"
                                    placeholder="Enter Email Address" required>
                                <label for="email_address" class="required-label">Email Address</label>
                                @error('email_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-4 text-center">
                        <button class="btn btn-lg text-white fw-semibold px-5 py-2 rounded-pill"
                            style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);"
                            id="submitButton"
                            type="button">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

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

        // Requesting for Others Toggle
        const requestingCheckbox = document.getElementById('requestingForOthers');
        const relationshipInput = document.getElementById('inputRelationship');
        const relationshipLabel = document.getElementById('relationshipLabel');

        if (requestingCheckbox && relationshipInput) {
            // Check on page load if checkbox was previously checked (old input)
            if (requestingCheckbox.checked) {
                relationshipInput.disabled = false;
                relationshipInput.readOnly = false;
                relationshipInput.required = true;
                relationshipLabel.classList.add('required-label');
            }

            requestingCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    relationshipInput.disabled = false;
                    relationshipInput.readOnly = false;
                    relationshipInput.required = true;
                    relationshipLabel.classList.add('required-label');
                    relationshipInput.focus();
                } else {
                    relationshipInput.disabled = true;
                    relationshipInput.readOnly = true;
                    relationshipInput.required = false;
                    relationshipInput.value = '';
                    relationshipLabel.classList.remove('required-label');
                    relationshipInput.classList.remove('is-invalid', 'is-valid');
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

            button.disabled = true; // Disable the button to prevent multiple clicks

            Swal.fire({
                title: 'Confirm Submission',
                text: "Are you sure you want to submit this request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1dd3b0',
                cancelButtonColor: '#1f2937',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Submitting...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Submit the form
                    form.submit();
                } else {
                    // If cancelled, re-enable the button
                    button.disabled = false;
                }
            });
        });

    });
</script>
@endsection
