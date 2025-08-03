<div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg mt-5" style="min-height: 400px;">
                    <div class="card-header text-light" style="background-color: #1f2937">
                        <h3 class="text-center font-weight-light my-4" style="color: #1dd3b0">Create Account</h3>
                    </div>

                    <form action="{{ route('account.otp') }}" method="POST" id="registerForm">
                        @csrf
                        <div class="card-body bg-light">
                            <div class="form-floating mb-3">
                                <input class="form-control @error('email_address') is-invalid @enderror" id="inputEmail" type="email" name="email_address" placeholder="name@example.com" />
                                <label for="inputEmail">Email Address</label>
                                @error('email_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control @error('username') is-invalid @enderror" id="inputUsername" type="text" name="username" placeholder="Enter your username" />
                                <label for="inputUsername">Username</label>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-floating mb-3 position-relative">
                                <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" />
                                <label for="inputPassword">Password</label>
                                <button type="button" class="btn btn-sm btn-link position-absolute end-0 me-3 text-dark"
                                    id="togglePassword1" tabindex="-1" aria-label="Toggle password visibility"
                                    style="top: 50%; transform: translateY(-50%); z-index: 10;">
                                    <i class="fas fa-eye" id="eyeIcon1"></i>
                                </button>
                            </div>
                            <div id="passwordRules" class="mb-3 d-none">
                                <small id="ruleLength" class="d-block text-danger">✖ At least 8 characters</small>
                                <small id="ruleLetter" class="d-block text-danger">✖ Contains a letter</small>
                                <small id="ruleSpecial" class="d-block text-danger">✖ Contains a special character</small>
                            </div>

                            <div class="form-floating mb-3 position-relative">
                                <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder="Confirm password" />
                                <label for="inputPasswordConfirm">Confirm Password</label>
                                <button type="button" class="btn btn-sm btn-link position-absolute end-0 me-3 text-dark"
                                    id="togglePassword2" tabindex="-1" aria-label="Toggle password visibility"
                                    style="top: 50%; transform: translateY(-50%); z-index: 10;">
                                    <i class="fas fa-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                            <small id="passwordMatchMessage" class="text-danger d-none">
                                Passwords do not match.
                            </small>
                        </div>

                        <div class="card-footer py-3 d-flex justify-content-between align-items-center" style="background-color: #1f2937;">
                            <div>
                                <button class="btn btn-block" type="submit" style="background-color: #1dd3b0; color: white;">
                                    Create Account
                                </button>
                            </div>

                            <div class="small text-end">
                                <span style="color: #94a3b8;">Have an account? </span>
                                <a href="#" class="fw-semibold" style="color: #1dd3b0; font-size: 0.85rem;">
                                    Go to login
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@push('scripts')
 <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordRules = document.getElementById('passwordRules');
            const toggleBtn1 = document.getElementById('togglePassword1');
            const passwordInput1 = document.getElementById('inputPassword');

            const toggleBtn2 = document.getElementById('togglePassword2');
            const passwordInput2 = document.getElementById('inputPasswordConfirm');
            const eyeIcon1 = document.getElementById('eyeIcon1');
            const eyeIcon2 = document.getElementById('eyeIcon2');
            const form = document.getElementById('registerForm');

            form.addEventListener('submit', function (e) {
                let isValid = true;

                const passwordVal = passwordInput1.value;
                const confirmPasswordVal = passwordInput2.value;
                const ruleLength = document.getElementById('ruleLength');
                const ruleLetter = document.getElementById('ruleLetter');
                const ruleSpecial = document.getElementById('ruleSpecial');
                const message = document.getElementById('passwordMatchMessage'); // Fixed: was missing declaration

                // Check password length
                if (passwordVal.length < 8) {
                    ruleLength.classList.remove('text-success');
                    ruleLength.classList.add('text-danger');
                    ruleLength.innerHTML = '✖ At least 8 characters';
                    passwordRules.classList.remove('d-none');
                    isValid = false;
                } else {
                    ruleLength.classList.remove('text-danger');
                    ruleLength.classList.add('text-success');
                    ruleLength.innerHTML = '✔ At least 8 characters';
                }

                // Check password has at least one letter
                if (!/[a-zA-Z]/.test(passwordVal)) {
                    ruleLetter.classList.remove('text-success');
                    ruleLetter.classList.add('text-danger');
                    ruleLetter.innerHTML = '✖ Contains a letter';
                    passwordRules.classList.remove('d-none');
                    isValid = false;
                } else {
                    ruleLetter.classList.remove('text-danger');
                    ruleLetter.classList.add('text-success');
                    ruleLetter.innerHTML = '✔ Contains a letter';
                }

                // Check password has special character
                if (!/[^a-zA-Z0-9]/.test(passwordVal)) {
                    ruleSpecial.classList.remove('text-success');
                    ruleSpecial.classList.add('text-danger');
                    ruleSpecial.innerHTML = '✖ Contains a special character';
                    passwordRules.classList.remove('d-none');
                    isValid = false;
                } else {
                    ruleSpecial.classList.remove('text-danger');
                    ruleSpecial.classList.add('text-success');
                    ruleSpecial.innerHTML = '✔ Contains a special character';
                }

                // Check confirm password matches
                if (passwordVal !== confirmPasswordVal) {
                    message.classList.remove('d-none');
                    isValid = false;
                } else {
                    message.classList.add('d-none');
                }

                if (!isValid) {
                    e.preventDefault(); // Stop form submission
                    passwordRules.classList.remove('d-none'); // Ensure rules are visible
                }
            });

            passwordInput1.addEventListener('focus', () => {
                passwordRules.classList.remove('d-none');
            });

            passwordInput1.addEventListener('input', () => {
                const value = passwordInput1.value;
                const ruleLength = document.getElementById('ruleLength');
                const ruleLetter = document.getElementById('ruleLetter');
                const ruleSpecial = document.getElementById('ruleSpecial'); // Added missing reference

                // Rule: At least 8 characters
                if (value.length >= 8) {
                    ruleLength.classList.remove('text-danger');
                    ruleLength.classList.add('text-success');
                    ruleLength.innerHTML = '✔ At least 8 characters';
                } else {
                    ruleLength.classList.remove('text-success');
                    ruleLength.classList.add('text-danger');
                    ruleLength.innerHTML = '✖ At least 8 characters';
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

                // Rule: Contains special character (FIXED: Added this missing validation)
                if (/[^a-zA-Z0-9]/.test(value)) {
                    ruleSpecial.classList.remove('text-danger');
                    ruleSpecial.classList.add('text-success');
                    ruleSpecial.innerHTML = '✔ Contains a special character';
                } else {
                    ruleSpecial.classList.remove('text-success');
                    ruleSpecial.classList.add('text-danger');
                    ruleSpecial.innerHTML = '✖ Contains a special character';
                }
            });

            // Fixed: Separate eye icons for each toggle button
            toggleBtn1.addEventListener('click', function () {
                const type = passwordInput1.type === 'password' ? 'text' : 'password';
                passwordInput1.type = type;
                eyeIcon1.classList.toggle('fa-eye');
                eyeIcon1.classList.toggle('fa-eye-slash');
            });

            toggleBtn2.addEventListener('click', function () {
                const type = passwordInput2.type === 'password' ? 'text' : 'password';
                passwordInput2.type = type;
                eyeIcon2.classList.toggle('fa-eye');
                eyeIcon2.classList.toggle('fa-eye-slash');
            });

            const password = document.getElementById('inputPassword');
            const confirmPassword = document.getElementById('inputPasswordConfirm');
            const message = document.getElementById('passwordMatchMessage');

            function validatePasswordMatch() {
                if (confirmPassword.value === '') {
                    message.classList.add('d-none');
                    return;
                }

                if (password.value !== confirmPassword.value) {
                    message.classList.remove('d-none');
                } else {
                    message.classList.add('d-none');
                }
            }

            password.addEventListener('input', validatePasswordMatch);
            confirmPassword.addEventListener('input', validatePasswordMatch);
        });
    </script>
@endpush
