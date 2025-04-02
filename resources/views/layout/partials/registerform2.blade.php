<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5" style="min-height: 400px;">
            <div class="card-header bg-dark text-light">
                <h3 class="text-center font-weight-light my-4">Create Account</h3>
            </div>
            <div class="card-body bg-light">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('account.store') }}" method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputEmail" type="email" name="email_address" placeholder="name@example.com" />
                        <label for="inputEmail">Email Address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputUsername" type="text" name="username" placeholder="Enter your username" />
                        <label for="inputUsername">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" />
                        <label for="inputPassword">Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder="Confirm password" />
                        <label for="inputPasswordConfirm">Confirm Password</label>
                    </div>
                    <div class="mt-4 mb-0">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-block" type="submit">Create Account</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer text-center py-3 mt-auto">
                <div class="small"><a href="{{ route('login') }}">Have an account? Go to login</a></div>
            </div>
        </div>
    </div>
</div>
