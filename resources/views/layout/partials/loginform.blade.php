<!--Login Start-->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 bg-white" style="box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.3);">
                <div class="row g-0"> <!-- Row inside the card for grid split -->
                    <div class="col-lg-6 p-4" style="background-color: #343a40; color: white; border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                        <div class="card-header border-0 bg-transparent">
                            <h3 class="text-center font-weight-light my-4 text-white">Login</h3>
                        </div>
                        <div class="card-body">
                            @include('layout.partials.message')

                            <form action="" method="post" id="loginForm">
                                {{ csrf_field() }} <!-- para sa forms -->
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="email" id="inputEmail" type="email" placeholder="name@example.com" />
                                    <label for="inputEmail">Email address</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Password" />
                                    <label for="inputPassword">Password</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" name="remember" id="exampleCheck1">
                                    <label class="form-check-label text-white" for="exampleCheck1">Remember Password</label>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                    <a class="small text-white-50" href="{{ route('forgot') }}">Forgot Password?</a>
                                    <button type="submit" class="btn btn-outline-warning">Log In</button>
                                </div>
                                <!-- Hidden input for FCM token -->
                                <input type="hidden" name="fcm_token" id="fcmToken">
                            </form>
                        </div>
                        <div class="card-footer text-center py-3 bg-transparent">
                            <div class="small">
                                <a href="{{ route('student.create') }}" class="text-white-50">Need an account? Sign up!</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                        <img src="{{ asset('/images/UbnhsBg.jpg.jpg') }}" alt="Login Image" class="img-fluid h-100 w-100" style="object-fit: cover; border-top-right-radius: .5rem; border-bottom-right-radius: .5rem;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Login End-->