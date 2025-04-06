                       <div class="row justify-content-center d-flex justify-content-center align-items-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5" style="background-color: #23272E;">
                                    <div class="card-header"><h3 class="text-center text-warning font-weight-light my-4">Find Your Account</h3></div>
                                    <div class="card-body">
                                        <form action="{{ route('forgot.submit') }}" method="POST">
                                        @csrf
                                        <div class="small mb-3 text-light">Enter your email address and we will send you a link to reset your password.</div>
                                        <form>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name = "variable" style="color: black"/>
                                                <label for="inputEmail">Email address</label>
                                            </div>
                                            <div class="container d-flex justify-content-end gap-2" >
                                                <a href="{{ route('login') }}" class="btn btn-outline-light">Cancel</a>
                                                <button type="submit" class="btn btn-outline-warning">Search</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a class="text-white" href="{{ route('student.create') }}">Need an account? Sign up!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
