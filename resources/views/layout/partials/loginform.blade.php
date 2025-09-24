@extends('layout.loginpage')


@section('content')
<!-- Login Start -->
@include('layout.partials.message')
<!-- @if(session('error'))
    <div id="floatingAlert" class="floating-attempt">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    </div>
@endif -->

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
            Login
          </h2>
        </div>



        <form action="{{ route('login.post') }}" method="post" id="loginForm" novalidate>
          @csrf

          <div class="form-floating mb-3">
            <input class="form-control rounded-3" name="email_address" id="inputEmail" type="email" placeholder="name@example.com" required style="background: #2d3748; border: none; color: #e2e8f0;" />
            <label for="inputEmail" style="color: #a0aec0;">Email address</label>
          </div>

          <div class="form-floating mb-3 position-relative">
            <input class="form-control rounded-3" name="password" id="inputPassword" type="password" placeholder="Password" required style="background: #2d3748; border: none; color: #e2e8f0;" />
            <label for="inputPassword" style="color: #a0aec0;">Password</label>

            <button type="button" class="btn btn-sm btn-link position-absolute top-50 end-0 translate-middle-y me-3 text-white" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility" style="z-index: 10;">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="remember" id="exampleCheck1" />
            <label class="form-check-label" for="exampleCheck1" style="color: #cbd5e1;">Remember me</label>
          </div>

          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-3">
            <a class="custom-teal-link text-decoration-none small" href="{{ route('forgot') }}">Forgot Password?</a>
            <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold w-100 w-md-auto" style="box-shadow: 0 4px 12px rgb(29 211 176 / 0.6); color: #1f2937;">
              Log In
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
  });
</script>
@endpush