
<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #23272e">
        <div class="p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px " >
            <div class="container text-white text-center">
                <h4>Verify Your Email</h4>
                <p>Please enter the verification code we sent to your gmail</p>
            </div>

            <form action="{{route('account.store')}}"  method="POST">
                @csrf
                <!-- Deubugging if it passes value -->
                <!-- @php
                    // dd($email, $username, $password);
                @endphp -->
                <!-- Passing value -->
                <input type="hidden" name="email_address" value="{{ $email }}">
                <input type="hidden" name="username" value="{{ $username }}">
                <input type="hidden" name="password" value="{{ $password }}">
                <!-- OTP INPUT -->
                <div class="d-flex justify-content-center align-items-center p-3 gap-1" >
                    <input type="text" name = "first" class="otp-input form-control text-center" maxlength="1"
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "second" class="otp-input form-control text-center" maxlength="1"
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "third" class="otp-input form-control text-center" maxlength="1"
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "fourth" class="otp-input form-control text-center" maxlength="1"
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "fifth" class="otp-input form-control text-center" maxlength="1"
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "sixth" class="otp-input form-control text-center" maxlength="1"
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">

                </div>
                <div class="text-center mt-3 d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">Verify</button>
                    <p><span id="countdown" class ="text-white">Loading...</span></p>
                </div>
            </form>

        </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll(".otp-input");

        const countdownDuration = {{ session('durationInSeconds', 300) }};
        const startTime = new Date("{{ \Carbon\Carbon::parse(session('countdown_start', now()))->toIso8601String() }}").getTime();
        const endTime = startTime + countdownDuration * 1000;
        const timerDisplay = document.getElementById("countdown");

        function updateTimer() {
            const now = new Date().getTime();
            const remaining = endTime - now;

            if (remaining <= 0) {
                timerDisplay.textContent = "Expired!";
            } else {
                const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
                timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                setTimeout(updateTimer, 1000);
            }
        }
        updateTimer();

        //  const expirationTime = new Date("{{ session('expiresAt') ?? '' }}").getTime();
        // if (!isNaN(expirationTime)) {
        //     function updateCountdown() {
        //         const now = new Date().getTime();
        //         const distance = expirationTime - now;

        //         if (distance <= 0) {
        //             document.getElementById("countdown").innerText = "Expired";
        //             clearInterval(timerInterval);
        //             return;
        //         }

        //         const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        //         const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        //         document.getElementById("countdown").innerText =
        //             `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        //     }

        //     updateCountdown();
        //     const timerInterval = setInterval(updateCountdown, 1000);
        // }

        // Auto-move on input
        inputs.forEach((input, index) => {
            input.addEventListener("input", (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus(); // Move to next input
                }
            });

            // Move back on backspace
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && index > 0 && e.target.value === "") {
                    inputs[index - 1].focus();
                }
            });

            // Restrict to numbers only
            input.addEventListener("keypress", (e) => {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Handle pasting full OTP
            input.addEventListener("paste", (e) => {
                e.preventDefault(); // Stop default paste behavior
                let pastedData = e.clipboardData.getData("text"); // Get clipboard text
                let digits = pastedData.replace(/\D/g, "").split("").slice(0, inputs.length); // Extract digits only

                // Fill each input with pasted digits
                inputs.forEach((inp, i) => {
                    inp.value = digits[i] || ""; // Fill available digits
                });

                // Focus last filled input
                if (digits.length > 0) {
                    let lastIndex = Math.min(digits.length, inputs.length) - 1;
                    inputs[lastIndex].focus();
                }
            });
        });
    });
</script>