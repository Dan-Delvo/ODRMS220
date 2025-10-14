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

                <form action="{{ route('account.otp') }}" method="POST">
                    @csrf

                    <!-- Personal Information Section -->
                    <h4 class="mb-3 text-dark">Personal Information</h4>

                    <!-- First and Middle Name -->
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
                                <input class="form-control @error('MiddleName') is-invalid @enderror" value="{{ old('MiddleName') }}"
                                    id="inputMiddleName"
                                    type="text"
                                    name="MiddleName"
                                    placeholder="Enter your middle name"
                                    required />
                                <label for="inputMiddleName">Middle Name (Optional)</label>
                                @error('MiddleName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="form-floating mb-3">
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
                    <div class="form-floating mb-3">
                        <input class="form-control @error('LRN') is-invalid @enderror"
                            value="{{ old('LRN') }}"
                            id="inputLRN"
                            type="text"
                            name="LRN"
                            placeholder="Enter your LRN" />
                        <label for="inputLRN">LRN (Learner's Reference Number)</label>
                        <div id="lrnValidation" class="invalid-feedback d-none"></div>
                        @error('LRN')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="lrnRules" class="mb-3 d-none">
                            <small id="ruleLrnLength" class="d-block text-danger">✖ Must be exactly 12 digits</small>
                            <small id="ruleLrnNumeric" class="d-block text-danger">✖ Digits only (0–9)</small>
                        </div>
                    </div>

                    <!-- Grade Level -->
                    <div class="form-floating mb-3">
                        <div class="form-group">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <select class="form-control @error('Grade_level') is-invalid @enderror"
                                id="grade_level"
                                name="Grade_level"
                                required>
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
                    <div class="form-floating mb-3">
                        <div class="form-group">
                            <label for="std_status" class="form-label">Student Status</label>
                            <select class="form-control @error('Std_status') is-invalid @enderror"
                                id="inputStdStatus"
                                name="Std_status"
                                required>
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
                        <div class="form-group">
                            <label for="inputLastSYAttended">Last School Year Attended</label>
                            <input class="form-control @error('Last_sy_attended') is-invalid @enderror"
                                value="{{ old('Last_sy_attended') }}"
                                id="inputLastSYAttended"
                                type="text"
                                name="Last_sy_attended"
                                placeholder="Enter last school year attended" />
                            @error('Last_sy_attended')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Account Information Section -->
                    <h4 class="mb-3 text-dark">Account Information</h4>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input class="form-control @error('email_address') is-invalid @enderror"
                            id="inputEmail"
                            type="email"
                            name="email_address"
                            placeholder="name@example.com" />
                        <label for="inputEmail">Email Address</label>
                        @error('email_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="form-floating mb-3">
                        <input class="form-control @error('username') is-invalid @enderror"
                            id="inputUsername"
                            type="text"
                            name="username"
                            placeholder="Enter your username" />
                        <label for="inputUsername">Username</label>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password with Eye Icon -->
                    <div class="form-floating mb-3 position-relative">
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" required />
                        <label for="inputPassword">Password</label>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;"></i>
                    </div>

                    <!-- Password Rules -->
                    <div id="passwordRules" class="mb-3 d-none">
                        <small id="ruleLength" class="d-block text-danger">✖ 8–20 characters</small>
                        <small id="ruleLetter" class="d-block text-danger">✖ Contains a letter</small>
                        <small id="ruleNumber" class="d-block text-danger">✖ Contains a number</small>
                        <small id="ruleSpecial" class="d-block text-danger">✖ Contains a special character</small>
                        <small id="ruleNoSpaces" class="d-block text-danger">✖ No spaces allowed</small>
                    </div>

                    <!-- Confirm Password with Eye Icon -->
                    <div class="form-floating mb-3 position-relative">
                        <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder="Confirm password" required />
                        <label for="inputPasswordConfirm">Confirm Password</label>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3" id="togglePasswordConfirm" style="cursor: pointer;"></i>
                    </div>

                    <small id="passwordMatchMessage" class="text-danger d-none">
                        Passwords do not match.
                    </small>

                    <!-- Submit and Back Buttons -->
                    <div class="mt-4 mb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <button class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);" type="submit">
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
        const role = document.getElementById("role").value;
        const isStudent = parseInt(role) === 1;

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

    // Single DOMContentLoaded event listener for all functionality
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
            if (!/[0-9]/.test(e.key)) {
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
            if (confirmPassword.value && confirmPassword.value !== password.value) {
                matchMessage.classList.remove("d-none");
            } else {
                matchMessage.classList.add("d-none");
            }
        }

        // Check password match when confirm password changes
        confirmPassword.addEventListener("input", checkMatch);
    });
</script>

@endsection