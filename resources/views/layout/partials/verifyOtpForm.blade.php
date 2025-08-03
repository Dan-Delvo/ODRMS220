<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #23272e">
    <div class="p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px " >
        <div class="container text-white text-center">
            <h4>Verify Your Email</h4>
            <p>Please enter the verification code we sent to your gmail</p>
            
            <!-- Alert container for AJAX messages -->
            <div id="alert-container"></div>
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <form action="{{route('account.store')}}" method="POST">
            @csrf
            <!-- Hidden inputs -->
            <input type="hidden" name="email_address" value="{{ $email }}">
            <input type="hidden" name="username" value="{{ $username }}">
            <input type="hidden" name="password" value="{{ $password }}">
            
            <!-- OTP INPUT -->
            <div class="d-flex justify-content-center align-items-center p-3 gap-1" >
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
            
            <div class="text-center mt-3 d-flex justify-content-between align-items-start">

                <div class="d-flex flex-column align-items-center">
                    <button type="button" id="send-again" class="btn btn-primary disabled" onclick="sendAgainAjax()"
                    style="
                        color: white;
                        text-decoration: none;
                        border-radius: 4px;
                        pointer-events: none;
                        opacity: 0.6;
                        outline: none;
                        box-shadow: none;
                        border: none;
                    ">
                    <span id="send-again-text">Send Again</span>
                    <span id="send-again-spinner" class="d-none">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        Sending...
                    </span>
                    </button>
                    <span id="countdown" class="text-white mt-1">Loading...</span>
                </div>
                
                <button type="submit" class="btn text-white" style="background-color: #1dd3b0;">Verify</button>

            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(".otp-input");
    
    // Get initial timer values from server
    let countdownDuration = {{ session('durationInSeconds', 300) }};
    let startTime = new Date("{{ \Carbon\Carbon::parse(session('countdown_start', now()))->toIso8601String() }}").getTime();
    let endTime = startTime + countdownDuration * 1000;
    const timerDisplay = document.getElementById("countdown");
    const sendAgainBtn = document.getElementById("send-again");
    
    // AJAX function to send OTP again
    window.sendAgainAjax = function() {
        // Get the hidden input values
        const email = document.querySelector('input[name="email_address"]').value;
        const username = document.querySelector('input[name="username"]').value;
        const password = document.querySelector('input[name="password"]').value;
        
        // Show loading state
        document.getElementById("send-again-text").classList.add("d-none");
        document.getElementById("send-again-spinner").classList.remove("d-none");
        sendAgainBtn.disabled = true;
        
        // Make AJAX request
        fetch("{{ route('account.resend') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: email,
                username: username,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading state
            document.getElementById("send-again-text").classList.remove("d-none");
            document.getElementById("send-again-spinner").classList.add("d-none");
            
            if (data.success) {
                // Show success message
                showAlert('success', data.message);
                
                // Update timer with new values from server
                countdownDuration = data.durationInSeconds;
                startTime = new Date(data.countdown_start).getTime();
                endTime = startTime + countdownDuration * 1000;
                
                // Disable button and restart timer
                sendAgainBtn.classList.add("disabled");
                sendAgainBtn.style.pointerEvents = "none";
                sendAgainBtn.style.opacity = "0.6";
                sendAgainBtn.disabled = true;
                
                // Clear OTP inputs
                inputs.forEach(input => input.value = '');
                inputs[0].focus();
                
                // Restart timer
                updateTimer();
            } else {
                // Show error message
                showAlert('danger', data.message || 'Failed to send verification code. Please try again.');
                sendAgainBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Hide loading state
            document.getElementById("send-again-text").classList.remove("d-none");
            document.getElementById("send-again-spinner").classList.add("d-none");
            sendAgainBtn.disabled = false;
            
            showAlert('danger', 'An error occurred. Please try again.');
        });
    };
    
    // Function to show alert messages
    function showAlert(type, message) {
        const alertContainer = document.getElementById("alert-container");
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="this.parentElement.remove()"></button>
            </div>
        `;
        alertContainer.innerHTML = alertHtml;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }

    function updateTimer() {
        const now = new Date().getTime();
        const remaining = endTime - now;

        if (remaining <= 0) {
            timerDisplay.textContent = "Expired!";
            sendAgainBtn.classList.remove("disabled");
            sendAgainBtn.style.pointerEvents = "auto";
            sendAgainBtn.style.opacity = "1";
            sendAgainBtn.disabled = false;
        } else {
            const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            setTimeout(updateTimer, 1000);
        }
    }
    updateTimer();

    // Auto-move on input and other OTP input handlers
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
});
</script>