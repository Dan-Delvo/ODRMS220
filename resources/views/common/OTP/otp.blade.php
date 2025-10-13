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
    const expiredMessage = document.getElementById("expired-message");

    // Get expiry time from session (passed from backend)
    let expiryTime = @json(session('expiry'));

    // Start the countdown timer
    if (expiryTime) {
        startCountdown(new Date(expiryTime));
    }

    // OTP input functionality - Auto-advance to next input
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

        // Only allow numeric input
        input.addEventListener("keypress", (e) => {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Handle paste - auto-fill all inputs
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

    // Form submission handler - prevent if expired
    form.addEventListener("submit", function(e) {
        if (isExpired()) {
            e.preventDefault();
            handleExpiry();
            return false;
        }
    });

    // Resend OTP functionality
    resendBtn.addEventListener("click", function(e) {
        e.preventDefault();
        
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

    function disableForm() {
        inputs.forEach(input => input.disabled = true);
        verifyBtn.disabled = true;
    }

    function isExpired() {
        if (!expiryTime) return false;
        return new Date() > new Date(expiryTime);
    }
});
</script>

@endsection