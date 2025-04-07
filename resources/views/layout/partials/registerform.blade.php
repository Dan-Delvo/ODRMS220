<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light">
            <!-- Dark Header -->
            <div class="card-header bg-dark text-white text-center py-4">
                <h3 class="my-0 fw-bold">Create Account</h3>
                <p class="text-white-50">Fill in the details below to create your account</p>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('student.store') }}" method="POST">
                    @csrf

                    <!-- Name Fields (First and Last Name) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                                <input class="form-control rounded-pill" id="inputFirstName" type="text" name="FirstName" placeholder="Enter your first name" required />
                                <label for="inputFirstName">First Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control rounded-pill" id="inputLastName" type="text" name="LastName" placeholder="Enter your last name" required />
                                <label for="inputLastName">Last Name</label>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputMiddleName" type="text" name="MiddleName" placeholder="Enter your middle name" />
                        <label for="inputMiddleName">Middle Name (Optional)</label>
                    </div>

                    <!-- Suffix -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputSuffix" type="text" name="Suffix" placeholder="Enter your suffix" />
                        <label for="inputSuffix">Suffix (Optional)</label>
                    </div>

                    <!-- LRN -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputLRN" type="text" name="LRN" placeholder="Enter your LRN" required />
                        <label for="inputLRN">LRN (Learner's Reference Number)</label>
                    </div>

                    <!-- Grade Level -->
                    <div class="form-floating mb-3">
                        <select class="form-control rounded-pill" id="grade_level" name="Grade_level" required>
                            <option value="" disabled selected>Select Grade Level</option>
                            @foreach ($grade as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                        <label for="grade_level">Grade Level</label>
                    </div>

                    <!-- Student Status -->
                    <div class="form-floating mb-3">
                        <select class="form-control rounded-pill" id="inputStdStatus" name="Std_status" required>
                            <option value="" disabled selected>Select Status</option>
                            @foreach ($stat as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <label for="inputStdStatus">Student Status</label>
                    </div>

                    <!-- Last School Year Attended -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputLastSYAttended" type="text" name="Last_sy_attended" placeholder="Enter last school year attended" required />
                        <label for="inputLastSYAttended">Last School Year Attended</label>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 mb-0">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-block rounded-pill btn-lg px-4" type="submit">Next</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="card-footer text-center py-3 bg-dark">
                <div class="small">
                    <a href="login.html" class="text-light">Have an account? Go to login</a>
                </div>
            </div>
        </div>
    </div>
</div>
