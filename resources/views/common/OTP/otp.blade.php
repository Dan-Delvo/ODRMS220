@extends('layout.loginpage')
@section('content')
@include('layout.partials.message')

<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #23272e">
    <div class="p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px">
        <div class="container text-white text-center">
            <h4>Verify Your Email</h4>
            <p>Please enter the verification code we sent to your gmail</p>
            
            <!-- Timer Display -->
            <div id="timer-container" class="mb-3">
                <p class="mb-1">Time remaining:</p>
                <h5 id="timer" class="text-warning mb-0">05:00</h5>
            </div>

            <!-- Lockout Message -->
            <div id="lockout-message" class="alert alert-danger d-none">
                <strong>Account Temporarily Locked!</strong><br>
                Too many failed attempts. Please wait <span id="lockout-timer"></span> before trying again.
            </div>

            <!-- Expired Message -->
            <div id="expired-message" class="alert alert-warning d-none">
                <strong>OTP Expired!</strong><br>
                Please request a new verification code.
            </div>
        </div>

        <form id="otp-form" action="{{route('verifyotp.submit')}}" method="POST">
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

    // Get expiry time from session (passed from backend)
    let expiryTime = @json(session('expiry'));
    let attemptCount = parseInt(localStorage.getItem('otp_attempts') || '0');
    let lockoutUntil = localStorage.getItem('otp_lockout_until');
    
    // Check if currently locked out
    if (lockoutUntil && new Date() < new Date(lockoutUntil)) {
        showLockout();
        startLockoutTimer(new Date(lockoutUntil));
        return;
    } else {
        // Clear expired lockout
        localStorage.removeItem('otp_lockout_until');
        localStorage.removeItem('otp_attempts');
        attemptCount = 0;
    }

    // Start the countdown timer
    if (expiryTime) {
        startCountdown(new Date(expiryTime));
    }

    // OTP input functionality
    inputs.forEach((input, index) => {
        input.addEventListener("input", (e) => {
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
            });

            if (digits.length > 0) {
                let lastIndex = Math.min(digits.length, inputs.length) - 1;
                inputs[lastIndex].focus();
            }
        });
    });

    // Form submission handler
    form.addEventListener("submit", function(e) {
        if (isExpired() || isLockedOut()) {
            e.preventDefault();
            return false;
        }
    });

    // Handle form submission response
    @if(session('error'))
        attemptCount++;
        localStorage.setItem('otp_attempts', attemptCount);
        
        if (attemptCount >= 3) {
            // Lock out for 15 minutes
            let lockoutTime = new Date();
            lockoutTime.setMinutes(lockoutTime.getMinutes() + 15);
            localStorage.setItem('otp_lockout_until', lockoutTime.toISOString());
            showLockout();
            startLockoutTimer(lockoutTime);
        }
    @endif

    // Resend OTP functionality
    resendBtn.addEventListener("click", function(e) {
        e.preventDefault();
        // Reset attempts when resending
        localStorage.removeItem('otp_attempts');
        localStorage.removeItem('otp_lockout_until');
        
        // Make AJAX call to resend OTP
        fetch("{{ route('resend.otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to get new timer
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: redirect to forgot password page
            window.location.href = "{{ route('forgot.submit') }}";
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
            if (timeLeft <= 60000) { // Less than 1 minute
                timerElement.className = "text-danger mb-0";
            } else if (timeLeft <= 120000) { // Less than 2 minutes
                timerElement.className = "text-warning mb-0";
            }
        }, 1000);
    }

    function handleExpiry() {
        timerElement.textContent = "00:00";
        timerElement.className = "text-danger mb-0";
        expiredMessage.classList.remove("d-none");
        disableForm();
        resendContainer.classList.remove("d-none");
    }

    function showLockout() {
        lockoutMessage.classList.remove("d-none");
        timerContainer.classList.add("d-none");
        disableForm();
    }

    function startLockoutTimer(lockoutTime) {
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const lockout = new Date(lockoutTime).getTime();
            const timeLeft = lockout - now;

            if (timeLeft <= 0) {
                clearInterval(timer);
                localStorage.removeItem('otp_lockout_until');
                localStorage.removeItem('otp_attempts');
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
        inputs.forEach(input => input.disabled = true);
        verifyBtn.disabled = true;
    }

    function isExpired() {
        if (!expiryTime) return false;
        return new Date() > new Date(expiryTime);
    }

    function isLockedOut() {
        if (!lockoutUntil) return false;
        return new Date() < new Date(lockoutUntil);
    }
});
</script>

@endsection