@extends('layout.blankpage')

@section('content')

@include('layout.partials.message')
<!-- Main Content Wrapper -->
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5" style="min-height: 400px;">
            <div class="card-header text-white" style="background-color: #1f2937;">
                <h3 class="text-center font-weight-light my-4">Create Account</h3>
            </div>
            <div class="card-body bg-light">

                <!-- Error Messages
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif -->

                <!-- Form to create user -->
                <form action="{{ route('account.otp') }}" method="POST">
                    @csrf

                    <!-- Personal Information Section -->
                    <h4 class="mb-3 text-dark">Personal Information</h4>

                    <!-- First and Last Name -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                                <input class="form-control @error('FirstName') is-invalid @enderror"
                                    value="{{ old('FirstName') }}"
                                    id="inputFirstName"
                                    type="text"
                                    name="FirstName"
                                    placeholder="Enter your first name"
                                    required />
                                <label for="inputFirstName">First Name</label>
                                @error('FirstName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('LastName') is-invalid @enderror"
                                    value="{{ old('LastName') }}"
                                    id="inputLastName"
                                    type="text"
                                    name="LastName"
                                    placeholder="Enter your last name"
                                    required />
                                <label for="inputLastName">Last Name</label>
                                @error('LastName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div class="form-floating mb-3">
                        <input class="form-control @error('MiddleName') is-invalid @enderror" value="{{ old('MiddleName') }}"
                            id="inputMiddleName"
                            type="text"
                            name="MiddleName"
                            placeholder="Enter your middle name"
                            required />
                        <label for="inputMiddleName">Middle Name</label>
                        @error('MiddleName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Suffix -->
                    <div class="form-floating mb-3">
                        <input class="form-control @error('Suffix') is-invalid @enderror"
                            value="{{ old('Suffix') }}"
                            id="inputSuffix"
                            type="text"
                            name="Suffix"
                            placeholder="Enter your suffix" />
                        <label for="inputSuffix">Suffix (Optional)</label>
                        @error('Suffix')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="form-floating mb-3">
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control @error('role') is-invalid @enderror"
                                id="role"
                                name="role"
                                onchange="toggleStudentFields()" required>
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                                @foreach ($role as $roles)
                                <option value="{{ $roles->id }}" @selected(old('role')==$roles)>
                                    {{ $roles->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- LRN -->
                    <div class="form-floating mb-3" id="lrnField">
                        <input class="form-control @error('LRN') is-invalid @enderror"
                            value="{{ old('LRN') }}"
                            id="inputLRN"
                            type="text"
                            name="LRN"
                            placeholder="Enter your LRN" />
                        <label for="inputLRN">LRN (Learner's Reference Number)</label>
                        @error('LRN')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Grade Level -->
                    <div class="form-floating mb-3" id="gradeLevelField">
                        <div class="form-group">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <select class="form-control @error('Grade_level') is-invalid @enderror"
                                id="grade_level"
                                name="Grade_level">
                                <option value="" disabled {{ old('Grade_level') ? '' : 'selected' }}>Select Grade Level</option>
                                @foreach ($grade as $level)
                                <option value="{{ $level }}" @selected(old('Grade_level')==$level)>
                                    {{ $level }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Student Status -->
                    <div class="form-floating mb-3" id="stdStatusField">
                        <div class="form-group">
                            <label for="std_status" class="form-label">Student Status</label>
                            <select class="form-control @error('Std_status') is-invalid @enderror"
                                id="inputStdStatus"
                                name="Std_status">
                                <option value="" disabled {{ old('Std_status') ? '' : 'selected' }}>Select Status</option>
                                @foreach ($stat as $status)
                                <option value="{{ $status }}" @selected(old('Std_status')==$status)>
                                    {{ $status }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Last School Year Attended -->
                    <div class="form-floating mb-3" id="lastSyField">
                        <input class="form-control @error('Last_sy_attended') is-invalid @enderror"
                            value="{{ old('Last_sy_attended') }}"
                            id="inputLastSYAttended"
                            type="text"
                            name="Last_sy_attended"
                            placeholder="Enter last school year attended" />
                        <label for="inputLastSYAttended">Last School Year Attended</label>
                        @error('Last_sy_attended')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Account Information Section -->
                    <h4 class="mb-3 text-dark">Account Information</h4>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input class="form-control @error('email_address') is-invalid @enderror"
                            value="{{ old('email_address') }}"
                            id="inputEmail"
                            type="email"
                            name="email_address"
                            placeholder="name@example.com"
                            required />
                        <label for="inputEmail">Email Address</label>
                        <div id="emailValidation" class="invalid-feedback d-none">
                            Please enter a valid email address (e.g., user@example.com)
                        </div>
                        <div id="emailFormatValidation" class="mb-2 d-none">
                            <small id="formatCheck" class="text-danger">✖ Invalid email format</small>
                        </div>
                        @error('email_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="form-floating mb-3">
                        <input class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}"
                            id="inputUsername"
                            type="text"
                            name="username"
                            placeholder="Enter your username"
                            required />
                        <label for="inputUsername">Username</label>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" required />
                        <label for="inputPassword">Password</label>
                    </div>

                    <!-- Password Rules -->
                    <div id="passwordRules" class="mb-3 d-none">
                        <small id="ruleLength" class="d-block text-danger">✖ At least 8 characters</small>
                        <small id="ruleLetter" class="d-block text-danger">✖ Contains a letter</small>
                        <small id="ruleSpecial" class="d-block text-danger">✖ Contains a special character</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder="Confirm password" required />
                        <label for="inputPasswordConfirm">Confirm Password</label>
                    </div>

                    <!-- Password Match Message -->
                    <small id="passwordMatchMessage" class="text-danger d-none">
                        Passwords do not match.
                    </small>

                    <!-- Submit and Back Buttons -->
                    <div class="mt-4 mb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <button class="btn text-black fw-semibold" id="submitBtn" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);" type="submit">
                                Submit
                            </button>
                            <a href="{{ url('panel/user') }}" class="btn text-white fw-semibold" style="background-color: #1f2937; box-shadow: 0 4px 10px rgba(31, 41, 55, 0.5);">
                                Back
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Optional styles to show disabled fields clearly -->
<style>
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
</style>

<!-- JavaScript - Consolidated into single DOMContentLoaded event -->
<script>
    // Function to toggle student-related fields
    function toggleStudentFields() {
        const roleSelect = document.getElementById("role");
        const selectedRole = parseInt(roleSelect.value);

        console.log('Selected role ID:', selectedRole); // Debug log

        // All student-specific fields
        const studentFields = [
            { element: document.getElementById("inputLRN"), container: document.getElementById("lrnField") },
            { element: document.getElementById("grade_level"), container: document.getElementById("gradeLevelField") },
            { element: document.getElementById("inputStdStatus"), container: document.getElementById("stdStatusField") },
            { element: document.getElementById("inputLastSYAttended"), container: document.getElementById("lastSyField") }
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
    document.addEventListener("DOMContentLoaded", function() {
        const password = document.getElementById("inputPassword");
        const confirmPassword = document.getElementById("inputPasswordConfirm");
        const passwordRules = document.getElementById("passwordRules");
        const matchMessage = document.getElementById("passwordMatchMessage");
        const emailInput = document.getElementById("inputEmail");
        const emailValidation = document.getElementById("emailValidation");
        const submitBtn = document.getElementById("submitBtn");

        const ruleLength = document.getElementById("ruleLength");
        const ruleLetter = document.getElementById("ruleLetter");
        const ruleSpecial = document.getElementById("ruleSpecial");

        // Initialize student fields toggle on page load
        toggleStudentFields();

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
        password.addEventListener("input", function() {
            passwordRules.classList.remove("d-none");

            // Rule: At least 8 characters
            if (password.value.length >= 8) {
                ruleLength.textContent = "✔ At least 8 characters";
                ruleLength.classList.replace("text-danger", "text-success");
            } else {
                ruleLength.textContent = "✖ At least 8 characters";
                ruleLength.classList.replace("text-success", "text-danger");
            }

            // Rule: Contains at least one letter
            if (/[a-zA-Z]/.test(password.value)) {
                ruleLetter.textContent = "✔ Contains a letter";
                ruleLetter.classList.replace("text-danger", "text-success");
            } else {
                ruleLetter.textContent = "✖ Contains a letter";
                ruleLetter.classList.replace("text-success", "text-danger");
            }

            // Rule: Contains at least one special character
            if (/[^a-zA-Z0-9]/.test(password.value)) {
                ruleSpecial.textContent = "✔ Contains a special character";
                ruleSpecial.classList.replace("text-danger", "text-success");
            } else {
                ruleSpecial.textContent = "✖ Contains a special character";
                ruleSpecial.classList.replace("text-success", "text-danger");
            }

            // Check password match when password changes
            checkMatch();
        });

        // Check password match function
        function checkMatch() {
            if (confirmPassword.value && confirmPassword.value !== password.value) {
                matchMessage.classList.remove("d-none");
            } else {
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
