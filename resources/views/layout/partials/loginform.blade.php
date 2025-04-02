<!--Login Start-->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light">
                <!-- Dark Header -->
                <div class="card-header bg-dark text-white text-center">
                    <h3 class="my-4 fw-bold">Login</h3>
                </div>
                <div class="card-body">
                    @include('layout.partials.message')

                    <form action="" method="post" id="loginForm">
                        {{ csrf_field() }} <!-- para sa forms -->

                        <div class="form-floating mb-3">
                            <input class="form-control rounded-pill" name="email" id="inputEmail" type="email" placeholder="name@example.com" />
                            <label for="inputEmail">Email address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control rounded-pill" name="password" id="inputPassword" type="password" placeholder="Password" />
                            <label for="inputPassword">Password</label>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="remember" id="exampleCheck1">
                            <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="password.html">Forgot Password?</a>
                            <button type="submit" class="btn btn-primary rounded-pill btn-lg">Log In</button>
                        </div>
                        <!-- Hidden input for FCM token -->
                        <input type="hidden" name="fcm_token" id="fcmToken">
                    </form>
                </div>
                <!-- Footer -->
                <div class="card-footer text-center py-3 bg-dark">
                    <div class="small"><a href="{{ route('student.create') }}" class="text-light">Need an account? Sign up!</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Login End-->
