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
                    <div class="form-floating mb-3">
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
                    <div class="form-floating mb-3">
                        <input class="form-control @error('Last_sy_attended') is-invalid @enderror"
                            value="{{ old('Last_sy_attended') }}"
                            id="inputLastSYAttended"
                            type="text"
                            name="Last_sy_attended"
                            placeholder="Enter last school year attended"
                            required />
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

                    <!-- Password -->
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" />
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
                        <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder="Confirm password" />
                        <label for="inputPasswordConfirm">Confirm Password</label>
                    </div>

                    <!-- Password Match Message -->
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
</style>

<!-- JavaScript - Consolidated into single DOMContentLoaded event -->
<script>
    // Function to toggle student-related fields
    function toggleStudentFields() {
        const role = document.getElementById("role").value;
        const isStudent = parseInt(role) === 1;

        // Fields to toggle
        document.getElementById("inputLRN").disabled = !isStudent;
        document.getElementById("grade_level").disabled = !isStudent;
        document.getElementById("inputLastSYAttended").disabled = !isStudent;
        document.getElementById("inputStdStatus").disabled = !isStudent;

        // Optionally hide the entire divs if you prefer to completely remove visibility
        const stdFields = [
            "inputLRN", "grade_level", "inputLastSYAttended", "inputStdStatus"
        ];
        stdFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.closest(".form-group")?.classList.toggle("d-none", !isStudent);
                el.closest(".form-floating")?.classList.toggle("d-none", !isStudent);
            }
        });
    }

    // Single DOMContentLoaded event listener for all functionality
    document.addEventListener("DOMContentLoaded", function() {
        const password = document.getElementById("inputPassword");
        const confirmPassword = document.getElementById("inputPasswordConfirm");
        const passwordRules = document.getElementById("passwordRules");
        const matchMessage = document.getElementById("passwordMatchMessage");

        const ruleLength = document.getElementById("ruleLength");
        const ruleLetter = document.getElementById("ruleLetter");
        const ruleSpecial = document.getElementById("ruleSpecial");

        // Initialize student fields toggle on page load
        toggleStudentFields();

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
    });
</script>

@endsection