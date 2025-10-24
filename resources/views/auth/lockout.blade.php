<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Locked - UBNHS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .lockout-container {
            background: #2d3748;
            border-radius: 1rem;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: white;
        }

        .lock-icon {
            font-size: 5rem;
            color: #ef4444;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .timer-display {
            background: #1f2937;
            border-radius: 0.75rem;
            padding: 2rem;
            margin: 2rem 0;
            border: 2px solid #ef4444;
        }

        .timer-number {
            font-size: 3.5rem;
            font-weight: bold;
            color: #1dd3b0;
            font-family: 'Courier New', monospace;
        }

        .timer-label {
            font-size: 0.9rem;
            color: #cbd5e1;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .warning-message {
            color: #fbbf24;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .info-message {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .lock-type-badge {
            display: inline-block;
            background: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            margin: 1rem 0;
            font-weight: 600;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #1f2937;
            border-radius: 4px;
            margin-top: 2rem;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #ef4444, #dc2626);
            border-radius: 4px;
            transition: width 1s linear;
            width: 0%;
        }

        @media (max-width: 576px) {
            .lockout-container {
                padding: 2rem 1.5rem;
            }
            .lock-icon {
                font-size: 3.5rem;
            }
            .timer-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="lockout-container">
        <div class="lock-icon">
            <i class="fas fa-lock"></i>
        </div>
        
        <h2 class="warning-message mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Account Temporarily Locked
        </h2>
        
        <p class="info-message">
            Your account has been locked due to multiple failed authentication attempts. 
            This is a security measure to protect your account.
        </p>

        <div class="timer-display">
            <div class="timer-number" id="timerDisplay">
                <span id="minutes">{{ str_pad(floor($remaining_seconds / 60), 2, '0', STR_PAD_LEFT) }}</span>:<span id="seconds">{{ str_pad($remaining_seconds % 60, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="timer-label">Time Remaining</div>
        </div>

        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progressBar"></div>
        </div>

        <p class="info-message mt-4 mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Please wait until the timer expires to try again.
        </p>
    </div>

    <script>
        // Get data from server
        let lockedUntil = {{ $locked_until }};
        const initialRemainingSeconds = {{ $remaining_seconds }};
        
        // Calculate the ACTUAL total lock time dynamically
        // This works for 15 min, 30 min, 1 hour, or any duration
        const totalLockTime = initialRemainingSeconds;
        
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const remaining = lockedUntil - now;
            
            if (remaining <= 0) {
                // Lockout expired, redirect to login
                window.location.href = '{{ route("login") }}';
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            
            // Update timer display
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            
            // Calculate progress dynamically based on actual total lock time
            // Progress represents how much time has ELAPSED (fills up over time)
            const timeElapsed = totalLockTime - remaining;
            const progress = (timeElapsed / totalLockTime) * 100;
            
            // Ensure progress is between 0 and 100
            const clampedProgress = Math.max(0, Math.min(100, progress));
            document.getElementById('progressBar').style.width = clampedProgress + '%';
        }
        
        // Update immediately
        updateTimer();
        
        // Update every second
        setInterval(updateTimer, 1000);

        // Prevent back button navigation
        window.history.pushState(null, '', window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, '', window.location.href);
        };
    </script>
</body>
</html>