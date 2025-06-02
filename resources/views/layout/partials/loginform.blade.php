<!--Login Start-->
<div class="container-fluid g-0">
  <div class="row g-0 min-vh-100">
    <!-- Left Side with Background Image (hidden on small screens) -->
    <div class="col-lg-9 d-none d-lg-block">
      <div
        style="
          height: 100vh;
          background: url('{{ asset('images/BG_UBNHS.jpg') }}') center center / cover no-repeat;
          filter: brightness(0.7);
        "
        aria-label="Background Image"
      ></div>
    </div>

    <!-- Right Side (Login Form) -->
    <div
      class="col-lg-3 d-flex flex-column justify-content-center align-items-center px-4 py-5"
      style="
        background-color: #1f2937;
        color: white;
        min-height: 100vh;
        box-shadow: 0 8px 24px rgb(0 0 0 / 0.3);
      "
    >
      <div class="w-100" style="max-width: 400px;">
        <div class="text-center mb-4">
          <h2
            class="font-weight-bold"
            style="color: #1dd3b0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;"
          >
            Login
          </h2>
        </div>

        @include('layout.partials.message')

        <form action="" method="post" id="loginForm" novalidate>
          {{ csrf_field() }}

          <div class="form-floating mb-3">
            <input
              class="form-control rounded-3"
              name="email"
              id="inputEmail"
              type="email"
              placeholder="name@example.com"
              required
              style="background: #2d3748; border: none; color: #e2e8f0;"
            />
            <label for="inputEmail" style="color: #a0aec0;">Email address</label>
          </div>

          <div class="form-floating mb-3">
            <input
              class="form-control rounded-3"
              name="password"
              id="inputPassword"
              type="password"
              placeholder="Password"
              required
              style="background: #2d3748; border: none; color: #e2e8f0;"
            />
            <label for="inputPassword" style="color: #a0aec0;">Password</label>
          </div>

          <div class="form-check mb-3">
            <input
              type="checkbox"
              class="form-check-input"
              name="remember"
              id="exampleCheck1"
            />
            <label class="form-check-label" for="exampleCheck1" style="color: #cbd5e1;">
              Remember me
            </label>
          </div>

          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-3">
            <a class="custom-teal-link text-decoration-none small" href="password.html">Forgot Password?</a>

            <button
              type="submit"
              class="btn btn-warning rounded-pill px-4 py-2 fw-semibold w-100 w-md-auto"
              style="box-shadow: 0 4px 12px rgb(29 211 176 / 0.6); color: #1f2937;"
            >
              Log In
            </button>
          </div>

          <input type="hidden" name="fcm_token" id="fcmToken" />
        </form>

        <div class="text-center mt-4 small">
          <span style="color: #94a3b8;">Don't have an account? </span>
          <a href="{{ route('student.create') }}" class="custom-teal-link fw-semibold">Sign up</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!--Login End-->

<style>
  /* Responsiveness Enhancements */
  @media (max-width: 991.98px) {
    .col-lg-3 {
      max-width: 100% !important;
      box-shadow: none !important;
      padding: 2rem 1.5rem;
    }

    .btn.btn-warning {
      width: 100%;
    }

    h2.font-weight-bold {
      font-size: 1.75rem;
    }
  }

  @media (max-width: 576px) {
    .col-lg-3 {
      padding: 1.5rem 1rem;
    }

    .form-floating label {
      font-size: 0.85rem;
    }

    .custom-teal-link {
      font-size: 0.85rem;
    }
  }

  .text-warning {
    color: #1dd3b0 !important;
  }

  .btn-warning {
    background-color: #1dd3b0 !important;
    border-color: #1dd3b0 !important;
  }

  .btn-warning:hover,
  .btn-warning:focus {
    background-color: #14b59c !important;
    border-color: #14b59c !important;
  }

  .custom-teal-link {
    color: #1dd3b0;
  }

  .custom-teal-link:hover {
    color: #14b1a2;
    text-decoration: underline;
  }
</style>
