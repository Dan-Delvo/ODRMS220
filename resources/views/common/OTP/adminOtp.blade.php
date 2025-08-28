@extends('layout.blankpage')
@section('content')
@include('layout.partials.message')

<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #23272e">
    <div class="p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px">
        <div class="container text-white text-center">
            <h4>Verify Your Email</h4>
            <p>Please enter the verification code we sent to your email</p>
            
            <!-- Timer Display -->
            <div id="timer-container" class="mb-3">
                <p class="mb-1">Time remaining:</p>
                <h5 id="timer" class="text-warning mb-0">03:00</h5>
            </div>

            <!-- Attempt Counter -->
            <div id="attempt-counter" class="mb-3">
                <small class="text-info">Attempts remaining: <span id="attempts-left">3</span></small>
            </div>

            <!-- Lockout Message -->
            <div id="lockout-message" class="alert alert-danger d-none">
                <strong>Account Temporarily Locked!</strong><br>
                Too many failed attempts. Please wait <span id="lockout-timer"></span> before trying again.
            </div>

            <!-- Expired Message -->
            <div id="expired-message" class="alert alert-warning d-none">
                <strong>OTP Expired!</strong><br>
                Your verification code has expired and your account has been temporarily locked for security.
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <form id="otp-form" action="{{ route('account.verify') }}" method="POST">
            @csrf
            <div class="d-flex justify-content-center align-items-center p-3 gap-1">
                <input type="text" name="first" class="otp-input form-control text-center" maxlength="1"
                style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                <input type="text" name="second" class="otp-input form-control text-center" maxlength="1"
                style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                <input type="text" name="third" class="otp-input form-control text-center" maxlength="1"
                style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                <input type="text" name="fourth" class="otp-input form-control text-center" maxlength="1"
                style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                <input type="text" name="fifth" class="otp-input form-control text-center" maxlength="1"
                style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                <input type="text" name="sixth" class="otp-input form-control text-center" maxlength="1"
                style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="resend-container" class="d-none">
                    <div class="small">
                        <a href="#" id="resend-btn" style="color: #1dd3b0; text-decoration: underline;">Resend OTP</a>
                    </div>
                </div>
                <div class="ms-auto">
                    <button type="submit" id="verify-btn" class="btn btn-outline-custom">Verify</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.btn-outline-custom {
    color: #1dd3b0;
    border-color: #1dd3b0;
    background-color: transparent;
}

.btn-outline-custom:hover {
    background-color: #1dd3b0;
    border-color: #1dd3b0;
    color: #000;
}

.btn-outline-custom:disabled {
    color: #6c757d;
    border-color: #6c757d;
    background-color: transparent;
}

.otp-input.error {
    border-color: #dc3545 !important;
    background-color: #f8d7da !important;
}

.shake {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80% {
        transform: translateX(-5px);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(5px);
    }
    100% {
        transform: translateX(0);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(".otp-input");
    const form = document.getElementById("otp-form");
    const verifyBtn = document.getElementById("verify-btn");
    const resendBtn = document.getElementById("resend-btn");
    const resendContainer = document.getElementById("resend-container");
    const timerElement = document.getElementById("timer");
    const timerContainer = document.getElementById("timer-container");
    const lockoutMessage = document.getElementById("lockout-message");
    const expiredMessage = document.getElementById("expired-message");
    const lockoutTimer = document.getElementById("lockout-timer");
    const attemptCounter = document.getElementById("attempt-counter");
    const attemptsLeft = document.getElementById("attempts-left");

    // Get data from session (passed from backend)
    let expiryTime = @json(session('expiresAt') ? session('expiresAt')->toISOString() : null);
    let email = @json(session('email'));
    let currentAttempts = @json(session("otp_attempts_{$email}" ?? session('email'), 0));
    let lockoutUntil = @json(session("lockout_until_{$email}" ?? session('email')) ? session("lockout_until_{$email}" ?? session('email'))->toISOString() : null);
    
    const maxAttempts = 3;
    let remainingAttempts = maxAttempts - currentAttempts;
    
    // Update attempt counter
    attemptsLeft.textContent = Math.max(0, remainingAttempts);
    
    // Check if currently locked out
    if (lockoutUntil && new Date() < new Date(lockoutUntil)) {
        showLockout();
        startLockoutTimer(new Date(lockoutUntil));
    } else if (expiryTime) {
        // Start the countdown timer if not locked out
        startCountdown(new Date(expiryTime));
    }

    // Check server-side error messages that indicate lockout
    @if(session('error') && (str_contains(session('error'), 'locked') || str_contains(session('error'), 'expired')))
        if (@json(str_contains(session('error'), 'locked'))) {
            // Force check lockout status from server
            checkLockoutStatus();
        }
    @endif

    // OTP input functionality
    inputs.forEach((input, index) => {
        input.addEventListener("input", (e) => {
            // Remove error styling on input
            input.classList.remove("error");
            
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && index > 0 && e.target.value === "") {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener("keypress", (e) => {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener("paste", (e) => {
            e.preventDefault();
            let pastedData = e.clipboardData.getData("text");
            let digits = pastedData.replace(/\D/g, "").split("").slice(0, inputs.length);

            inputs.forEach((inp, i) => {
                inp.value = digits[i] || "";
                inp.classList.remove("error");
            });

            if (digits.length > 0) {
                let lastIndex = Math.min(digits.length, inputs.length) - 1;
                inputs[lastIndex].focus();
            }
        });
    });

    // Form submission handler
    form.addEventListener("submit", function(e) {
        // Clear any previous error styling
        inputs.forEach(input => input.classList.remove("error"));
        
        if (isExpired() || isLockedOut()) {
            e.preventDefault();
            return false;
        }

        // Check if all fields are filled
        const allFilled = Array.from(inputs).every(input => input.value.length === 1);
        if (!allFilled) {
            e.preventDefault();
            inputs.forEach(input => {
                if (input.value.length === 0) {
                    input.classList.add("error");
                }
            });
            
            // Shake animation
            document.querySelector(".p-4").classList.add("shake");
            setTimeout(() => {
                document.querySelector(".p-4").classList.remove("shake");
            }, 500);
            
            return false;
        }
    });

    // Handle failed verification attempts
    @if(session('error') && !str_contains(session('error'), 'locked') && !str_contains(session('error'), 'expired'))
        // This was a failed attempt, add error styling and shake
        inputs.forEach(input => {
            input.classList.add("error");
            input.value = ''; // Clear the inputs
        });
        
        document.querySelector(".p-4").classList.add("shake");
        setTimeout(() => {
            document.querySelector(".p-4").classList.remove("shake");
        }, 500);
        
        // Focus first input
        inputs[0].focus();
        
        // Update attempt counter
        remainingAttempts--;
        attemptsLeft.textContent = Math.max(0, remainingAttempts);
        
        if (remainingAttempts <= 1) {
            attemptCounter.innerHTML = '<small class="text-danger">Attempts remaining: <span id="attempts-left">' + Math.max(0, remainingAttempts) + '</span> - Account will be locked after next failed attempt!</small>';
        }
    @endif

    // Resend OTP functionality
    resendBtn.addEventListener("click", function(e) {
        e.preventDefault();
        resendBtn.style.pointerEvents = 'none';
        resendBtn.textContent = 'Sending...';
        
        fetch("{{ route('account.resend') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: email
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update expiry time and restart countdown
                expiryTime = data.expiry;
                
                // Reset UI
                expiredMessage.classList.add("d-none");
                resendContainer.classList.add("d-none");
                timerContainer.classList.remove("d-none");
                enableForm();
                
                // Reset attempt counter
                remainingAttempts = maxAttempts;
                attemptsLeft.textContent = remainingAttempts;
                attemptCounter.innerHTML = '<small class="text-info">Attempts remaining: <span id="attempts-left">' + remainingAttempts + '</span></small>';
                
                // Clear inputs and remove error styling
                inputs.forEach(input => {
                    input.value = '';
                    input.classList.remove("error");
                });
                inputs[0].focus();
                
                // Restart countdown
                startCountdown(new Date(expiryTime));
                
                // Show success message temporarily
                showTemporaryMessage('New verification code sent!', 'success');
            } else {
                showTemporaryMessage(data.message || 'Failed to send code', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showTemporaryMessage('Network error. Please try again.', 'error');
        })
        .finally(() => {
            resendBtn.style.pointerEvents = 'auto';
            resendBtn.textContent = 'Resend OTP';
        });
    });

    function startCountdown(expiryTime) {
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const expiry = new Date(expiryTime).getTime();
            const timeLeft = expiry - now;

            if (timeLeft <= 0) {
                clearInterval(timer);
                handleExpiry();
                return;
            }

            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            timerElement.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Change color when time is running low
            if (timeLeft <= 30000) { // Less than 30 seconds
                timerElement.className = "text-danger mb-0 fw-bold";
            } else if (timeLeft <= 60000) { // Less than 1 minute
                timerElement.className = "text-warning mb-0";
            } else {
                timerElement.className = "text-success mb-0";
            }
        }, 1000);
    }

    function handleExpiry() {
        timerElement.textContent = "00:00";
        timerElement.className = "text-danger mb-0 fw-bold";
        expiredMessage.classList.remove("d-none");
        timerContainer.classList.add("d-none");
        attemptCounter.classList.add("d-none");
        disableForm();
        
        // Show resend option after expiry
        setTimeout(() => {
            resendContainer.classList.remove("d-none");
        }, 2000);
    }

    function showLockout() {
        lockoutMessage.classList.remove("d-none");
        timerContainer.classList.add("d-none");
        attemptCounter.classList.add("d-none");
        expiredMessage.classList.add("d-none");
        resendContainer.classList.add("d-none");
        disableForm();
    }

    function startLockoutTimer(lockoutTime) {
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const lockout = new Date(lockoutTime).getTime();
            const timeLeft = lockout - now;

            if (timeLeft <= 0) {
                clearInterval(timer);
                location.reload();
                return;
            }

            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            lockoutTimer.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function disableForm() {
        inputs.forEach(input => {
            input.disabled = true;
            input.style.backgroundColor = '#e9ecef';
        });
        verifyBtn.disabled = true;
    }

    function enableForm() {
        inputs.forEach(input => {
            input.disabled = false;
            input.style.backgroundColor = 'white';
        });
        verifyBtn.disabled = false;
    }

    function isExpired() {
        if (!expiryTime) return false;
        return new Date() > new Date(expiryTime);
    }

    function isLockedOut() {
        if (!lockoutUntil) return false;
        return new Date() < new Date(lockoutUntil);
    }

    function checkLockoutStatus() {
        fetch("{{ route('account.lockout.status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.locked) {
                showLockout();
                lockoutTimer.textContent = data.remaining_minutes + ':00';
                
                // Start countdown from server time
                const lockoutEnd = new Date();
                lockoutEnd.setMinutes(lockoutEnd.getMinutes() + data.remaining_minutes);
                startLockoutTimer(lockoutEnd);
            } else {
                remainingAttempts = data.max_attempts - data.attempts;
                attemptsLeft.textContent = Math.max(0, remainingAttempts);
            }
        })
        .catch(error => {
            console.error('Error checking lockout status:', error);
        });
    }

    function showTemporaryMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.container.text-white').appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Focus first input on load if form is enabled
    if (!verifyBtn.disabled) {
        inputs[0].focus();
    }
});
</script>

@endsection