@extends('layout.loginpage')

@section ('content')

<style>
    .card {
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .card:hover {
        border-color: #1dd3b0;
        box-shadow: 0 0 25px rgba(29, 211, 176, 0.4);
    }
</style>

<div class="d-flex justify-content-center align-items-center"
    style="min-height: 100vh; background: linear-gradient(135deg, #28313B, #23272E, #1B1F24);">
    @include('layout.partials.message')
    <div class="col-lg-5">
        <div class="card shadow-lg border-0 rounded-lg" style="background-color: #23272E;">
            <div class="card-header">
                <h3 class="text-center font-weight-light my-4" style="color: #1dd3b0">Find Your Account</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('forgot.submit') }}" method="POST">
                    @csrf
                    <div class="small mb-3 text-light">
                        Enter your email address and we will send you a link to reset your password.
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="variable" style="color: black" />
                        <label for="inputEmail">Email address</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-light" onclick="window.location.replace('{{ route('login') }}')">Go Back</button>
                        <button type="submit" class="btn btn-outline-custom">Search</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="small">
                    <a class="text-white" href="{{ route('student.create') }}">Need an account? Sign up!</a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection