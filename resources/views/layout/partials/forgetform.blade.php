<!-- @if (session('error'))
<div id="errorAlert" class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow-lg" role="alert" style="z-index: 1050; width: 20%;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif -->


@include('layout.partials.message')
<div class="row justify-content-center d-flex justify-content-center align-items-center">
    <div class="col-lg-5">
        <div class="card shadow-lg border-0 rounded-lg mt-5" style="background-color: #23272E;">
            <div class="card-header">
                <h3 class="text-center font-weight-light my-4" style="color: #1dd3b0">Find Your Account</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('forgot.submit') }}" method="POST">
                    @csrf
                    <div class="small mb-3 text-light">Enter your email address and we will send you a link to reset your password.</div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="variable" style="color: black" />
                        <label for="inputEmail">Email address</label>
                    </div>
                    <div class="container d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-light" onclick="window.location.replace('{{ route('login') }}')">Go Back</button>
                        <button type="submit" class="btn btn-outline-custom">
                            Search
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="small"><a class="text-white" href="{{ route('student.create') }}">Need an account? Sign up!</a></div>
            </div>
        </div>
    </div>
</div>



