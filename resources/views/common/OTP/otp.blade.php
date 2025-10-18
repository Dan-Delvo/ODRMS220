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

                <div class="d-flex ms-auto gap-2">
                    <!-- ✅ Back Button -->
                    <a href="{{ route('otp.back') }}" class="btn btn-outline-light">Back</a>

                    <!-- ✅ Verify Button -->
                    <button type="submit" id="verify-btn" class="btn btn-outline-custom">Verify</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    (function() {
        // ✅ Use IIFE to prevent multiple executions
        const inputs = document.querySelectorAll(".otp-input");
        const form = document.getElementById("otp-form");
        const verifyBtn = document.getElementById("verify-btn");
        const resendBtn = document.getElementById("resend-btn");
        const resendContainer = document.getElementById("resend-container");
        const timerElement = document.getElementById("timer");
        const timerContainer = document.getElementById("timer-container");
        const expiredMessage = document.getElementById("expired-message");

        // 🕒 expiry time from Laravel session
        let expiryTime = @json(session('expiry'));
        let countdownInterval;
        let resendLocked = false;

        // ✅ Immediately show the correct remaining time (avoid 5:00 flicker)
        if (expiryTime) {
            const now = new Date().getTime();
            const expiry = new Date(expiryTime).getTime();
            const timeLeft = expiry - now;

            if (timeLeft > 0) {
                const minutes = Math.floor(timeLeft / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                timerElement.textContent = `${minutes.toString().padStart(2, "0")}:${seconds
                .toString()
                .padStart(2, "0")}`;
            } else {
                timerElement.textContent = "00:00";
            }

            startCountdown(new Date(expiryTime));
        }

        // 🧩 OTP input functionality
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
                if (!/[0-9]/.test(e.key)) e.preventDefault();
            });

            input.addEventListener("paste", (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData("text");
                const digits = pastedData.replace(/\D/g, "").split("").slice(0, inputs.length);

                inputs.forEach((inp, i) => (inp.value = digits[i] || ""));
                if (digits.length > 0) {
                    inputs[Math.min(digits.length, inputs.length) - 1].focus();
                }
            });
        });

        // 🧩 Prevent submitting expired OTP
        form.addEventListener("submit", function(e) {
            if (isExpired()) {
                e.preventDefault();
                handleExpiry();
            }
        });

        // 🧩 FIXED resend OTP (no duplicate listeners)
        resendBtn.addEventListener("click", async function(e) {
            e.preventDefault();

            if (resendLocked) return; // 🔒 prevent double trigger
            resendLocked = true;

            resendBtn.textContent = "Sending...";
            resendBtn.style.pointerEvents = "none";
            resendBtn.style.opacity = "0.6";

            try {
                const response = await fetch("{{ route('resend.otp') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector("[name='csrf-token']")?.getAttribute("content") ||
                            "{{ csrf_token() }}",
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // ✅ Hide expired message
                    expiredMessage.classList.add("d-none");

                    // ✅ Show timer container again
                    timerContainer.classList.remove("d-none");

                    // ✅ Hide resend container
                    resendContainer.classList.add("d-none");

                    resendBtn.textContent = "Resend OTP";
                    resendBtn.style.color = "#1dd3b0";
                    resendBtn.style.pointerEvents = "auto";
                    resendBtn.style.opacity = "1";

                    // ✅ Update expiry time dynamically
                    expiryTime = new Date(data.expiry);
                    startCountdown(expiryTime);

                    // ✅ Enable OTP inputs again and clear them
                    inputs.forEach((input) => {
                        input.disabled = false;
                        input.value = "";
                    });
                    verifyBtn.disabled = false;

                    // Focus on first input
                    inputs[0].focus();

                    resendLocked = false;
                } else {
                    resendBtn.textContent = "Failed. Try again";
                    resendBtn.style.color = "red";
                    setTimeout(resetResendButton, 4000);
                }
            } catch (error) {
                console.error("Resend error:", error);
                resendBtn.textContent = "Error!";
                resendBtn.style.color = "red";
                setTimeout(resetResendButton, 4000);
            }
        });

        function resetResendButton() {
            resendBtn.textContent = "Resend OTP";
            resendBtn.style.color = "#1dd3b0";
            resendBtn.style.pointerEvents = "auto";
            resendBtn.style.opacity = "1";
            resendLocked = false;
        }

        // 🧩 Countdown logic
        function startCountdown(expiryTime) {
            if (countdownInterval) clearInterval(countdownInterval);

            countdownInterval = setInterval(() => {
                const now = new Date().getTime();
                const expiry = new Date(expiryTime).getTime();
                const timeLeft = expiry - now;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    handleExpiry();
                    return;
                }

                const minutes = Math.floor(timeLeft / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                timerElement.textContent = `${minutes
                .toString()
                .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

                // 🔴 change color depending on time left
                if (timeLeft <= 60000) {
                    timerElement.className = "text-danger mb-0";
                } else if (timeLeft <= 120000) {
                    timerElement.className = "text-warning mb-0";
                } else {
                    timerElement.className = "text-success mb-0";
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
            inputs.forEach((input) => (input.disabled = true));
            verifyBtn.disabled = true;
        }

        function isExpired() {
            if (!expiryTime) return false;
            return new Date() > new Date(expiryTime);
        }
    })();
</script>

@endsection