@extends('layout.loginpage')

@section('content')
<!-- Login Start -->
@include('layout.partials.message')

<div class="container-fluid g-0">
  <div class="row g-0 min-vh-100">
    <!-- Left Side with Background Image (hidden on small screens) -->
    <div class="col-lg-9 d-none d-lg-block">
      <div
        style="height: 100vh; background: url('{{ asset('images/BG_UBNHS.jpg') }}') center center / cover no-repeat; filter: brightness(0.7);"
        aria-label="Background Image"></div>
    </div>

    <!-- Right Side (Login Form) -->
    <div class="col-lg-3 d-flex flex-column justify-content-center align-items-center px-4 py-5"
      style="background-color: #1f2937; color: white; min-height: 100vh; box-shadow: 0 8px 24px rgb(0 0 0 / 0.3);">
      <div class="w-100" style="max-width: 400px;">
        <!-- PWA Install Button -->
        <div class="text-center mb-3">
          <button id="installButton" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1" style="display: none; border-color: #1dd3b0; color: #1dd3b0; font-size: 0.85rem; transition: all 0.3s ease;">
            <i class="fas fa-download me-1"></i>Install App
          </button>
        </div>

        <div class="text-center mb-4">
          <h2 class="font-weight-bold" style="color: #1dd3b0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            Log in
          </h2>
        </div>

        <!-- Error Alert -->
        <div id="errorAlert" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
          <span id="errorMessage"></span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Lockout Alert -->
        <div id="lockoutAlert" class="alert alert-warning" role="alert" style="display: none;">
          <strong>Account Locked!</strong>
          <p id="lockoutMessage" class="mb-0"></p>
        </div>

        <form action="{{ route('login.post') }}" method="post" id="loginForm" novalidate>

          @csrf

          <div class="form-floating mb-3">
            <input class="form-control rounded-3" name="email_address" id="inputEmail" type="email"
                   placeholder="name@example.com" required
                   style="background: #2d3748; border: none; color: #e2e8f0;" />
            <label for="inputEmail" style="color: #a0aec0;">Email address</label>
          </div>

          <div class="form-floating mb-3 position-relative">
            <input class="form-control rounded-3" name="password" id="inputPassword" type="password"
                   placeholder="Password" required
                   style="background: #2d3748; border: none; color: #e2e8f0;" />
            <label for="inputPassword" style="color: #a0aec0;">Password</label>

            <button type="button" class="btn btn-sm btn-link position-absolute top-50 end-0 translate-middle-y me-3 text-white"
                    id="togglePassword" tabindex="-1" aria-label="Toggle password visibility" style="z-index: 10;">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>

          <div class="form-check d-flex justify-content-between mb-3">
            <div>
                <input type="checkbox" class="form-check-input" name="remember" id="exampleCheck1" />
                <label class="form-check-label" for="exampleCheck1" style="color: #cbd5e1;">Remember me</label>
            </div>
            <div>
                <a class="custom-teal-link text-decoration-none small" href="{{ route('forgot') }}">Forgot Password?</a>
            </div>
          </div>

          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-3">
            <button type="submit" id="loginButton" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold w-100 w-md-auto"
                    style="box-shadow: 0 4px 12px rgb(29 211 176 / 0.6); color: #1f2937;">
              <span id="loginButtonText">Log In</span>
              <span id="loginSpinner" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true" style="display: none;"></span>
            </button>
          </div>

          <input type="hidden" name="fcm_token" id="fcmToken" />
        </form>

        <div class="text-center mt-4 small">

        </div>
      </div>
    </div>
  </div>
</div>
<!-- Login End -->
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {

    // Toggle password
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('inputPassword');
    const eyeIcon = document.getElementById('eyeIcon');

    toggleBtn.addEventListener('click', function() {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      eyeIcon.classList.toggle('fa-eye');
      eyeIcon.classList.toggle('fa-eye-slash');
    });

    // PWA install
    let deferredPrompt;
    const installButton = document.getElementById('installButton');

    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      installButton.style.display = 'inline-block';
    });

    installButton.addEventListener('click', async () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        const {
          outcome
        } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        installButton.style.display = 'none';
      }
    });

    window.addEventListener('appinstalled', () => {
      installButton.style.display = 'none';
    });

    if (window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches) {
      installButton.style.display = 'none';
    }

    // ========================================
    // AJAX LOGIN HANDLING
    // ========================================
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const loginButtonText = document.getElementById('loginButtonText');
    const loginSpinner = document.getElementById('loginSpinner');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const lockoutAlert = document.getElementById('lockoutAlert');
    const lockoutMessage = document.getElementById('lockoutMessage');

    // Function to show error
    function showError(message) {
      errorMessage.textContent = message;
      errorAlert.style.display = 'block';
      errorAlert.classList.add('show');

      // Auto-hide after 5 seconds
      setTimeout(() => {
        hideError();
      }, 5000);
    }

    // Function to hide error
    function hideError() {
      errorAlert.classList.remove('show');
      setTimeout(() => {
        errorAlert.style.display = 'none';
      }, 150);
    }

    // Function to show lockout
    function showLockout(minutes, seconds) {
      const minuteText = minutes === 1 ? 'minute' : 'minutes';
      lockoutMessage.innerHTML = `Too many failed login attempts.<br>Please try again in <strong>${minutes} ${minuteText}</strong>.`;
      lockoutAlert.style.display = 'block';

      // Disable form
      loginButton.disabled = true;
      document.getElementById('inputEmail').disabled = true;
      document.getElementById('inputPassword').disabled = true;

      // Start countdown
      startLockoutCountdown(seconds);
    }

    // Countdown timer for lockout
    function startLockoutCountdown(totalSeconds) {
      let remaining = totalSeconds;

      const countdownInterval = setInterval(() => {
        remaining--;

        const minutes = Math.ceil(remaining / 60);
        const minuteText = minutes === 1 ? 'minute' : 'minutes';

        lockoutMessage.innerHTML = `Too many failed login attempts.<br>Please try again in <strong>${minutes} ${minuteText}</strong>.`;

        if (remaining <= 0) {
          clearInterval(countdownInterval);
          // Reload page to allow login again
          window.location.reload();
        }
      }, 1000);
    }

    // Function to set loading state
    function setLoading(isLoading) {
      if (isLoading) {
        loginButton.disabled = true;
        loginButtonText.textContent = 'Logging in...';
        loginSpinner.style.display = 'inline-block';
      } else {
        loginButton.disabled = false;
        loginButtonText.textContent = 'Log In';
        loginSpinner.style.display = 'none';
      }
    }

    // Handle form submission
    loginForm.addEventListener('submit', async function(e) {
      e.preventDefault();

      // Hide any existing errors
      hideError();
      lockoutAlert.style.display = 'none';

      // Set loading state
      setLoading(true);

      // Get form data
      const formData = new FormData(this);

      try {
        const response = await fetch(this.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
          // Success - show success message briefly then redirect
          loginButtonText.textContent = 'Success!';
          loginButton.classList.remove('btn-warning');
          loginButton.classList.add('btn-success');

          setTimeout(() => {
            window.location.href = data.redirect;
          }, 500);

        } else {
          // Failed login
          setLoading(false);

          if (data.locked) {
            // Account is locked
            showLockout(data.remaining_minutes, data.remaining_seconds);
          } else {
            // Show error message
            showError(data.message);
          }
        }

      } catch (error) {
        setLoading(false);
        console.error('Login error:', error);
        showError('An error occurred. Please try again.');
      }
    });

  });
</script>
@endpush
