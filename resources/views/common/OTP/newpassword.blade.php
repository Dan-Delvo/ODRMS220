@extends('layout.loginpage')

@section('content')
@include('layout.partials.message')
<!-- @if (session('status'))
<div id="successAlert" class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow-lg" role="alert" style="z-index: 1050; width: 20%;">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
<div id="errorAlert" class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow-lg" role="alert" style="z-index: 1050; width: 20%;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif -->

<div class="d-flex justify-content-center align-items-center" style="background-color: #23272e; height: 100vh;">
    <div class="container p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px">
        <div class="container">
            <h3 class="text-center fw-semibold" style="color: #1dd3b0">Change Your Password</h3>
            <p class="text-center text-light">Enter a new password below to change your password</p>

            <form action="{{ route('newpassword.submit')}}" method="POST" id="changePasswordForm">
                @csrf
                <label for="password" class="form-label mt-3 text-light">Password</label>
                <div class="input-group mb-3">
                    <input type="password" id="password" class="form-control" name="password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                        <i id="toggleIcon1" class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Password Rules (hidden by default) -->
                <div id="passwordRules" class="mb-3 d-none">
                    <small id="ruleLength" class="d-block text-danger">✖ 8-20 characters</small>
                    <small id="ruleLetter" class="d-block text-danger">✖ Contains a letter</small>
                    <small id="ruleNumber" class="d-block text-danger">✖ Contains a number</small>
                    <small id="ruleSpecial" class="d-block text-danger">✖ Contains a special character</small>
                    <small id="ruleNoSpaces" class="d-block text-danger">✖ No spaces allowed</small>
                </div>

                <label for="confirmPassword" class="form-label text-light">Confirm Password</label>
                <div class="input-group mb-3">
                    <input type="password" id="confirmPassword" class="form-control">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                        <i id="toggleIcon2" class="bi bi-eye"></i>
                    </button>
                </div>

                <small id="passwordMatchMessage" class="text-danger d-none mb-3 d-block">
                    Passwords do not match.
                </small>

                <div id="passwordHelpBlock" class="form-text text-light mb-3">
                    Your password must be 8-20 characters long, contain letters, numbers, and special characters, and must not contain spaces.
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-custom">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordRules = document.getElementById('passwordRules');
        const form = document.getElementById('changePasswordForm');
        const matchMessage = document.getElementById('passwordMatchMessage');

        // Show password rules when password field is focused
        passwordInput.addEventListener('focus', () => {
            passwordRules.classList.remove('d-none');
        });

        // Real-time password validation
        passwordInput.addEventListener('input', () => {
            const value = passwordInput.value;
            const ruleLength = document.getElementById('ruleLength');
            const ruleLetter = document.getElementById('ruleLetter');
            const ruleNumber = document.getElementById('ruleNumber');
            const ruleSpecial = document.getElementById('ruleSpecial');
            const ruleNoSpaces = document.getElementById('ruleNoSpaces');

            // Rule: 8-20 characters
            if (value.length >= 8 && value.length <= 20) {
                ruleLength.classList.remove('text-danger');
                ruleLength.classList.add('text-success');
                ruleLength.innerHTML = '✔ 8-20 characters';
            } else {
                ruleLength.classList.remove('text-success');
                ruleLength.classList.add('text-danger');
                ruleLength.innerHTML = '✖ 8-20 characters';
            }

            // Rule: Contains at least one letter
            if (/[a-zA-Z]/.test(value)) {
                ruleLetter.classList.remove('text-danger');
                ruleLetter.classList.add('text-success');
                ruleLetter.innerHTML = '✔ Contains a letter';
            } else {
                ruleLetter.classList.remove('text-success');
                ruleLetter.classList.add('text-danger');
                ruleLetter.innerHTML = '✖ Contains a letter';
            }

            // Rule: Contains at least one number
            if (/[0-9]/.test(value)) {
                ruleNumber.classList.remove('text-danger');
                ruleNumber.classList.add('text-success');
                ruleNumber.innerHTML = '✔ Contains a number';
            } else {
                ruleNumber.classList.remove('text-success');
                ruleNumber.classList.add('text-danger');
                ruleNumber.innerHTML = '✖ Contains a number';
            }

            // Rule: Contains special character
            if (/[^a-zA-Z0-9\s]/.test(value)) {
                ruleSpecial.classList.remove('text-danger');
                ruleSpecial.classList.add('text-success');
                ruleSpecial.innerHTML = '✔ Contains a special character';
            } else {
                ruleSpecial.classList.remove('text-success');
                ruleSpecial.classList.add('text-danger');
                ruleSpecial.innerHTML = '✖ Contains a special character';
            }

            // Rule: No spaces
            if (!/\s/.test(value)) {
                ruleNoSpaces.classList.remove('text-danger');
                ruleNoSpaces.classList.add('text-success');
                ruleNoSpaces.innerHTML = '✔ No spaces allowed';
            } else {
                ruleNoSpaces.classList.remove('text-success');
                ruleNoSpaces.classList.add('text-danger');
                ruleNoSpaces.innerHTML = '✖ No spaces allowed';
            }

            // Also check password match when main password changes
            validatePasswordMatch();
        });

        // Password match validation
        function validatePasswordMatch() {
            if (confirmPasswordInput.value === '') {
                matchMessage.classList.add('d-none');
                return;
            }

            if (passwordInput.value !== confirmPasswordInput.value) {
                matchMessage.classList.remove('d-none');
            } else {
                matchMessage.classList.add('d-none');
            }
        }

        confirmPasswordInput.addEventListener('input', validatePasswordMatch);

        // Form submission validation
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Always prevent default first

            const passwordVal = passwordInput.value;
            const confirmPasswordVal = confirmPasswordInput.value;
            let isValid = true;

            // Check if password is empty
            if (passwordVal === "") {
                alert("Password field cannot be empty.");
                return false;
            }

            // Check password length
            if (passwordVal.length < 8 || passwordVal.length > 20) {
                alert("Password must be between 8-20 characters.");
                passwordRules.classList.remove('d-none');
                return false;
            }

            // Check for at least one letter
            if (!/[a-zA-Z]/.test(passwordVal)) {
                alert("Password must contain at least one letter.");
                passwordRules.classList.remove('d-none');
                return false;
            }

            // Check for at least one number
            if (!/[0-9]/.test(passwordVal)) {
                alert("Password must contain at least one number.");
                passwordRules.classList.remove('d-none');
                return false;
            }

            // Check for special characters
            if (!/[^a-zA-Z0-9\s]/.test(passwordVal)) {
                alert("Password must contain at least one special character.");
                passwordRules.classList.remove('d-none');
                return false;
            }

            // Check for spaces
            if (/\s/.test(passwordVal)) {
                alert("Password must not contain spaces.");
                passwordRules.classList.remove('d-none');
                return false;
            }

            // Check if passwords match
            if (passwordVal !== confirmPasswordVal) {
                alert("Passwords do not match. Try again.");
                return false;
            }
            // Here you would normally submit the form
            form.submit();
        });
    });

    function togglePassword(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>
@endsection