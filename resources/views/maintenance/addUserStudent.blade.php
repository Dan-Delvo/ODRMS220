@extends('layout.blankpage')

@section('content')

@include('layout.partials.message')

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header-add">
        <h1>Create Account</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('users') }}">Users List</a></li>
            <li class="breadcrumb-item active">Create Account</li>
        </ol>
    </div>

    <!-- Add User Card -->
    <div class="add-card">
        <div class="add-card-header">
            <div class="header-left">
                <span class="header-icon"><i class="fas fa-user-plus"></i></span>
                <h5>Create Account</h5>
            </div>
        </div>
        <div class="add-card-body">

                <form action="{{ route('account.otp') }}" method="POST"
                    data-swal-loading="true"
                    data-swal-title="Adding Users"
                    data-swal-text="This may take a few seconds...">
                    @csrf

                    <!-- ================= PERSONAL INFORMATION ================= -->
                    <h4 class="mb-3 text-dark">Personal Information</h4>

                    <div class="row g-3 mb-3">
                        <!-- First Name -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control @error('FirstName') is-invalid @enderror"
                                    value="{{ old('FirstName') }}"
                                    id="inputFirstName" type="text" name="FirstName"
                                    placeholder="Enter your first name" required>
                                <label for="inputFirstName">First Name</label>
                                @error('FirstName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Middle Name -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control @error('MiddleName') is-invalid @enderror"
                                    value="{{ old('MiddleName') }}"
                                    id="inputMiddleName" type="text" name="MiddleName"
                                    placeholder="Enter your middle name">
                                <label for="inputMiddleName">Middle Name (Optional)</label>
                                @error('MiddleName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control @error('LastName') is-invalid @enderror"
                                    value="{{ old('LastName') }}"
                                    id="inputLastName" type="text" name="LastName"
                                    placeholder="Enter your last name" required>
                                <label for="inputLastName">Last Name</label>
                                @error('LastName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Suffix -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input class="form-control @error('Suffix') is-invalid @enderror"
                                    value="{{ old('Suffix') }}"
                                    id="inputSuffix" type="text" name="Suffix"
                                    placeholder="Suffix (Optional)">
                                <label for="inputSuffix">Suffix (Optional)</label>
                                @error('Suffix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select @error('role') is-invalid @enderror"
                                    id="role" name="role" onchange="toggleStudentFields()" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                                    @foreach ($role as $roles)
                                    <option value="{{ $roles->id }}"
                                        {{ old('role') == $roles->id ? 'selected' : '' }}>
                                        {{ $roles->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="role">Role</label>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- LRN -->
                        <div class="col-md-5" id="lrnField">
                            <div class="form-floating">
                                <input class="form-control @error('LRN') is-invalid @enderror"
                                    value="{{ old('LRN') }}"
                                    id="inputLRN" type="text" name="LRN"
                                    placeholder="Enter your LRN" maxlength="12">
                                <label for="inputLRN">LRN (12-digit)</label>
                                @error('LRN') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <!-- LRN Validation Rules -->
                            <div id="lrnRules" class="mt-2 d-none">
                                <small id="ruleLrnLength" class="d-block text-danger">✖ Must be exactly 12 digits</small>
                                <small id="ruleLrnNumeric" class="d-block text-danger">✖ Digits only (0–9)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Grade Level -->
                        <div class="col-md-4" id="gradeLevelField">
                            <div class="form-floating">
                                <select class="form-select @error('Grade_level') is-invalid @enderror"
                                    id="grade_level" name="Grade_level">
                                    <option value="" disabled {{ old('Grade_level') ? '' : 'selected' }}>Select Grade Level</option>
                                    @foreach ($grade as $level)
                                    <option value="{{ $level }}" @selected(old('Grade_level')==$level)>
                                        {{ $level }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="grade_level">Grade Level</label>
                                @error('Grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Student Status -->
                        <div class="col-md-4" id="stdStatusField">
                            <div class="form-floating">
                                <select class="form-select @error('Std_status') is-invalid @enderror"
                                    id="inputStdStatus" name="Std_status">
                                    <option value="" disabled {{ old('Std_status') ? '' : 'selected' }}>Select Status</option>
                                    @foreach ($stat as $status)
                                    <option value="{{ $status }}" @selected(old('Std_status')==$status)>
                                        {{ $status }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="inputStdStatus">Student Status</label>
                                @error('Std_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Last SY Attended -->
                        <div class="col-md-4" id="lastSyField">
                            <div class="form-floating">
                                <input class="form-control @error('Last_sy_attended') is-invalid @enderror"
                                    value="{{ old('Last_sy_attended') }}"
                                    id="inputLastSYAttended" type="text" name="Last_sy_attended"
                                    placeholder="Last School Year Attended">
                                <label for="inputLastSYAttended">Last SY Attended</label>
                                @error('Last_sy_attended') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- ================= ACCOUNT INFORMATION ================= -->
                    <h4 class="mb-3 text-dark">Account Information</h4>

                    <div class="row g-3 mb-3">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('email_address') is-invalid @enderror"
                                    value="{{ old('email_address') }}"
                                    id="inputEmail" type="email" name="email_address"
                                    placeholder="name@example.com" required>
                                <label for="inputEmail">Email Address</label>
                                @error('email_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username') }}"
                                    id="inputUsername" type="text" name="username"
                                    placeholder="Enter your username" required>
                                <label for="inputUsername">Username</label>
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Password -->
                        <div class="col-md-6 position-relative">
                            <div class="form-floating">
                                <input class="form-control" id="inputPassword" type="password" name="password"
                                    placeholder="Create a password" required>
                                <label for="inputPassword">Password</label>
                                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3"
                                    id="togglePassword" style="cursor: pointer;"></i>
                            </div>
                            <!-- Password Rules (Below password field) -->
                            <div id="passwordRules" class="mt-2 d-none">
                                <small id="ruleLength" class="d-block text-danger">✖ 8–20 characters</small>
                                <small id="ruleLetter" class="d-block text-danger">✖ Contains a letter</small>
                                <small id="ruleNumber" class="d-block text-danger">✖ Contains a number</small>
                                <small id="ruleSpecial" class="d-block text-danger">✖ Contains a special character</small>
                                <small id="ruleNoSpaces" class="d-block text-danger">✖ No spaces allowed</small>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 position-relative">
                            <div class="form-floating">
                                <input class="form-control" id="inputPasswordConfirm" type="password"
                                    name="password_confirmation" placeholder="Confirm password" required>
                                <label for="inputPasswordConfirm">Confirm Password</label>
                                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3"
                                    id="togglePasswordConfirm" style="cursor: pointer;"></i>
                            </div>
                            <!-- Password Match Message (Below confirm password field) -->
                            <div class="mt-2">
                                <small id="passwordMatchMessage" class="d-block text-danger d-none">✖ Passwords do not match</small>
                            </div>
                        </div>
                    </div>

                    <!-- ================= ACTION BUTTONS ================= -->
                    <div class="d-flex justify-content-between flex-wrap gap-2 mt-2">
                        <a href="{{ url('panel/user') }}" class="btn btn-back-add">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <button class="btn btn-submit-add" type="submit">
                            <i class="fas fa-paper-plane me-1"></i> Submit
                        </button>
                    </div>
                </form>

        </div>
    </div>
</div>

<style>
    :root {
        --primary-green: #1dd3b0;
        --primary-dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .page-header-add {
        background: var(--primary-dark);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .page-header-add h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .page-header-add .breadcrumb {
        margin: 0.25rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .page-header-add .breadcrumb-item a {
        color: #1dd3b0;
        text-decoration: none;
    }

    .page-header-add .breadcrumb-item.active {
        color: #d1d5db;
    }

    .add-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .add-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .add-card-header {
        background: var(--primary-dark);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .add-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .add-card-header .header-icon {
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

    .add-card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .add-card-body {
        padding: 1.5rem;
    }

    .add-card-body h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-dark);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid rgba(29, 211, 176, 0.3);
    }

    .add-card-body .form-floating > .form-control,
    .add-card-body .form-floating > .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .add-card-body .form-floating > .form-control:focus,
    .add-card-body .form-floating > .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.15);
    }

    .btn-submit-add {
        background: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-submit-add:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.4);
        color: white;
    }

    .btn-back-add {
        background: var(--primary-dark);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        transition: all 0.2s;
    }

    .btn-back-add:hover {
        background: #374151;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(31, 41, 55, 0.3);
        color: white;
    }

    .alert {
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
    }

    input:disabled,
    select:disabled {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    input:read-only {
        background-color: #f8f9fa !important;
        cursor: default;
    }

    .field-hidden {
        display: none !important;
    }

    /* ===== Tablet ===== */
    @media (max-width: 991px) {
        .container-fluid.px-4 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }

    /* ===== Mobile ===== */
    @media (max-width: 767px) {
        .container-fluid.px-4 {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 1rem !important;
        }

        .page-header-add {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .page-header-add h1 {
            font-size: 1.35rem;
        }

        .add-card {
            border-radius: 12px;
        }

        .add-card-header {
            padding: 0.875rem 1.25rem;
        }

        .add-card-body {
            padding: 1rem;
        }

        .add-card-body h4 {
            font-size: 0.9rem;
        }

        .add-card-body .form-floating > .form-control,
        .add-card-body .form-floating > .form-select {
            font-size: 0.8rem;
        }

        .btn-submit-add,
        .btn-back-add {
            font-size: 0.85rem;
            padding: 0.5rem 1.5rem;
        }
    }

    /* ===== Small Mobile ===== */
    @media (max-width: 575px) {
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .page-header-add {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .page-header-add h1 {
            font-size: 1.15rem;
        }

        .page-header-add .breadcrumb {
            font-size: 0.75rem;
        }

        .add-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .add-card-header .header-left {
            justify-content: center;
        }

        .add-card-header h5 {
            font-size: 0.875rem;
        }

        .add-card-body {
            padding: 0.875rem;
        }

        .add-card-body h4 {
            font-size: 0.85rem;
        }

        /* Stack form columns */
        .add-card-body .row > [class*="col-md"] {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .btn-submit-add,
        .btn-back-add {
            flex: 1;
            text-align: center;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
        }
    }
</style>

<!-- JavaScript - Consolidated into single DOMContentLoaded event -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const password = document.getElementById("inputPassword");
        const confirmPassword = document.getElementById("inputPasswordConfirm");
        const togglePassword = document.getElementById("togglePassword");
        const togglePasswordConfirm = document.getElementById("togglePasswordConfirm");
        const lrnValidation = document.getElementById("lrnValidation");
        const passwordRules = document.getElementById("passwordRules");
        const matchMessage = document.getElementById("passwordMatchMessage");
        const emailInput = document.getElementById("inputEmail");
        const emailValidation = document.getElementById("emailValidation");
        const submitBtn = document.getElementById("submitBtn");
        const ruleLength = document.getElementById("ruleLength");
        const ruleLetter = document.getElementById("ruleLetter");
        const ruleSpecial = document.getElementById("ruleSpecial");
        const lrnField = document.getElementById('lrnField');
        const lrnInput = document.getElementById('inputLRN');
        const lrnRules = document.getElementById('lrnRules');
        const ruleLrnLength = document.getElementById('ruleLrnLength');
        const ruleLrnNumeric = document.getElementById('ruleLrnNumeric');
        // Function to toggle student-related fields
        function toggleStudentFields() {
            const roleSelect = document.getElementById("role");
            const selectedRole = parseInt(roleSelect.value);

            console.log('Selected role ID:', selectedRole); // Debug log

            // All student-specific fields
            const studentFields = [{
                    element: document.getElementById("inputLRN"),
                    container: document.getElementById("lrnField")
                },
                {
                    element: document.getElementById("grade_level"),
                    container: document.getElementById("gradeLevelField")
                },
                {
                    element: document.getElementById("inputStdStatus"),
                    container: document.getElementById("stdStatusField")
                },
                {
                    element: document.getElementById("inputLastSYAttended"),
                    container: document.getElementById("lastSyField")
                }
            ];

            // Check if role is student-related
            let isStudent = false;
            if (selectedRole) {
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                const roleName = selectedOption.text.toLowerCase();
                // Check if role is student-related
                isStudent = roleName.includes('student');
            }

            // Handle student fields
            studentFields.forEach(field => {
                if (field.element && field.container) {
                    if (isStudent) {
                        // Show field
                        field.container.classList.remove("field-hidden");
                        field.element.disabled = false;

                        // Different behavior for role ID = 1 vs other student roles
                        if (selectedRole === 1) {
                            // For role ID = 1: fields are editable and required
                            field.element.readOnly = false;
                            field.element.disabled = false;
                            field.element.setAttribute('required', 'required');

                            // Clear any default values for role ID = 1
                            if (field.element.id === "inputLRN" && field.element.value === "000000000000") {
                                field.element.value = "";
                            }
                            if (field.element.id === "inputLastSYAttended" && field.element.value === "0000") {
                                field.element.value = "";
                            }
                        } else {
                            // For other student roles: set default values and make read-only/disabled
                            if (field.element.id === "inputLRN") {
                                field.element.value = "000000000000";
                                field.element.readOnly = true;
                                field.element.removeAttribute('required');
                            } else if (field.element.id === "grade_level") {
                                field.element.value = "";
                                field.element.disabled = true;
                                field.element.removeAttribute('required');
                            } else if (field.element.id === "inputStdStatus") {
                                field.element.value = "";
                                field.element.disabled = true;
                                field.element.removeAttribute('required');
                            } else if (field.element.id === "inputLastSYAttended") {
                                field.element.value = "0000";
                                field.element.readOnly = true;
                                field.element.removeAttribute('required');
                            }
                        }
                    } else {
                        // Hide field for non-students
                        field.container.classList.add("field-hidden");
                        field.element.disabled = true;
                        field.element.removeAttribute('required');
                        field.element.value = "";
                        field.element.classList.remove('is-invalid', 'is-valid');
                    }
                }
            });
        }

        // Enhanced email validation function
        function validateEmail(email) {
            // More comprehensive email regex
            const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
            return emailRegex.test(email);
        }

        // Real-time email format validation
        function validateEmailFormat(email) {
            const emailInput = document.getElementById("inputEmail");
            const emailValidation = document.getElementById("emailValidation");
            const emailFormatValidation = document.getElementById("emailFormatValidation");
            const formatCheck = document.getElementById("formatCheck");

            if (email.length === 0) {
                // Clear all validation when empty
                emailInput.classList.remove("is-invalid", "is-valid");
                emailValidation.classList.add("d-none");
                emailFormatValidation.classList.add("d-none");
                return;
            }

            // Check basic format
            if (!validateEmail(email)) {
                emailInput.classList.remove("is-valid");
                emailInput.classList.add("is-invalid");
                emailValidation.classList.remove("d-none");
                emailFormatValidation.classList.remove("d-none");
                formatCheck.textContent = "✖ Invalid email format";
                formatCheck.classList.replace("text-success", "text-danger");
                return;
            }

            // Check for common email issues
            const issues = [];

            // Check for consecutive dots
            if (email.includes('..')) {
                issues.push("Contains consecutive dots");
            }

            // Check for valid characters before @
            const localPart = email.split('@')[0];
            if (localPart.startsWith('.') || localPart.endsWith('.')) {
                issues.push("Cannot start or end with a dot");
            }

            // Check domain part
            const domainPart = email.split('@')[1];
            if (domainPart) {
                // Check for valid domain format
                if (!domainPart.includes('.') || domainPart.startsWith('.') || domainPart.endsWith('.')) {
                    issues.push("Invalid domain format");
                }

                // Check for valid TLD (at least 2 characters)
                const tld = domainPart.split('.').pop();
                if (tld && tld.length < 2) {
                    issues.push("Invalid top-level domain");
                }
            }

            if (issues.length > 0) {
                emailInput.classList.remove("is-valid");
                emailInput.classList.add("is-invalid");
                emailValidation.classList.add("d-none");
                emailFormatValidation.classList.remove("d-none");
                formatCheck.textContent = "✖ " + issues[0];
                formatCheck.classList.replace("text-success", "text-danger");
            } else {
                // Email format is valid
                emailInput.classList.remove("is-invalid");
                emailInput.classList.add("is-valid");
                emailValidation.classList.add("d-none");
                emailFormatValidation.classList.remove("d-none");
                formatCheck.textContent = "✔ Valid email format";
                formatCheck.classList.replace("text-danger", "text-success");
            }
        }

        // Single DOMContentLoaded event listener for all functionality


        lrnInput.addEventListener('focus', () => {
            lrnRules.classList.remove('d-none');
        });

        lrnInput.addEventListener('blur', () => {
            if (lrnInput.value.trim() === '') {
                lrnRules.classList.add('d-none');
            }
        });

        // 🧩 Optional: prevent typing non-digits
        lrnInput.addEventListener('keypress', (e) => {
            const value = lrnInput.value;

            // Disallow non-digits
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }

            // Prevent typing beyond 12 digits
            if (value.length >= 12) {
                e.preventDefault();
            }
        });

        lrnInput.addEventListener('input', () => {
            const value = lrnInput.value.trim();

            if (value === '') {
                // Reset to default state when empty
                ruleLrnLength.textContent = '✖ Must be exactly 12 digits';
                ruleLrnNumeric.textContent = '✖ Digits only (0–9)';
                ruleLrnLength.classList.add('text-danger');
                ruleLrnNumeric.classList.add('text-danger');
                ruleLrnLength.classList.remove('text-success');
                ruleLrnNumeric.classList.remove('text-success');
                return;
            }

            const validLength = value.length === 12;
            const validNumeric = /^\d+$/.test(value);

            ruleLrnLength.textContent = (validLength ? '✔' : '✖') + ' Must be exactly 12 digits';
            ruleLrnNumeric.textContent = (validNumeric ? '✔' : '✖') + ' Digits only (0–9)';

            ruleLrnLength.classList.toggle('text-success', validLength);
            ruleLrnLength.classList.toggle('text-danger', !validLength);
            ruleLrnNumeric.classList.toggle('text-success', validNumeric);
            ruleLrnNumeric.classList.toggle('text-danger', !validNumeric);
        });


        // Toggle password visibility
        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("bi-eye");
            this.classList.toggle("bi-eye-slash");
        });

        togglePasswordConfirm.addEventListener("click", function() {
            const type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
            confirmPassword.setAttribute("type", type);
            this.classList.toggle("bi-eye");
            this.classList.toggle("bi-eye-slash");
        });

        // Initialize student fields toggle on page load — based on old role value
        const roleSelect = document.getElementById("role");

        // Run the function once when the page loads
        toggleStudentFields();

        // Also re-run once the DOM fully paints (handles restored old() values)
        setTimeout(() => {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            if (selectedOption) {
                toggleStudentFields(); // Ensure correct field visibility after Laravel repopulates the form
            }
        }, 50);

        // Rebind the change event to handle future user selections
        roleSelect.addEventListener("change", toggleStudentFields);


        // Real-time email validation (frontend only)
        emailInput.addEventListener("input", function() {
            const email = emailInput.value.trim();
            validateEmailFormat(email);
        });

        // Additional validation on blur for better UX
        emailInput.addEventListener("blur", function() {
            const email = emailInput.value.trim();
            validateEmailFormat(email);
        });

        // Show password rules when typing in password
        // Show password rules when typing in password
        password.addEventListener("input", function() {
            passwordRules.classList.remove("d-none");

            const val = password.value;

            // Rule: Length 8–20 characters
            if (val.length >= 8 && val.length <= 20) {
                ruleLength.textContent = "✔ 8–20 characters";
                ruleLength.classList.replace("text-danger", "text-success");
            } else {
                ruleLength.textContent = "✖ 8–20 characters";
                ruleLength.classList.replace("text-success", "text-danger");
            }

            // Rule: Contains at least one letter
            if (/[a-zA-Z]/.test(val)) {
                ruleLetter.textContent = "✔ Contains a letter";
                ruleLetter.classList.replace("text-danger", "text-success");
            } else {
                ruleLetter.textContent = "✖ Contains a letter";
                ruleLetter.classList.replace("text-success", "text-danger");
            }

            // Rule: Contains at least one number
            if (/\d/.test(val)) {
                ruleNumber.textContent = "✔ Contains a number";
                ruleNumber.classList.replace("text-danger", "text-success");
            } else {
                ruleNumber.textContent = "✖ Contains a number";
                ruleNumber.classList.replace("text-success", "text-danger");
            }

            // Rule: Contains at least one special character
            if (/[^a-zA-Z0-9]/.test(val)) {
                ruleSpecial.textContent = "✔ Contains a special character";
                ruleSpecial.classList.replace("text-danger", "text-success");
            } else {
                ruleSpecial.textContent = "✖ Contains a special character";
                ruleSpecial.classList.replace("text-success", "text-danger");
            }

            // Rule: No spaces allowed
            if (!/\s/.test(val)) {
                ruleNoSpaces.textContent = "✔ No spaces allowed";
                ruleNoSpaces.classList.replace("text-danger", "text-success");
            } else {
                ruleNoSpaces.textContent = "✖ No spaces allowed";
                ruleNoSpaces.classList.replace("text-success", "text-danger");
            }

            // Check password match
            checkMatch();
        });


        // Check password match function
        function checkMatch() {
            if (confirmPassword.value) {
                matchMessage.classList.remove("d-none");

                if (confirmPassword.value === password.value) {
                    // Passwords match - show green checkmark
                    matchMessage.textContent = "✔ Passwords matched";
                    matchMessage.classList.remove("text-danger");
                    matchMessage.classList.add("text-success");
                } else {
                    // Passwords don't match - show red X
                    matchMessage.textContent = "✖ Passwords do not match";
                    matchMessage.classList.remove("text-success");
                    matchMessage.classList.add("text-danger");
                }
            } else {
                // Hide message when confirm password is empty
                matchMessage.classList.add("d-none");
            }
        }

        // Check password match when confirm password changes
        confirmPassword.addEventListener("input", checkMatch);

        // Form submission validation
        document.querySelector("form").addEventListener("submit", function(e) {
            const email = emailInput.value.trim();

            // Validate email before submission
            if (email && !validateEmail(email)) {
                e.preventDefault();
                emailInput.classList.add("is-invalid");
                emailValidation.classList.remove("d-none");
                emailInput.focus();
                return false;
            }

            // Check if passwords match
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                matchMessage.classList.remove("d-none");
                confirmPassword.focus();
                return false;
            }
        });
    });
</script>

@endsection
